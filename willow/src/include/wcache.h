/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wcache: entity caching.
 */

#ifndef WCACHE_H
#define WCACHE_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include "willow.h"
#include "queue.h"

#define WCACHE_FREE	1

/*
 * Information about the current cache state, stored in the null key
 * (length 0).
 */
struct cache_state {
	w_size_t	cs_id;
	w_size_t	cs_size;
};

struct cache_object {
	char		 *co_key;
	int		  co_flags;
	time_t		  co_expires;	/* Expires: header or -1		*/
	time_t		  co_time;	/* Last-Modified / retrieval time	*/
	time_t		  co_lru;	/* Last used timestamp, for eviction	*/
	int		  co_id;	/* Object id				*/
	int		  co_plen;	/* Size of cache object path		*/
	size_t		  co_size;	/* Object size				*/
	char		 *co_path;	/* Object data location			*/
	int		  co_complete;	/* Finished being cached		*/

	TAILQ_ENTRY(cache_object) entries;
};

void wcache_init(int);
void wcache_setupfs(void);
void wcache_shutdown(void);
void state_lock(void);
void state_unlock(void);

struct cache_object *wcache_find_object(const char *key, int *);
void wcache_release(struct cache_object *, int complete);

#endif
