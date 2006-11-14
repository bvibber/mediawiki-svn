/* @(#) $Id$ */
/* This source code is in the public domain. */
/* 
 * Willow: Lightweight HTTP reverse-proxy.
 * wcache: entity caching.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <sys/types.h>
#include <sys/stat.h>

#include <cstring>
#include <cerrno>
#include <cstdlib>
#include <cmath>
#include <strings.h>
#include <limits.h>
#include <cassert>
#include <unistd.h>
#include <fcntl.h>
#include <event.h>

#include "wcache.h"
#include "wlog.h"
#include "wconfig.h"
#include "willow.h"
#include "format.h"

#define CACHEDIR "__objects__"

static int cache_next_id(void);
static void run_expiry(int, short, void*);
static void wcache_evict(struct cache_object *);
static void cache_getstate(struct cache_state *);
static struct cache_object *wcache_new_object(const char *);
static void wcache_free_object(struct cache_object *);
static int cache_open(struct cache_object *, int, int);
static void cache_unlink(struct cache_object *);
static void cache_writestate(struct cache_state *state);
static void expire_sched(void);
static void idx_add(struct cache_object *);
static void idx_rem(struct cache_object *);
static struct cache_object *idx_find(const char *key);

#define hashsize(n) ((uint32_t)1<<(n))
#define hashmask(n) (hashsize(n)-1)

#define HASH_ELEMS hashsize(HASH_BITS)

static struct cache_state state;
static struct event expire_ev;
static struct timeval expire_tv;

static int int_max_len;

struct key_idx_entry {
	struct cache_object *obj;
	LIST_ENTRY(key_idx_entry) entries;
};

LIST_HEAD(key_idx_head, key_idx_entry);
struct key_idx_bucket {
	key_idx_head head;
} key_idx[HASH_ELEMS];
 	
TAILQ_HEAD(objlist, cache_object) objects;

static int
cache_open(cache_object *obj, int flags, int mode)
{
	char *path;
	int plen;
	int i;

	plen = strlen(config.caches[0].dir) + 1 + sizeof(CACHEDIR) + 1 + 6 + int_max_len;
	path = (char *)wmalloc(plen + 1);
	sprintf(path, "%s/%s/%s", config.caches[0].dir, CACHEDIR, obj->co_path);
	if (mode)
		unlink(path);
	i = open(path, flags, mode);
	wfree(path);
	return i;
}

static void
cache_unlink(cache_object *obj)
{
	char *path;
	int plen;

	plen = strlen(config.caches[0].dir) + 1 + sizeof(CACHEDIR) + 1 + 6 + int_max_len;
	path = (char *)wmalloc(plen + 1);
	sprintf(path, "%s/%s/%s", config.caches[0].dir, CACHEDIR, obj->co_path);
	unlink(path);
	wfree(path);
	return;
}

void
wcache_setupfs(void)
{
	int		 i, j, k;
struct	cachedir	*cd;
struct	cache_state	 state;
	
	for (cd = config.caches; cd < config.caches + config.ncaches; ++cd) {
		size_t	 dlen;
		char 	*dir;
		
		dlen = strlen(cd->dir) + sizeof(CACHEDIR) + 2 + 6 /* 0/1/2/ */;
		if ((dir = (char *)wmalloc(dlen)) == NULL)
			outofmemory();
		
		snprintf(dir, dlen, "%s/%s", cd->dir, CACHEDIR);
		
		/* create base directory if it doesn't exist */
		/*LINTED unsafe mkdir*/
		if (mkdir(cd->dir, 0700) < 0 || mkdir(dir, 0700) < 0) {
			wlog(WLOG_ERROR, format("%s: mkdir: %e") % cd->dir);
			exit(8);
		}
		
		for (i = 0; i < 10; ++i) {
			snprintf(dir, dlen, "%s/%s/%d", cd->dir, CACHEDIR, i);
			
			/*LINTED unsafe mkdir*/
			if (mkdir(dir, 0700) < 0) {
				wlog(WLOG_ERROR, format("%s: mkdir: %e") % dir);
				exit(8);
			}
			
			for (j = 0; j < 10; ++j) {
				snprintf(dir, dlen, "%s/%s/%d/%d", cd->dir, CACHEDIR, i, j);
				/*LINTED unsafe mkdir*/
				if (mkdir(dir, 0700) < 0) {
					wlog(WLOG_ERROR, format("%s: mkdir: %e")
						% dir);
					exit(8);
				}
				for (k = 0; k < 10; ++k) {
					snprintf(dir, dlen, "%s/%s/%d/%d/%d", cd->dir, CACHEDIR, i, j, k);
					/*LINTED unsafe mkdir*/
					if (mkdir(dir, 0700) < 0) {
						wlog(WLOG_ERROR, 
						format("%s: mkdir: %e") % dir);
						exit(8);
					}
				}
			}
		}
		wfree(dir);
		wlog(WLOG_NOTICE, format("created cache directory structure for %s")
			% cd->dir);
	}
	wcache_init(0);
	
	bzero(&state, sizeof(state));
	state.cs_id = 1000;
	state.cs_size = 0;
	cache_writestate(&state);
	
	wlog(WLOG_NOTICE, "wrote initial cache state");
	wcache_shutdown();
}

void
wcache_shutdown(void)
{
	cache_writestate(&state);
}

void
wcache_init(int readstate)
{
struct	cachedir	*cd;
	
	if (config.ncaches == 0) {
		wlog(WLOG_NOTICE, "caching disabled, running in proxy-only mode");
		return;
	}
	
	/* only one cache dir supported for now... */
	for (cd = config.caches; cd < config.caches + config.ncaches; ++cd) {
	}
	
	int_max_len = (int) log10((double) INT_MAX) + 1;

	TAILQ_INIT(&objects);

	cache_getstate(&state);
	if (readstate) {
		expire_sched();
	}
}

static void
expire_sched(void)
{
	expire_tv.tv_usec = 0;
	expire_tv.tv_sec = config.cache_expevery;
	evtimer_set(&expire_ev, run_expiry, NULL);
	event_add(&expire_ev, &expire_tv);
}

void
wcache_release(cache_object *obj, int comp)
{
	if (comp) {
		if (!obj->co_complete)
			state.cs_size += obj->co_size;
		obj->co_complete = 1;
	} else if (!obj->co_complete) {
		wcache_evict(obj);
	}
}

static void
wcache_evict(cache_object *obj)
{
	TAILQ_REMOVE(&objects, obj, entries);
	cache_unlink(obj);
	state.cs_size -= obj->co_size;
	idx_rem(obj);
	wcache_free_object(obj);
}

struct cache_object *
wcache_find_object(const char *key, int *fd, int flags)
{
struct	cache_object	*co;

	co = idx_find(key);

	if (co) {
		/*
		 * If they only want it for writing, fail if it exists
		 */
		if (flags & WCACHE_WRONLY)
			return NULL;

		if (!strcmp(key, co->co_key)) {
			if (!co->co_complete) {
				return NULL;
			}
			*fd = cache_open(co, O_RDONLY, 0);
			if (*fd == -1) {
				wlog(WLOG_WARNING,
					format("opening cache file %s: %e")
					% co->co_path);
				co = NULL;
			}
			return co;
		}
	}

	co = wcache_new_object(key);
	idx_add(co);
	if ((*fd = cache_open(co, O_WRONLY | O_CREAT | O_EXCL, 0600)) == -1) {
		wlog(WLOG_WARNING, format("opening cached file: %e"));
		wcache_free_object(co);
		return NULL;
	}

	return co;
}

static void
cache_writestate(cache_state *state)
{
	FILE 		*stfil;
	char		*stpath;
	int		 stlen;
struct	cache_object	*obj;

	if (!config.caches)
		return;
	stlen = strlen(config.caches[0].dir) + 1 + 5 + 1;
	if ((stpath = (char *)wmalloc(stlen)) == NULL)
		outofmemory();
	snprintf(stpath, stlen, "%s/%s", config.caches[0].dir, "state");
	if ((stfil = fopen(stpath, "w")) == NULL) {
		wlog(WLOG_WARNING, format("opening cache dir %s: %e") % stpath);
		exit(8);
	}
	fprintf(stfil, "%llu %llu\n", (w_size_t) state->cs_id, (w_size_t)state->cs_size);
	TAILQ_FOREACH(obj, &objects, entries) {
		if (!obj->co_complete)
			continue;
		fprintf(stfil, "%s %s %lu %lu %lu %d %lu\n", obj->co_key, obj->co_path, 
			(unsigned long) obj->co_size, 
		        (unsigned long) obj->co_time, (unsigned long) obj->co_lru, obj->co_id, 
		        (unsigned long) obj->co_expires);
	}
	fclose(stfil);
}

static void
cache_getstate(cache_state *state)
{
	FILE 		*stfil;
	char		*stpath;
	int		 stlen;
	char		*s;

	stlen = strlen(config.caches[0].dir) + 1 + 5 + 1;
	if ((stpath = (char *)wmalloc(stlen)) == NULL)
		outofmemory();
	snprintf(stpath, stlen, "%s/%s", config.caches[0].dir, "state");
	if ((stfil = fopen(stpath, "r")) == NULL) {
		wlog(WLOG_WARNING, format("opening cache state %s: %e") % stpath);
		wlog(WLOG_WARNING, "using default cache state");
		state->cs_id = 1000;
		return;
	}

	if (fscanf(stfil, "%llu %llu\n", &state->cs_id, &state->cs_size) != 2) {
		wlog(WLOG_ERROR, format("data format error in cache state file %s")
			% stpath);
		exit(8);
	}

	s = (char *)wmalloc(65535);
	while (fgets(s, 65534, stfil)) {
		char url[65535], path[128];
		int size, time, lru, id, expires;
		struct cache_object *obj;
		if (sscanf(s, "%65534s %127s %d %d %d %d %d", url, path, &size, &time, &lru, &id, &expires) != 7) {
			wlog(WLOG_ERROR, 
				format("data format error in cache state file %s")
				% stpath);
			exit(8);
		}
		obj = (cache_object *)wmalloc(sizeof(*obj));
		bzero(obj, sizeof(*obj));
		obj->co_key = wstrdup(url);
		obj->co_size = size;
		obj->co_path = wstrdup(path);
		obj->co_complete = 1;
		obj->co_time = time;
		obj->co_lru = lru;
		obj->co_id = id;
		obj->co_expires = expires;
		TAILQ_INSERT_TAIL(&objects, obj, entries);
		idx_add(obj);
	}

}

static int
cache_next_id(void)
{
	int i;
	i = state.cs_id;
	++state.cs_id;
	return i;
}

static struct cache_object *
wcache_new_object(const char *key)
{
struct	cache_object	*ret;
	int		 i;
	char		 *p, *s, a[11];

	if ((ret = (cache_object *)wcalloc(1, sizeof(*ret))) == NULL) {
		outofmemory();
		/*NOTREACHED*/
	}
	
	ret->co_id = cache_next_id();
	ret->co_key = wstrdup(key);

	assert(ret->co_id > 999);
	ret->co_plen = int_max_len + 6;
	if ((ret->co_path = (char *)wmalloc(ret->co_plen + 1)) == NULL) {
		outofmemory();
		/*NOTREACHED*/
	}
	p = ret->co_path;
	snprintf(a, 10, "%d", ret->co_id);
	s = a + strlen(a) - 1;
	
	for (i = 0; i < 3; ++i) {
		*p++ = *s--;
		*p++ = '/';
 	}
	*p = '\0';
	if (strlcat(ret->co_path, a, ret->co_plen + 1) >= ret->co_plen + 1)
		abort();

	TAILQ_INSERT_HEAD(&objects, ret, entries);
	return ret;
}

static void
wcache_free_object(cache_object *obj)
{
}

static void
run_expiry(int fd, short ev, void *data)
{
	w_size_t	 wantsize;

	cache_writestate(&state);
	wantsize = (w_size_t) (config.caches[0].maxsize * ((100.0-config.cache_expthresh)/100));
	if (state.cs_size <= wantsize) {
		expire_sched();
		return;
	}
	while (state.cs_size > wantsize) {
		struct cache_object *obj = TAILQ_LAST(&objects, objlist);
		if (!obj)
			break;
		wcache_evict(obj);
	}
	expire_sched();
}

static void
idx_add(cache_object *obj)
{
	struct key_idx_head *head = &key_idx[hash((u_char *)obj->co_key)].head;
	struct key_idx_entry *entry = (key_idx_entry *)wmalloc(sizeof(*entry));
	bzero(entry, sizeof(*entry));
	entry->obj = obj;
	LIST_INSERT_HEAD(head, entry, entries);
}

static struct cache_object *
idx_find(const char *key)
{
	struct key_idx_head *head = &key_idx[hash((u_char *)key)].head;
	struct key_idx_entry *entry;
	LIST_FOREACH(entry, head, entries)
		if (!strcmp(entry->obj->co_key, key))	
			return entry->obj;
	return NULL;
}

static void
idx_rem(cache_object *obj)
{
	struct key_idx_head *head = &key_idx[hash((u_char *)obj->co_key)].head;
	struct key_idx_entry *entry;
	LIST_FOREACH(entry, head, entries)
		if (entry->obj == obj)	
			LIST_REMOVE(entry, entries);
}

#define mix(a,b,c) \
{ \
  a -= b; a -= c; a ^= (c>>13); \
  b -= c; b -= a; b ^= (a<<8); \
  c -= a; c -= b; c ^= (b>>13); \
  a -= b; a -= c; a ^= (c>>12);  \
  b -= c; b -= a; b ^= (a<<16); \
  c -= a; c -= b; c ^= (b>>5); \
  a -= b; a -= c; a ^= (c>>3);  \
  b -= c; b -= a; b ^= (a<<10); \
  c -= a; c -= b; c ^= (b>>15); \
}

/*
 * By Bob Jenkins, 1996.  bob_jenkins@burtleburtle.net.  You may use this
 * code any way you wish, private, educational, or commercial.  It's free.
 *
 * See http://burtleburtle.net/bob/hash/evahash.html
 */

uint32_t hash(const u_char *k, int bits)
{
   uint32_t a,b,c,len;
   uint32_t length = strlen((char *)k);
   uint32_t initval = 0;

   /* Set up the internal state */
   len = length;
   a = b = 0x9e3779b9;  /* the golden ratio; an arbitrary value */
   c = initval;         /* the previous hash value */

   /*---------------------------------------- handle most of the key */
   while (len >= 12)
   {
      a += (k[0] +((uint32_t)k[1]<<8) +((uint32_t)k[2]<<16) +((uint32_t)k[3]<<24));
      b += (k[4] +((uint32_t)k[5]<<8) +((uint32_t)k[6]<<16) +((uint32_t)k[7]<<24));
      c += (k[8] +((uint32_t)k[9]<<8) +((uint32_t)k[10]<<16)+((uint32_t)k[11]<<24));
      mix(a,b,c);
      k += 12; len -= 12;
   }

   /*------------------------------------- handle the last 11 bytes */
   c += length;
   switch(len)              /* all the case statements fall through */
   {
   case 11: c+=((uint32_t)k[10]<<24);
   case 10: c+=((uint32_t)k[9]<<16);
   case 9 : c+=((uint32_t)k[8]<<8);
      /* the first byte of c is reserved for the length */
   case 8 : b+=((uint32_t)k[7]<<24);
   case 7 : b+=((uint32_t)k[6]<<16);
   case 6 : b+=((uint32_t)k[5]<<8);
   case 5 : b+=k[4];
   case 4 : a+=((uint32_t)k[3]<<24);
   case 3 : a+=((uint32_t)k[2]<<16);
   case 2 : a+=((uint32_t)k[1]<<8);
   case 1 : a+=k[0];
     /* case 0: nothing left to add */
   }
   mix(a,b,c);
   /*-------------------------------------------- report the result */
   return c & hashmask(bits);
}
