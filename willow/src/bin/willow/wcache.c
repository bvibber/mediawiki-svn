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
#include <unistd.h>
#include <fcntl.h>
#include <event.h>

#include "wcache.h"
#include "wlog.h"
#include "wconfig.h"
#include "willow.h"

#define CACHEDIR "__objects__"

static void dberror(const char *, int);
static int cache_next_id(void);
static void run_expiry(int, short, void*);
static void wcache_evict(struct cache_object *);
static void cache_getstate(struct cache_state *);
static struct cache_object *wcache_new_object(const char *);
static void wcache_free_object(struct cache_object *);
static int cache_open(struct cache_object *, int, int);
static int cache_unlink(struct cache_object *);
static void cache_writestate(struct cache_state *state);
static void expire_sched(void);

static struct cache_state state;
static struct event expire_ev;
static struct timeval expire_tv;
static struct cache_object cache_meta;

static int int_max_len;

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

static int
cache_unlink(obj)
	struct cache_object *obj;
{
	char *path;
	int plen;
	int i;

	plen = strlen(config.caches[0].dir) + 1 + sizeof(CACHEDIR) + 1 + 6 + int_max_len;
	path = wmalloc(plen + 1);
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
	if (readstate) {
		expire_sched();
	}
}

static void
expire_sched()
{
	expire_tv.tv_usec = 0;
	expire_tv.tv_sec = config.cache_expevery;
	evtimer_set(&expire_ev, run_expiry, NULL);
	event_add(&expire_ev, &expire_tv);
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
				wcache_evict(obj);
				break;
			}
	}
}

static void
wcache_evict(obj)
	struct cache_object *obj;
{
struct	cache_object *prev	= obj->co_prev;
	prev->co_next = obj->co_next;
	if (cache_meta.co_tail == obj)
		cache_meta.co_tail = prev;
	cache_unlink(obj);
	wcache_free_object(obj);
}

struct cache_object *
wcache_find_object(key, fd)
	const char *key;
	int *fd;
{
struct	cache_object	*co;

	WDEBUG((WLOG_DEBUG, "wcache_find_object: looking for %s", key));
	for (co = cache_meta.co_next; co; co = co->co_next) {
		WDEBUG((WLOG_DEBUG, "trying %s, comp=%d", co->co_key, co->co_complete));
		if (!strcmp(key, co->co_key)) {
			if (!co->co_complete) {
				return NULL;
			}
			*fd = cache_open(co, O_RDONLY, 0);
			WDEBUG((WLOG_DEBUG, "found! fd=%d", *fd));
			if (*fd == -1) {
				wlog(WLOG_WARNING, "opening cache file %s: %s", co->co_path, strerror(errno));
				co = NULL;
			}
			return co;
		}
	}

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
	for (obj = cache_meta.co_next; obj; obj = obj->co_next) {
		if (!obj->co_complete)
			continue;
		fprintf(stfil, "%s %s %d %d %d %d %d\n", obj->co_key, obj->co_path, obj->co_size, obj->co_time,
			obj->co_lru, obj->co_id, obj->co_expires);
	}
	fclose(stfil);
}

static void
cache_getstate(state)
	struct cache_state *state;
{
	FILE 		*stfil;
	char		*stpath;
	int		 stlen;
struct	cache_object	*obj;
	int		 i;
	char		*s;
	size_t		 l;

	stlen = strlen(config.caches[0].dir) + 1 + 5 + 1;
	if ((stpath = wmalloc(stlen)) == NULL)
		outofmemory();
	safe_snprintf(stlen, (stpath, stlen, "%s/%s", config.caches[0].dir, "state"));
	if ((stfil = fopen(stpath, "r")) == NULL) {
		wlog(WLOG_WARNING, "opening cache state %s: %s", stpath, strerror(errno));
		wlog(WLOG_WARNING, "using default cache state");
		state->cs_id = 1000;
		return;
	}

	if (fscanf(stfil, "%d\n", &state->cs_id) != 1) {
		wlog(WLOG_ERROR, "data format error in cache state file %s", stpath);
		exit(8);
	}

	s = wmalloc(65535);
	while (fgets(s, 65534, stfil)) {
		char url[65535], path[128];
		int size, time, lru, id, expires;
		struct cache_object *obj;
		if (sscanf(s, "%65534s %127s %d %d %d %d %d", url, path, &size, &time, &lru, &id, &expires) != 7) {
			wlog(WLOG_ERROR, "data format error in cache state file %s", stpath);
			exit(8);
		}
		obj = wmalloc(sizeof(*obj));
		bzero(obj, sizeof(*obj));
		obj->co_key = wstrdup(url);
		obj->co_size = size;
		obj->co_path = wstrdup(path);
		obj->co_complete = 1;
		obj->co_time = time;
		obj->co_lru = lru;
		obj->co_id = id;
		obj->co_expires = expires;
		obj->co_prev = cache_meta.co_tail;
		cache_meta.co_tail = obj;
		WDEBUG((WLOG_DEBUG, "load %s %s from cache", obj->co_key, obj->co_path));
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
	if (cache_meta.co_next)
		cache_meta.co_next->co_prev = ret;
	cache_meta.co_next = ret;
	return ret;
}

static void
wcache_free_object(obj)
	struct cache_object *obj;
{
}

static void
run_expiry(fd, ev, data)
	int fd;
	short ev;
	void *data;
{
	w_size_t	 wantsize;
	int		 i;
struct	cache_object	*obj;

	WDEBUG((WLOG_DEBUG, "expire: start, run every %d", config.cache_expevery));

	cache_writestate(&state);
	wantsize = config.caches[0].maxsize * ((100.0-config.cache_expthresh)/100);
	if (state.cs_size <= wantsize) {
		WDEBUG((WLOG_DEBUG, "expire: cache only %lld bytes large", state.cs_size));
		expire_sched();
		return;
	}
	while (state.cs_size > wantsize) {
		struct cache_object *obj = cache_meta.co_tail;
		WDEBUG((WLOG_DEBUG, "expiring some objects, size=%lld, want=%lld", state.cs_size, wantsize));
		wcache_evict(obj);
	}
	expire_sched();
}
