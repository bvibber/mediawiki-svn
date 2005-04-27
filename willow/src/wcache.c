/* @(#) $Header$ */
/* This source code is in the public domain. */
/* 
 * Willow: Lightweight HTTP reverse-proxy.
 * wcache: entity caching.
 */

/*
 * Cache metadata is stored in a BerkeleyDB database, along with a key which
 * represents the filename. The objects themselves are stored on a filesystem,
 * using the key and a path constructed from the key's prefix; for example,
 * the key "123456" would be stored as "1/2/3/123456".
 */
 
#include <sys/types.h>
#include <sys/stat.h>

#include <string.h>
#include <errno.h>
#include <stdlib.h>
#include <math.h>

#include <db.h>
 
#include "wcache.h"
#include "wlog.h"
#include "wconfig.h"
#include "willow.h"

DB_ENV *cacheenv;
DB *cacheobjs;

#define CACHEDIR "__objects__"

static void cache_writestate(struct cache_state *, DB_TXN *);
static int cache_getstate(struct cache_state *, DB_TXN *);
static int cache_next_id(void);

static struct cache_state state;

void
wcache_setupfs(void)
{
	int		 i, j, k;
struct	cachedir	*cd;
struct	cache_state	 state;
	DB_TXN		 *txn;
	
	for (cd = config.caches; cd < config.caches + config.ncaches; ++cd) {
		char *dir = wmalloc(strlen(cd->dir) + sizeof(CACHEDIR) + 7);
		char *env;
		
		sprintf(dir, "%s/%s", cd->dir, CACHEDIR);
		env = wmalloc(strlen(cd->dir) + 9);
		sprintf(env, "%s/__env__", cd->dir);
		
		/* create base directory if it doesn't exist */
		if (mkdir(cd->dir, 0700) < 0 || mkdir(dir, 0700) < 0 || mkdir(env, 0700)) {
			wlog(WLOG_ERROR, "%s: mkdir: %s", cd->dir, strerror(errno));
			exit(8);
		}
		
		for (i = 0; i < 10; ++i) {
			sprintf(dir, "%s/%s/%d", cd->dir, CACHEDIR, i);
			if (mkdir(dir, 0700) < 0) {
				wlog(WLOG_ERROR, "%s: mkdir: %s", dir, strerror(errno));
				exit(8);
			}
			
			for (j = 0; j < 10; ++j) {
				sprintf(dir, "%s/%s/%d/%d", cd->dir, CACHEDIR, i, j);
				if (mkdir(dir, 0700) < 0) {
					wlog(WLOG_ERROR, "%s: mkdir: %s", dir, strerror(errno));
					exit(8);
				}
				for (k = 0; k < 10; ++k) {
					sprintf(dir, "%s/%s/%d/%d/%d", cd->dir, CACHEDIR, i, j, k);
					if (mkdir(dir, 0700) < 0) {
						wlog(WLOG_ERROR, "%s: mkdir: %s", dir, strerror(errno));
						exit(8);
					}
				}
			}
		}
		wfree(dir);
		wfree(env);
		wlog(WLOG_NOTICE, "created cache directory structure for %s", cd->dir);
	}
	wcache_init();
	
	memset(&state, 0, sizeof(state));
	state.cs_id = 1000;
	if (i = cacheenv->txn_begin(cacheenv, NULL, &txn, 0)) {
		wlog(WLOG_ERROR, "setupfs: txn_begin: %s", db_strerror(i));
		exit(8);
	}
	cache_writestate(&state, txn);
	if (i = txn->commit(txn, 0)) {
		wlog(WLOG_ERROR, "setupfs: commit: %s", db_strerror(i));
		exit(8);
	}
	wlog(WLOG_NOTICE, "wrote initial cache state");
	wcache_shutdown();
}									
			
void
wcache_shutdown(void)
{
	cacheobjs->close(cacheobjs, 0);
	cacheenv->close(cacheenv, 0);
}

void
wcache_init(void)
{
struct	cachedir	*cd;
	int		 i;
struct	stat		 sb;
	DB_TXN		*txn;
	
	wlog(WLOG_NOTICE, "using bdb: %s", DB_VERSION_STRING);
	
	/* only one cache dir supported for now... */
	for (cd = config.caches; cd < config.caches + config.ncaches; ++cd) {
		char *dir;
		
		fprintf(stderr, "pass\n");
		dir = wmalloc(strlen(cd->dir) + 9);
		sprintf(dir, "%s/__env__", cd->dir);
		
		if (i = db_env_create(&cacheenv, 0)) {
			wlog(WLOG_ERROR, "%s: db_env_create: %s", 
					cd->dir, db_strerror(i));
			exit(8);
		}

		cacheenv->set_errfile(cacheenv, stderr);
		cacheenv->set_errpfx(cacheenv, "willow");

		if (i = cacheenv->open(cacheenv, dir, DB_CREATE | DB_INIT_TXN | DB_INIT_LOCK | 
				DB_INIT_MPOOL | DB_PRIVATE | DB_THREAD, 0)) {
			wlog(WLOG_ERROR, "%s: open: %s",
					cd->dir, db_strerror(i));
			exit(8);
		}
		
		if (i = db_create(&cacheobjs, cacheenv, 0)) {
			wlog(WLOG_ERROR, "%s: db_create: %s",
					cd->dir, db_strerror(i));
			exit(8);
		}
		
		cacheenv->txn_begin(cacheenv, NULL, &txn, 0);
		if (i = cacheobjs->open(cacheobjs, txn, "cacheobjs.db", NULL, DB_HASH,
				DB_CREATE, 0600)) {
			wlog(WLOG_ERROR, "%s: db open: %s", cd->dir, db_strerror(i));
			exit(8);
		}
		txn->commit(txn, 0);
		
		wfree(dir);
	}
	
	cacheenv->txn_begin(cacheenv, NULL, &txn, 0);
	cache_getstate(&state, txn);
	txn->commit(txn, 0);
}

static char *
make_keybuf(key)
	struct cache_key *key;
{
	char *buf = wmalloc(key->ck_len + 1);
	memcpy(buf, key->ck_key, key->ck_len + 1);
	return buf;
}

static char *
make_databuf(obj)
	struct cache_object *obj;
{
	char *buf = wmalloc(sizeof(struct cache_object) + obj->co_plen + 1);
	memcpy(buf, obj, sizeof(struct cache_object));
	memcpy(buf + sizeof(struct cache_object), obj->co_path, obj->co_plen + 1);
	return buf;
}
	
struct cache_object *
wcache_find_object(key)
	struct cache_key *key;
{
	DBT		 keyt, datat;
	DB_TXN		*txn;
struct	cache_object	*data;
	int		 i;
	char		*keybuf, *databuf;

	DEBUG((WLOG_DEBUG, "wcache_find_object: looking for %s %d", key->ck_key, key->ck_len));
	keybuf = make_keybuf(key);	
	
	memset(&keyt, 0, sizeof(keyt));
	memset(&datat, 0, sizeof(datat));
	
	keyt.data = keybuf;
	keyt.size = key->ck_len + 1;
	
	datat.flags = DB_DBT_MALLOC;
	
	cacheenv->txn_begin(cacheenv, NULL, &txn, 0);
	i = cacheobjs->get(cacheobjs, txn, &keyt, &datat, 0);
	txn->commit(txn, 0);
	
	wfree(keybuf);
	
	if (i) {
		if (i != DB_NOTFOUND)
			wlog(WLOG_WARNING, "database error: %s", db_strerror(i));
		return NULL;
	}
	
	data = datat.data;
	data->co_path = (char *)data + sizeof(*data);
	DEBUG((WLOG_DEBUG, "found %s, path=[%s]", key->ck_key, data->co_path));
	data->co_flags &= ~WCACHE_FREE;
	return data;
}

int
wcache_store_object(key, obj)
	struct cache_key *key;
	struct cache_object *obj;
{
	DBT		 keyt, datat;
	DB_TXN		 *txn;
	char		*keybuf, *databuf;
	int		 i;
	
	DEBUG((WLOG_DEBUG, "storing %s %d in cache, path %s", key->ck_key, key->ck_len, obj->co_path));
	
	memset(&keyt, 0, sizeof(keyt));
	memset(&datat, 0, sizeof(datat));
	keybuf = make_keybuf(key);
	
	keyt.data = keybuf;
	keyt.size = key->ck_len + 1;
	
	datat.data = make_databuf(obj);
	datat.size = sizeof(struct cache_object) + obj->co_plen + 1;
	
	cacheenv->txn_begin(cacheenv, NULL, &txn, 0);
	i = cacheobjs->put(cacheobjs, txn, &keyt, &datat, DB_NOOVERWRITE);
	txn->commit(txn, 0);
	
	wfree(keybuf);
	wfree(datat.data);
	
	if (i) {
		wlog(WLOG_WARNING, "cache_put: %s", db_strerror(i));
		return i;
	}
	return 0;
}

struct cache_key *
wcache_make_key(host, path)
	const char *host, *path;
{
struct	cache_key	*ret;
	
	ret = wmalloc(sizeof(*ret));
	ret->ck_len = strlen(host) + strlen(path);
	ret->ck_key = wmalloc(ret->ck_len + 1);
	sprintf(ret->ck_key, "%s%s", host, path);
	return ret;
}

void
wcache_free_key(key)
	struct cache_key *key;
{
	wfree(key->ck_key);
	wfree(key);
}

void
wcache_free_object(obj)
	struct cache_object *obj;
{
	if (obj->co_flags & WCACHE_FREE) {
		wfree(obj->co_path);
		wfree(obj);
	} else {
		free(obj);
	}
}

static void
cache_writestate(state, txn)
	struct cache_state *state;
	DB_TXN *txn;
{
	DBT	keyt, datat;
	int	i;
	
	DEBUG((WLOG_DEBUG, "writing cache state"));
	
	memset(&keyt, 0, sizeof(keyt));
	memset(&datat, 0, sizeof(datat));
	
	keyt.size = 5;
	keyt.data = "STATE";
	
	datat.size = sizeof(*state);
	datat.data = state;
	
	if (i = cacheobjs->put(cacheobjs, txn, &keyt, &datat, 0)) {
		wlog(WLOG_ERROR, "writing cache state: %s", db_strerror(i));
	}
	DEBUG((WLOG_DEBUG, "write: cs_id = %d", state->cs_id));
}

static int
cache_getstate(state, txn)
	struct cache_state *state;
	DB_TXN *txn;
{
	DBT	keyt, datat;
	int	i;
	
	DEBUG((WLOG_DEBUG, "reading cache state"));
	
	memset(&keyt, 0, sizeof(keyt));
	memset(&datat, 0, sizeof(datat));
	
	keyt.size = 5;
	keyt.data = "STATE";
	
	datat.ulen = sizeof(*state);
	datat.data = state;
	datat.flags = DB_DBT_USERMEM;
		
	if (i = cacheobjs->get(cacheobjs, txn, &keyt, &datat, 0)) {
		wlog(WLOG_WARNING, "reading cache state: %s", db_strerror(i));
		return i;
	}
	
	DEBUG((WLOG_DEBUG, "cs_id = %d", state->cs_id));
	return 0;
}

static int
cache_next_id(void)
{
	DB_TXN *txn;
	int	i;
	
	cacheenv->txn_begin(cacheenv, NULL, &txn, 0);
	state.cs_id++;
	cache_writestate(&state, txn);
	txn->commit(txn, 0);

	return state.cs_id;
}

struct cache_object *
wcache_new_object(key)
	struct cache_key *key;
{
struct	cache_object	*ret;
	int		 i;
	char		 *p, *s, a[11];

	ret = wmalloc(sizeof(*ret));
	memset(ret, 0, sizeof(*ret));
	ret->co_id = cache_next_id();
	
	ret->co_plen = (log10(ret->co_id) + 1) + 6;
	ret->co_path = wmalloc(ret->co_plen + 1);
	p = ret->co_path;
	sprintf(a, "%d", ret->co_id);
	s = a + strlen(a) - 1;
	DEBUG((WLOG_DEBUG, "id=%d a=%s", ret->co_id, a));
	
	for (i = 0; i < 3; ++i) {
		*p++ = *s--;
		*p++ = '/';
	}
	*p = '\0';
	strcat(ret->co_path, a);
	ret->co_flags |= WCACHE_FREE;
	DEBUG((WLOG_DEBUG, "new object path is [%s], len %d", ret->co_path, ret->co_plen));
	return ret;
}
