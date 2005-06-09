/* @(#) $Header$ */
/* This source code is in the public domain. */
/* 
 * Willow: Lightweight HTTP reverse-proxy.
 * wcache: entity caching.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include <sys/types.h>
#include <sys/stat.h>

#include <string.h>
#include <errno.h>
#include <stdlib.h>
#include <math.h>
#include <strings.h>
#include <limits.h>
#include <assert.h>
#include <pthread.h>
#include <unistd.h>
#include <fcntl.h>

#include "wcache.h"
#include "wlog.h"
#include "wconfig.h"
#include "willow.h"

#define CACHEDIR "__objects__"

struct cache_object cache_meta;

static void dberror(const char *, int);
static int cache_next_id(void);
static void *run_expirey(void *);
static void wcache_evict(struct cache_object *);
static void cache_getstate(struct cache_state *);
static struct cache_object *wcache_new_object(const char *);
static void wcache_free_object(struct cache_object *);
static int cache_open(struct cache_object *, int, int);

static struct cache_state state;
pthread_mutex_t state_mtx = PTHREAD_MUTEX_INITIALIZER;

static int int_max_len;
static pthread_t expire_thread;

static int
cache_open(obj, flags, mode)
	struct cache_object *obj;
	int flags, mode;
{
	char *path;
	int plen;
	int i;

	plen = strlen(config.caches[0].dir) + 1 + sizeof(CACHEDIR) + 1 + 6 + int_max_len;
	path = wmalloc(plen + 1);
	sprintf(path, "%s/%s/%s", config.caches[0].dir, CACHEDIR, obj->co_path);
	if (mode)
		unlink(path);
	i = open(path, flags, mode);
	wfree(path);
	return i;
}

void
wcache_setupfs(void)
{
	int		 i, j, k;
struct	cachedir	*cd;
struct	cache_state	 state;
	
	for (cd = config.caches; cd < config.caches + config.ncaches; ++cd) {
		size_t	 len, dlen;
		char 	*dir;
		
		dlen = strlen(cd->dir) + sizeof(CACHEDIR) + 2 + 6 /* 0/1/2/ */;
		if ((dir = wmalloc(dlen)) == NULL)
			outofmemory();
		
		safe_snprintf(dlen, (dir, dlen, "%s/%s", cd->dir, CACHEDIR));
		
		/* create base directory if it doesn't exist */
		/*LINTED unsafe mkdir*/
		if (mkdir(cd->dir, 0700) < 0 || mkdir(dir, 0700) < 0) {
			wlog(WLOG_ERROR, "%s: mkdir: %s", cd->dir, strerror(errno));
			exit(8);
		}
		
		for (i = 0; i < 10; ++i) {
			safe_snprintf(dlen, (dir, dlen, "%s/%s/%d", cd->dir, CACHEDIR, i));
			
			/*LINTED unsafe mkdir*/
			if (mkdir(dir, 0700) < 0) {
				wlog(WLOG_ERROR, "%s: mkdir: %s", dir, strerror(errno));
				exit(8);
			}
			
			for (j = 0; j < 10; ++j) {
				safe_snprintf(dlen, (dir, dlen, "%s/%s/%d/%d", cd->dir, CACHEDIR, i, j));
				/*LINTED unsafe mkdir*/
				if (mkdir(dir, 0700) < 0) {
					wlog(WLOG_ERROR, "%s: mkdir: %s", dir, strerror(errno));
					exit(8);
				}
				for (k = 0; k < 10; ++k) {
					safe_snprintf(dlen, (dir, dlen, "%s/%s/%d/%d/%d", cd->dir, CACHEDIR, i, j, k));
					/*LINTED unsafe mkdir*/
					if (mkdir(dir, 0700) < 0) {
						wlog(WLOG_ERROR, "%s: mkdir: %s", dir, strerror(errno));
						exit(8);
					}
				}
			}
		}
		wfree(dir);
		wlog(WLOG_NOTICE, "created cache directory structure for %s", cd->dir);
	}
	wcache_init(0);
	
	bzero(&state, sizeof(state));
	state.cs_id = 1000;
	state.cs_size = 0;
	
	wlog(WLOG_NOTICE, "wrote initial cache state");
	wcache_shutdown();
}									
			
void
wcache_shutdown(void)
{
}

void
state_lock()
{
#ifdef THREADED_IO
	pthread_mutex_lock(&state_mtx);
#endif
}

void
state_unlock()
{
#ifdef THREADED_IO
	pthread_mutex_unlock(&state_mtx);
#endif
}

void
wcache_init(readstate)
	int readstate;
{
struct	cachedir	*cd;
	int		 i;
	
	if (config.ncaches == 0) {
		wlog(WLOG_WARNING, "no cache directories specified");
		return;
	}
	
	/* only one cache dir supported for now... */
	for (cd = config.caches; cd < config.caches + config.ncaches; ++cd) {
	}
	
	int_max_len = (int) log10((double) INT_MAX) + 1;

	cache_getstate(&state);

	if (readstate)
		pthread_create(&expire_thread, NULL, run_expirey, NULL);
}

void
wcache_release(obj, comp)
	struct cache_object *obj;
	int comp;
{
	WDEBUG((WLOG_DEBUG, "release %s, comp=%d", obj->co_key, comp));

	if (comp) {
		obj->co_complete = 1;
	} else {
		struct cache_object *o;
		for (o = &cache_meta; o->co_next; o = o->co_next)
			if (o->co_next == obj) {
				o->co_next = o->co_next->co_next;
				wcache_free_object(obj);
				break;
			}
	}
}

struct cache_object *
wcache_find_object(key, fd)
	const char *key;
	int *fd;
{
struct	cache_object	*co;

	state_lock();
	for (co = cache_meta.co_next; co; co = co->co_next)
		if (!strcmp(key, co->co_key)) {
			if (!co->co_complete) {
				state_unlock();
				return NULL;
			}
			*fd = cache_open(co, O_RDONLY, 0);
			if (*fd == -1)
				co = NULL;
			state_unlock();
			return co;
		}

	state_unlock();
	co = wcache_new_object(key);
	if ((*fd = cache_open(co, O_WRONLY | O_CREAT | O_EXCL, 0600)) == -1) {
		wlog(WLOG_WARNING, "opening cached file: %s", strerror(errno));
		wcache_free_object(co);
		return NULL;
	}

	return co;
}

static void
cache_writestate(state)
	struct cache_state *state;
{
	FILE 		*stfil;
	char		*stpath;
	int		 stlen;
struct	cache_object	*obj;

	stlen = strlen(config.caches[0].dir) + 1 + 5 + 1;
	if ((stpath = wmalloc(stlen)) == NULL)
		outofmemory();
	safe_snprintf(stlen, (stpath, stlen, "%s/%s", config.caches[0].dir, "state"));
	if ((stfil = fopen(stpath, "w")) == NULL) {
		wlog(WLOG_WARNING, "opening cache dir %s: %s", stpath, strerror(errno));
		exit(8);
	}
	fprintf(stfil, "%d\n", state->cs_id);
	for (obj = cache_meta.co_next; obj; obj = obj->co_next)
		fprintf(stfil, "%s %s %d %d\n", obj->co_key, obj->co_path, obj->co_lru, obj->co_size);
	fclose(stfil);
}

static void
cache_getstate(state)
	struct cache_state *state;
{
	state->cs_id = 1000;
}

static int
cache_next_id(void)
{
	int i;
	state_lock();
	i = state.cs_id;
	++state.cs_id;
	state_unlock();
	return i;
}

static struct cache_object *
wcache_new_object(key)
	const char *key;
{
struct	cache_object	*ret;
	int		 i;
	char		 *p, *s, a[11];

	if ((ret = wcalloc(1, sizeof(*ret))) == NULL) {
		outofmemory();
		/*NOTREACHED*/
	}
	
	ret->co_id = cache_next_id();
	state_lock();
	ret->co_key = wstrdup(key);

	assert(ret->co_id > 999);
	ret->co_plen = int_max_len + 6;
	if ((ret->co_path = wmalloc(ret->co_plen + 1)) == NULL) {
		outofmemory();
		/*NOTREACHED*/
	}
	p = ret->co_path;
	safe_snprintf(10, (a, 10, "%d", ret->co_id));
	s = a + strlen(a) - 1;
	WDEBUG((WLOG_DEBUG, "id=%d a=%s", ret->co_id, a));
	
	for (i = 0; i < 3; ++i) {
		*p++ = *s--;
		*p++ = '/';
 	}
	*p = '\0';
	if (strlcat(ret->co_path, a, ret->co_plen + 1) >= ret->co_plen + 1)
		abort();
	WDEBUG((WLOG_DEBUG, "new object path is [%s], len %d", ret->co_path, ret->co_plen));

	ret->co_next = cache_meta.co_next;
	cache_meta.co_next = ret;
	state_unlock();
	return ret;
}

static void
wcache_free_object(obj)
	struct cache_object *obj;
{
}

static void *
run_expirey(data)
	void *data;
{
	wlog(WLOG_NOTICE, "cache expirey thread starting");
	for (;;) {
		w_size_t	 wantsize;
		int		 i;
	struct	cache_object	*obj;

		WDEBUG((WLOG_DEBUG, "expire: start, run every %d", config.cache_expevery));
		sleep(config.cache_expevery);

		cache_writestate(&state);
		wantsize = config.caches[0].maxsize * ((100.0-config.cache_expthresh)/100);
		if (state.cs_size <= wantsize) {
			WDEBUG((WLOG_DEBUG, "expire: cache only %lld bytes large", state.cs_size));
			continue;
		}
		WDEBUG((WLOG_DEBUG, "expiring some objects, size=%lld, want=%lld", state.cs_size, wantsize));
	}
}
