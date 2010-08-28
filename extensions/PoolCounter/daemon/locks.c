#define _XOPEN_SOURCE 500
#include <string.h>
#include <stdlib.h>
#include <stdio.h>
#include "locks.h"
#include "hash.h"
#include "client_data.h"

void init_lock(struct locks* l) {
	l->state = UNLOCKED;
}

void finish_lock(struct locks* l) {
	if (l->state != UNLOCKED) {
		remove_client_lock(l, 0);
	}
}

static struct hashtable* primary_hashtable;

/* These defines are the same in memcached */
#define hashsize(n) ((uint32_t)1<<(n))
#define hashmask(n) (hashsize(n)-1)

#define DOUBLE_LLIST_INIT(this) do { (this).prev = (this).next = &(this); } while (0)
#define DOUBLE_LLIST_DEL(this) do { (this)->prev->next = (this)->next; (this)->next->prev = (this)->prev; } while (0)
#define DOUBLE_LLIST_ADD(parent,child) do { (child)->prev = (parent)->prev; (child)->next = (child)->prev->next /* parent */; (parent)->prev->next = (child); (parent)->prev = (child); } while(0);

/* Converts a numeric text into an unsigned integer.
 * Returns 0 if it's a NULL pointer or not a natural.
 */
unsigned atou(char const* astext)  {
	int num = 0;
	if (!astext) return 0;
	
	while ( *astext ) {
		if ( *astext < '0' ) return 0;
		if ( *astext > '9' ) return 0;
		num = num * 10 + *astext - '0';
		astext++;
	}
	return num;
}

char* process_line(struct client_data* cli_data, char* line, int line_len) {
	struct locks* l = &cli_data->client_locks;
	
	if ( !strncmp( line, "ACQ4ME ", 7 ) || !strncmp( line, "ACQ4ANY ", 8 ) ) {
		if ( l->state != UNLOCKED ) {
			return "LOCK_HELD\n";
		}
		
		int for_anyone = line[6] != ' ';
		
		char* key = strtok( line + 7 + for_anyone, " " );
		unsigned workers = atou( strtok(NULL, " ") );
		unsigned maxqueue = atou( strtok(NULL, " ") );
		unsigned timeout = atou( strtok(NULL, " ") );
		
		if ( !key || !workers || !maxqueue || !timeout ) {
			return "ERROR BAD_SYNTAX\n";
		}
		
		uint32_t hash_value = hash( key, strlen( key ), 0 );
		struct PoolCounter* pCounter;
		pCounter = hashtable_find( primary_hashtable, hash_value, key );
		if ( !pCounter ) {
			pCounter = malloc( sizeof( *pCounter ) );
			if ( !pCounter ) {
				fprintf(stderr, "Out of memory\n");
				return "ERROR OUT_OF_MEMORY\n";
			}
			pCounter->htentry.key = strdup( key );
			pCounter->htentry.key_hash = hash_value;
			pCounter->count = 0;
			pCounter->processing = 0;
			
			DOUBLE_LLIST_INIT( pCounter->working );
			DOUBLE_LLIST_INIT( pCounter->for_them );
			DOUBLE_LLIST_INIT( pCounter->for_anyone );
			
			hashtable_insert( primary_hashtable, (struct hashtable_entry *) pCounter );
		}
		
		if ( pCounter->count >= maxqueue )
			return "QUEUE_FULL\n";
		
		l->parent = pCounter;
		pCounter->count++;
		
		if ( pCounter->processing < workers ) {
			l->state = PROCESSING;
			pCounter->processing++;
			DOUBLE_LLIST_ADD( &pCounter->working, &l->siblings );
			return "LOCKED\n";
		} else {
			struct timeval wait_time;
			if ( for_anyone ) {
				l->state = WAIT_ANY;
				DOUBLE_LLIST_ADD( &pCounter->for_anyone, &l->siblings );
			} else {
				l->state = WAITING;
				DOUBLE_LLIST_ADD( &pCounter->for_them, &l->siblings );
			}
			
			wait_time.tv_sec = timeout;
			wait_time.tv_usec = 0;

			event_add( &cli_data->ev, &wait_time );
			return NULL;
		}
	} else if ( !strncmp(line, "RELEASE", 7) ) {
		if ( l->state == UNLOCKED ) {
			return "NOT_LOCKED\n";
		} else {
			remove_client_lock( l, 1 );
			return "RELEASED\n";
		}
	} else {
		return "ERROR BAD_COMMAND\n";
	}
}

void process_timeout(struct locks* l) {
	if ( ( l->state == WAIT_ANY ) || ( l->state == WAITING ) ) {
		send_client( l, "TIMEOUT\n" );
		remove_client_lock( l, 0 );
	}
}

void remove_client_lock(struct locks* l, int wakeup_anyones) {
	DOUBLE_LLIST_DEL(&l->siblings);
	
	if ( wakeup_anyones ) {
		while ( l->parent->for_anyone.next != &l->parent->for_anyone ) {
			send_client( (void*)l->parent->for_anyone.next, "DONE\n" );
			remove_client_lock( (void*)l->parent->for_anyone.next, 0 );
		}
	}
	
	if ( l->state == PROCESSING ) {
		/* One slot freed, wake up another worker */
		
		/* Give priority to those which need to do it themselves, since 
		 * the anyones will benefit from it, too.
		 * TODO: Prefer the first anyone if it's much older.
		 */
		struct locks* new_owner = NULL;
		if ( l->parent->for_them.next != &l->parent->for_them ) {
			/* The oldest waiting worker will be on next */
			new_owner = (struct locks*) l->parent->for_them.next;
		} else if ( l->parent->for_anyone.next != &l->parent->for_anyone ) {
			new_owner = (struct locks*) l->parent->for_anyone.next;
		}
		
		if ( new_owner ) {
			DOUBLE_LLIST_DEL( &new_owner->siblings );
			DOUBLE_LLIST_ADD( &l->parent->working, &new_owner->siblings );
			send_client( new_owner, "LOCKED\n" );
			new_owner->state = PROCESSING;
		} else {
			l->parent->processing--;			
		}
	}
	
	l->state = UNLOCKED;
	l->parent->count--;
	if ( !l->parent->count ) {
		hashtable_remove( l->parent->htentry.parent_hashtable, &l->parent->htentry );
		free( l->parent->htentry.key );
		free( l->parent );
	}
}

/* The  code below is loosely based in those of memcached assoc.c */
struct hashtable {
	unsigned int hashpower;
	uint32_t items;
	struct hashtable* old_hashtable;
	struct double_linked_list hashentries[1];
};

void hashtable_init() {
	primary_hashtable = hashtable_create(16);
	if (! primary_hashtable) {
        fprintf( stderr, "Failed to init hashtable.\n" );
        exit( EXIT_FAILURE );
    }
}

struct hashtable* hashtable_create(int hashpower) {
    struct hashtable* new_hashtable;
    new_hashtable = calloc( hashsize( hashpower ) + ( sizeof( struct hashtable ) - 1 ) / 
		sizeof( new_hashtable->hashentries[0] ), sizeof( new_hashtable->hashentries[0] ) );

    if ( !new_hashtable )
		return NULL;
    
    new_hashtable->hashpower = hashpower;
    if ( new_hashtable->old_hashtable != NULL ) {
		int i; /* Zeroes are not NULL here... */
		new_hashtable->old_hashtable = NULL;
		for ( i=0; i < hashsize( hashpower ); i++ ) {
			new_hashtable->hashentries[i].prev = new_hashtable->hashentries[i].next = NULL;
		}
	}
	return new_hashtable;
}

/**
 * Find an entry with the given key in the hash table.
 * NULL if not found.
 */
void* hashtable_find(struct hashtable* ht, uint32_t hash_value, const char* key) {
    struct hashtable_entry *begin, *cur;

    begin = (struct hashtable_entry*) &ht->hashentries[hash_value & hashmask(ht->hashpower)];
    if (!begin->hashtable_siblings.next) return NULL; /* Empty bucket */

	for (cur = (struct hashtable_entry*) begin->hashtable_siblings.next; cur != begin;
		cur = (struct hashtable_entry*)cur->hashtable_siblings.next) {

		if ( ( cur->key_hash == hash_value ) && ( !strcmp( key, cur->key ) ) ) {
			return cur;
		}
	}
	
	if ( ht->old_hashtable ) {
		if ( !ht->old_hashtable->items ) {
			/* Empty hash table */
			free(ht->old_hashtable);
			ht->old_hashtable = NULL;
			return NULL; 
		}
		
		return hashtable_find( ht->old_hashtable, hash_value, key );
	}
	return NULL;
}

/**
 * Insert into the hash table an item known not to exist there.
 */
void hashtable_insert(struct hashtable* ht, struct hashtable_entry* htentry) {
	struct double_linked_list* begin;
	
	if (! ht->old_hashtable && ht->items >= (hashsize( ht->hashpower ) * 3) / 2) {
		/* Same growing condition as in memcached */
		struct hashtable* new_ht;
		new_ht = hashtable_create( ht->hashpower + 1 );
		if ( new_ht ) {
			new_ht->old_hashtable = ht;
			primary_hashtable = new_ht;
			ht = new_ht;
		}
	}
	
	begin = &ht->hashentries[ htentry->key_hash & hashmask( ht->hashpower ) ];
	if ( !begin->next ) { DOUBLE_LLIST_INIT( *begin ); }
	DOUBLE_LLIST_ADD( begin, &htentry->hashtable_siblings );
	htentry->parent_hashtable = ht;
	ht->items++;
}

/**
 * Remove this entry from this hash table.
 * Freeing the entry is the caller's responsability.
 */
void hashtable_remove(struct hashtable* ht, struct hashtable_entry* htentry) {
	DOUBLE_LLIST_DEL( &htentry->hashtable_siblings );
	ht->items--;
}
