/*
 * Copyright (c) 2004-2005 Sean Chittenden <sean@chittenden.org>
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * $Nexadesic: src/lib/libmemcache/memcache.h,v 1.34 2005/01/24 07:36:17 sean Exp $
 */

/*
 * The following copyright is included as the TAILQ_* macros come from
 * sys/queue.h which has the following LICENSE/Copyright notice.  XXX
 *
 * Copyright (c) 1991, 1993
 *      The Regents of the University of California.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 4. Neither the name of the University nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL THE REGENTS OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 *
 *      @(#)queue.h     8.5 (Berkeley) 8/20/94
 * $FreeBSD: src/sys/sys/queue.h,v 1.58 2004/04/07 04:19:49 imp Exp $
 */

#ifndef MEMCACHE_H
#define MEMCACHE_H

#include <netdb.h>
#include <sys/types.h>
#include <sys/time.h>
#include <unistd.h>

#ifdef __cplusplus
extern "C" {
#endif

/* Macros for testing versions */
#define MEMCACHE_VER		"1.2.3"
#define MEMCACHE_VERNUM		010203
#define MEMCACHE_RELDATE	20050123

/* Our initial read(2) buffer has to be long enough to read the
 * first line of the response.  ie:
 *
 * "VALUE #{'k' * 250} #{2 ** 15} #{2 ** 32}\r\n.length => 275
 *
 * However, since we want to avoid the number of system calls
 * necessary, include trailing part of the protocol in our estimate:
 *
 * "\r\nEND\r\n".length => 7
 *
 * Which yields a mandatory limit of 282 bytes for a successful
 * response.  If we wish to try and get lucky with our first read(2)
 * call and be able to read(2) in small values without making a second
 * read(2) call, pad this number with a sufficiently large byte value.
 * If most of your keys are 512B, then a GET_INIT_BUF_SIZE of 794
 * would be prudent (512 + 282).
 *
 * The default value of 1024 means that values less than 724 bytes
 * will always be read(2) via the first read(2) call.  Increasing this
 * value to large values is not beneficial.  If a second read(2) call
 * is necessary, the read(2) will be made with a sufficiently large
 * buffer already allocated. */
#define GET_INIT_BUF_SIZE ((size_t)1024)

/* Enables extra protocol checking.  It's not strictly necessary if the
 * server's sending the right data.  This should be on by default,
 * but, for those that don't want every check done to make sure things
 * are right, undef this.  99% of the universe should leave this as
 * is.  If you think you're than 1% who doesn't need it, you're on
 * crack and should definitely leave PEDANTIC on. %*/
#define PEDANTIC

#define MAX_KEY_LEN 250

#define HAVE_SELECT 1

/* #define USE_CRC32_HASH 1 */
/* #define USE_ELF_HASH 1 */
/* #define USE_PERL_HASH 1 */
#define USE_PHP_HASH 1

/* Various values for _flags.  Use their function counterparts instead
 * of testing these bits directly (ie: mcm_res_free_on_delete(),
 * mcm_res_found(), and mcm_res_attempted()). */
#define MCM_RES_FREE_ON_DELETE		0x01
#define MCM_RES_NO_FREE_ON_DELETE	0x02
#define MCM_RES_FOUND			0x04
#define MCM_RES_ATTEMPTED		0x08
#define MCM_RES_NEED_FREE_KEY		0x10

/* Aliases for MCM_RES_* #define's. Use their function counterparts
 * instead of testing these bits directly (ie:
 * mc_res_free_on_delete(), mc_res_found(), and
 * mc_res_attempted()).  */
#define MC_RES_FREE_ON_DELETE		MCM_RES_FREE_ON_DELETE
#define MC_RES_NO_FREE_ON_DELETE	MCM_RES_NO_FREE_ON_DELETE
#define MC_RES_FOUND			MCM_RES_FOUND
#define MC_RES_ATTEMPTED		MCM_RES_ATTEMPTED
#define MC_RES_NEED_FREE_KEY		MCM_RES_NEED_FREE_KEY

/* A convenience macro that lets people avoid the expense of strlen(3)
 * if they're using a string that's defined at compile time. */
#define MCM_CSTRLEN(_str) (sizeof(_str) - 1)


/* Begin various TAILQ macros */
#define TRASHIT(x)	do {(x) = (void *)-1;} while (0)

#define TAILQ_HEAD(name, type)					\
struct name {							\
	struct type *tqh_first; /* first element */		\
	struct type **tqh_last; /* addr of last next element */	\
}

#define TAILQ_ENTRY(type)						\
struct {								\
	struct type *tqe_next;	/* next element */			\
	struct type **tqe_prev;	/* address of previous next element */	\
}

#define TAILQ_FIRST(head)	((head)->tqh_first)

#define TAILQ_NEXT(elm, field)	((elm)->field.tqe_next)

#define TAILQ_INIT(head) do {				\
	TAILQ_FIRST((head)) = NULL;			\
	(head)->tqh_last = &TAILQ_FIRST((head));	\
} while (0)

#define TAILQ_INSERT_TAIL(head, elm, field) do {	\
	TAILQ_NEXT((elm), field) = NULL;		\
	(elm)->field.tqe_prev = (head)->tqh_last;	\
	*(head)->tqh_last = (elm);			\
	(head)->tqh_last = &TAILQ_NEXT((elm), field);	\
} while (0)

#define TAILQ_REMOVE(head, elm, field) do {						\
	if ((TAILQ_NEXT((elm), field)) != NULL)						\
		TAILQ_NEXT((elm), field)->field.tqe_prev = (elm)->field.tqe_prev;	\
	else										\
		(head)->tqh_last = (elm)->field.tqe_prev;				\
	*(elm)->field.tqe_prev = TAILQ_NEXT((elm), field);				\
	TRASHIT((elm)->field.tqe_next);							\
	TRASHIT((elm)->field.tqe_prev);							\
} while (0)
/* End various TAILQ macros */


struct memcache_server {
  /* The hostname of the server. */
  char *hostname;

  /* Port number of the host we're connecting to. */
  char *port;

  /* The file descriptor for this server */
  int fd;

  /* The file descriptor flags */
  int flags;

  /* The timeout for this server */
  struct timeval tv;

  /* Is this particular server active or not?
   *
   * 'd' == Down	Last request was unsuccessful
   * 'n' == No host	The hostname doesn't exist
   * 't' == Try		Haven't connected to it yet, will attempt
   * 'u' == Up		Has been connected to successfully
   */
  char active;

  /* A cached copy of the looked up host. */
  struct addrinfo *hostinfo;

  /* The number of addresses in the cached copy.  If there is more
   * than one per DNS entry (discouraged), we establish a connection
   * to them all. */
  u_int32_t num_addrs;

#ifdef HAVE_SELECT
  /* Reduces the amount of user time required when reading data. */
  fd_set fds;
  struct timeval select_tv;
#endif

  /* Read only. The allocated size of the buffer. */
  size_t size;

  /* Internal.  A buffer for data.  This value only changes on
   * mcRealloc(). */
  char *buf;

  /* Internal.  A cursor for where we are in the buffer.  This changes
   * every time we examine a bit of data in our buffer. */
  char *cur;

  /* Internal.  A pointer to where data should be appended with future
   * read(2) calls. */
  char *read_cur;

  /* Internal.  A pointer to the start of the current line in the
   * buffer. */
  char *start;

  /* Misc list bits */
  TAILQ_ENTRY(memcache_server) entries;
};


struct memcache_server_stats {
  pid_t pid;
  time_t uptime;
  time_t time;
  char *version;
  struct timeval rusage_user;
  struct timeval rusage_system;
  u_int32_t curr_items;
  u_int64_t total_items;
  u_int64_t bytes;
  u_int32_t curr_connections;
  u_int64_t total_connections;
  u_int32_t connection_structures;
  u_int64_t cmd_get;
  u_int64_t cmd_refresh;
  u_int64_t cmd_set;
  u_int64_t get_hits;
  u_int64_t get_misses;
  u_int64_t refresh_hits;
  u_int64_t refresh_misses;
  u_int64_t bytes_read;
  u_int64_t bytes_written;
  u_int64_t limit_maxbytes;
};


/* struct memcache.  Any of the bits that are commented as "Internal"
 * should not be twiddled with, ever.  The misc member can be used by
 * applications and is *never* touched/accessed by memcache(3).  Its
 * primary purpose is to aid in embedding memcache(3) in other
 * programming languages. */
struct memcache {
  /* The default timeout for all servers. */
  struct timeval tv;

  /* The number of servers in live_servers in the live_servers
   * list. */
  u_int32_t num_live_servers;

  /* A generic pointer not used by memcache(3), but can be used by
   * calling programs. */
  void *misc;

  /* An array of usable memcache_servers. */
  struct memcache_server **live_servers;

  /* The complete list of servers. */
  TAILQ_HEAD(memcache_server_list, memcache_server) server_list;
};


/* The memcache API allows callers to provide their own memory
 * allocation routines to aid in embedability with existing programs,
 * libraries, programming languages, and environments that have their
 * own memory handling routines. */
typedef void	 (*mcFreeFunc)(void *mem);
typedef void	*(*mcMallocFunc)(const size_t size);
typedef void	*(*mcReallocFunc)(void *mem, const size_t size);


/* This structure is only used to support multiple memory contexts.
 * By default, all libmemcache(3) API calls use the global memory
 * context, mcGlobalCtxt.  Under special circumstances (ie, Apache),
 * it is necessary to have multiple memory contexts that correspond
 * with their various different calling libraries (PHP, PostgreSQL,
 * APR, etc).  struct memcache_ctxt and its friends mcm_*() are used
 * to fulfill this goal.  Under most instances, programs, use of
 * mc_*() is sufficient, however there is nothing wrong with defining
 * your own memory context.
 *
 * mcMallocAtomic is used where applicable in the event that the
 * calling application makes use of a garbage collection mechanism
 * (ie, Boehm).  In non-GC'ed environments, this should be set to the
 * same things as mcMalloc. */
struct memcache_ctxt {
  /* Memory context function pointers. */
  mcFreeFunc	mcFree;
  mcMallocFunc	mcMalloc;
  mcMallocFunc	mcMallocAtomic;
  mcReallocFunc	mcRealloc;
};


struct memcache_res {
  const char *key;	/* key */
  size_t len;		/* length of key */
  u_int32_t hash;	/* hash of the key */
  void *val;		/* the value */
  size_t bytes;		/* length of val */

  /* If size is zero (default), the memory for val is automatically
   * allocated using mcMalloc(3).  If size is zero, _flags has its
   * MC_RES_FREE_ON_DELETE bit set.
   *
   * If size is non-zero, libmemcache(3) assumes that the caller has
   * set val to an available portion of memory that is size bytes
   * long.  libmemcache(3) will only populate val with as many bytes
   * as are specified by size (ie, it will trim the value in order to
   * fit into val). If size is non-zero, _flags has its
   * MC_RES_NO_FREE_ON_DELETE bit set by default. */
  size_t size;

  /* A generic pointer not used by memcache(3), but can be used by
   * calling programs. */
  void *misc;

  TAILQ_ENTRY(memcache_res) entries;

  /* This is the client supplied flags.  Please note, this flags is
   * very different than _flags (_flags is an internal bit and
   * shouldn't be read/changed, etc). */
  u_int16_t flags;

  /* If _flags has 0x01 set, val will be free(3)'ed on when this
   * struct is cleaned up via mc_res_free() or the request is cleaned
   * up via mc_req_free().
   *
   * If _flags has is 0x02 set, val will not be free(3)'ed when this
   * response or its parent request are cleaned up.
   *
   * Note: Use mc_res_free_on_delete() to set the "free on delete"
   * bits. */
  char _flags;
};

struct memcache_req {
  /* A generic pointer not used by memcache(3), but can be used by
   * calling programs. */
  void *misc;

  TAILQ_HEAD(memcache_res_list, memcache_res) query;
  TAILQ_HEAD(memcache_res_cb_list, memcache_res_cb) cb;
  u_int16_t num_keys;
};


/* Call back interface bits.  libmemcache(3) offers a callback
 * mechanism wherein many get requests can be lumped together into a
 * single get request.  After a response has been read from the
 * server, the callbacks are executed.
 *
 * Example:
 *
 * static void my_callback_func(MCM_CALLBACK_SIG);
 * static void
 * my_callback_func(MCM_CALLBACK_FUNC) {
 *   struct my_struct *ptr = (struct my_struct *)MCM_CALLBACK_PTR;
 *   struct memcache_ctxt *ctxt = MCM_CALLBACK_CTXT;
 *   struct memcache_res *res = MCM_CALLBACK_RES;
 *   ...
 * }
 *
 * and callbacks are registered like:
 *
 * struct memcache_req *req = mc_req_new();
 * struct memcache_res *res = mc_req_add(req, key, key_len);
 * mc_res_register_fetch_cb(req, res, my_callback_func, NULL);
 *
 * or, if you want to pass an optional void * pointer:
 *
 * struct my_struct *ptr;
 * struct memcache_res_cb *cb = mc_res_register_fetch_cb(req, res, my_callback_func, ptr);
 *
 * Then call mc_get() like normal whenever and your callback will be
 * executed.  Ex:
 *
 * mc_get(req);
 *
 * Function authors MUST use the following macros instead of
 * explicitly defining the various struct members.  These MD5s *will*
 * change every release to ensure developers (ie, YOU!) use these
 * macros.  Consider yourself warned. */
#define MCM_CALLBACK_CTXT	a8b9d8a801b46af93a5ce67f0a1be74d
#define MCM_CALLBACK_PTR	e19a47c80dcf9388187eccb589f2e72c
#define MCM_CALLBACK_RES	f7282cb0f3c3f2ef3242b9ae28117146

/* Function signature and arguments */
#define MCM_CALLBACK_FUNC	const struct memcache_ctxt *MCM_CALLBACK_CTXT, struct memcache_res *MCM_CALLBACK_RES, void *MCM_CALLBACK_PTR
#define MCM_CALLBACK_SIG	const struct memcache_ctxt *, struct memcache_res *, void *

typedef void (*mcResCallback)(MCM_CALLBACK_FUNC);
struct memcache_res_cb {
  const struct memcache_ctxt *ctxt;
  struct memcache_req *req;
  struct memcache_res *res;
  mcResCallback cb;
  void *misc;
  TAILQ_ENTRY(memcache_res_cb) entries;
};


/* Adds a given key to the cache */
int			 mc_add(struct memcache *mc,
				const char *key, const size_t key_len,
				const void *val, const size_t bytes,
				const time_t expire, const u_int16_t flags);

/* Gets the value from memcache and allocates the data for the caller.
 * It is the caller's responsibility to free the returned value.
 * mc_get() is the preferred interface, however. */
void			mc_aget(struct memcache *mc, const char *key, const size_t len, void**, u_int32_t*);

/* Gets the value from memcache and allocates the data for the caller.
 * It is the caller's responsibility to free the returned value.
 * mc_refresh() is the preferred interface, however. */
void			*mc_arefresh(struct memcache *mc, const char *key, const size_t len);

/* Decrements a given key */
u_int32_t		 mc_decr(struct memcache *mc, const char *key, const size_t key_len, const u_int32_t val);

/* Deletes a given key */
int			 mc_delete(struct memcache *mc, const char *key, const size_t key_len, const time_t hold);

/* Flushes all keys on a given server */
int			 mc_flush(struct memcache *mc, struct memcache_server *ms);

/* Flushes all keys on all servers */
int			 mc_flush_all(struct memcache *mc);

/* cleans up a memcache object. */
void			 mc_free(struct memcache *mc);

/* mc_get() is the preferred method of accessing memcache.  It
 * accepts multiple keys and lets a user (should they so choose)
 * perform memory caching to reduce the number of mcMalloc(3) calls
 * makes. */
void			 mc_get(struct memcache *mc, struct memcache_req *req);

/* Generates a hash value from a given key */
u_int32_t		 mc_hash_key(const char *key, const size_t len);

/* Increments a given key */
u_int32_t		 mc_incr(struct memcache *mc, const char *key, const size_t key_len, const u_int32_t val);

/* Allocates a new memcache object */
struct memcache	*mc_new(void);

/* mc_refresh() is the preferred method of accessing memcache.  It
 * accepts multiple keys and lets a user (should they so choose)
 * perform memory caching to reduce the number of mcMalloc(3) calls
 * makes.  mc_refresh() differs from mc_get() in that mc_refresh
 * updates the expiration time to be now + whatever the expiration for
 * the item was set to.  Sessions should use this as a way of noting
 * sessions expiring. */
void			 mc_refresh(struct memcache *mc, struct memcache_req *req);

/* Returns the release date for the library */
const u_int32_t		 mc_reldate(void);

/* Replaces a given key to the cache */
int			 mc_replace(struct memcache *mc,
				    const char *key, const size_t key_len,
				    const void *val, const size_t bytes,
				    const time_t expire, const u_int16_t flags);

/* Safely adds a key to a given request (the key is mc_strdup()'ed).
   See mc_req_add_ref() to avoid the mc_strdup(): note the warning in
   mc_req_add_ref() if you decide to use the other function. */
struct memcache_res	*mc_req_add(struct memcache_req *req, const char *key, const size_t len);

/* Adds a key to a given request. Stores a pointer to key.  key can
   not be modified or free(3)'ed until the passed in request and the
   returning struct memcache_res object is done being used. */
struct memcache_res	*mc_req_add_ref(struct memcache_req *req, const char *key, const size_t len);

/* Cleans up a given request and its subsequent responses.  If _flags
 * has the MC_RES_FREE_ON_DELETE bit set (default), it will clean up
 * the value too.  If _flags has MC_RES_NO_FREE_ON_DELETE set,
 * however, it is the caller's responsibility to free the value.  To
 * prevent double free(3) errors, if a value is free(3)'ed before
 * mc_req_free() is called, set val to NULL. */
void			 mc_req_free(struct memcache_req *req);

/* Allocates a new memcache request object. */
struct memcache_req	*mc_req_new(void);

/* Returns 1 if there has been an attempt by the library to fill the
 * struct's bits from a memcache server.  Returns 0 if there has been
 * no attempt to fill this struct's data. */
int			 mc_res_attempted(const struct memcache_res *res);

/* Tells the response object to free the allocated memory when it gets
 * cleaned up or to let the caller manage the memory. */
void			 mc_res_free_on_delete(struct memcache_res *res, const int free_on_delete);

/* Returns 1 if the given memcache_res struct contains data that was
 * found and if there has been an attempt at filling the data.  Return
 * 0 if no value was found for the key *or* there has been no attempt
 * at filling the data. */
int			 mc_res_found(const struct memcache_res *res);

/* Cleans up an individual response object.  Normally this is not
 * necessary as a call to mc_req_free() will clean up its response
 * objects. */
void			 mc_res_free(struct memcache_req *req, struct memcache_res *res);

/* Registers a callback with the request so that upon completion of a
 * fetch request, a callback gets executed. */
int			 mc_res_register_fetch_cb(struct memcache_req *req, struct memcache_res *res,
						  mcResCallback cb, void *misc);

/* Marks a given server as available again.  Does not reconnect to the
 * server, however. */
int			 mc_server_activate(struct memcache *mc, struct memcache_server *ms);

/* Mark all servers as available.  Does not connect to any servers,
 * but marks them as available. */
int			 mc_server_activate_all(struct memcache *mc);

/* Disconnects from a given server and marks it as down. */
void			 mc_server_deactivate(struct memcache *mc, struct memcache_server *ms);

/* Disconnects from a given server */
void			 mc_server_disconnect(const struct memcache *mc, struct memcache_server *ms);

/* Disconnects from all servers (leaves their active flag alone). */
void			 mc_server_disconnect_all(const struct memcache *mc);

/* When given a hash value, this function returns the appropriate
 * server to connect to in order to find the key. */
struct memcache_server	*mc_server_find(struct memcache *mc, const u_int32_t hash);

/* Adds a server to the list of available servers.  By default,
 * servers are assumed to be available.  Return codes:
 *
 * 0:	success
 * -1:	Unable to allocate a new server instance
 * -2:	Unable to strdup hostname
 * -3:	Unable to strdup port
 * -4:	Unable to Unable to resolve the host, server deactivated, but added to list
 * -5:	Unable to realloc(3) the server list, server list unchanged */
int			 mc_server_add(struct memcache *mc, const char *hostname, const char *port);
int			 mc_server_add2(struct memcache *mc,
					const char *hostname, const size_t hostname_len,
					const char *port, const size_t port_len);
int			 mc_server_add3(struct memcache *mc, struct memcache_server *ms);

/* Like the above, except hostport can be in the format:
 * "hostname:port" or just "hostname".  Ex: "127.0.0.1:11211" */
int			 mc_server_add4(struct memcache *mc, const char *hostport);

/* Free's the space from a struct memcache_server.  Use mc_free(3)
 * instead: only use this function if you really know what you're
 * doing. */
void			 mc_server_free(struct memcache_server *ms);

/* Creates a new server struct */
struct memcache_server	*mc_server_new(void);


/* Cleans up a given stat's object */
void			 mc_server_stats_free(struct memcache_server_stats *s);

/* Gets a stats object from the given server.  It is the caller's
 * responsibility to cleanup the resulting object via
 * mc_server_stats_free(). */
struct memcache_server_stats	*mc_server_stats(struct memcache *mc, struct memcache_server *ms);

/* Sets a given key */
int			 mc_set(struct memcache *mc,
				const char *key, const size_t key_len,
				const void *val, const size_t bytes,
				const time_t expire, const u_int16_t flags);

/* Creates a stats object for all available servers and returns the
 * cumulative stats.  Per host-specific data is generally the same as
 * the last server queried. */
struct memcache_server_stats	*mc_stats(struct memcache *mc);

/* memcache(3)'s strdup */
char			*mc_strdup(const char *str);

/* memcache(3)'s strndup: returns a dup of str up to len bytes long,
 * and pads the string with a null character (ie: len + 1). */
char			*mc_strndup(const char *str, const size_t len);


/* Sets the default timeout for new servers. */
void			 mc_timeout(struct memcache *mc, const int sec, const int usec);

/* Returns a numeric version of the library */
const u_int32_t		 mc_vernum(void);

/* Returns a string version of the library */
const char		*mc_version(void);



/* BEGIN memory management API functions and support for multiple
 * memory contexts.  Most users of libmemcache(3) should use the
 * functions prototyped above.  The below functions should be used by
 * advanced developers seeking a tad bit more control over their app's
 * use of libmemcache(3), authors of language extensions, or
 * developers who need to write modules that exist inside of a single
 * process with multiple memory allocation routines (ex: Apache and
 * PHP). */


/* The following two functions are used to setup additional memory
 * allocations for programs that use libmemcache(3), but are not using
 * the standard system memory management routines (ex: PostgreSQL,
 * Ruby, etc.) */
int	mcMemSetup(mcFreeFunc freeFunc, mcMallocFunc mallocFunc,
		   mcMallocFunc mallocAtomicFunc, mcReallocFunc reallocFunc);
int	mcMemGet(mcFreeFunc *freeFunc, mcMallocFunc *mallocFunc,
		 mcMallocFunc *mallocAtomicFunc, mcReallocFunc *reallocFunc);


/* From here on out, the API assumes callers are providing a valid
 * memory context.  This allows multiple memory contexts to exist
 * inside the same process.  Very handy for PHP/Apache/APR authors, or
 * developers in similar situations.  For maximum portability and
 * embedability, use of the mcm_*() functions is *strongly*
 * encouraged. */

/* Creates a new memory context from scratch: should be sufficient for
 * most applications. */
struct memcache_ctxt	*mcMemNewCtxt(mcFreeFunc freeFunc, mcMallocFunc mallocFunc,
				      mcMallocFunc mallocAtomicFunc, mcReallocFunc reallocFunc);

/* Free's a given memcache context */
void			 mcMemFreeCtxt(struct memcache_ctxt *ctxt);

/* Safely assigns the various function pointers to the passed in
 * memory context.  Only needed when an application needs to change
 * its memory allocation routines (not sure why this would ever need
 * to happen, to be honest). */
int			 mcMemSetupCtxt(struct memcache_ctxt *ctxt, mcFreeFunc freeFunc,
					mcMallocFunc mallocFunc, mcMallocFunc mallocAtomicFunc,
					mcReallocFunc reallocFunc);

/* Functions from here to the bottom of the section behave identically
 * to the above functions, except they have one additional argument, a
 * struct memcache_ctxt pointer.  See above for documentation. */
int			 mcm_add(const struct memcache_ctxt *ctxt, struct memcache *mc,
				 const char *key, const size_t key_len,
				 const void *val, const size_t bytes,
				 const time_t expire, const u_int16_t flags);
void			mcm_aget(const struct memcache_ctxt *ctxt, struct memcache *mc,
				 const char *key, const size_t len, void**, u_int32_t*);
void			*mcm_arefresh(const struct memcache_ctxt *ctxt, struct memcache *mc,
				      const char *key, const size_t len);
u_int32_t		 mcm_decr(const struct memcache_ctxt *ctxt, struct memcache *mc,
				  const char *key, const size_t key_len, const u_int32_t val);
int			 mcm_delete(const struct memcache_ctxt *ctxt, struct memcache *mc,
				    const char *key, const size_t key_len, const time_t hold);
int			 mcm_flush(const struct memcache_ctxt *ctxt, struct memcache *mc,
				   struct memcache_server *ms);
int			 mcm_flush_all(const struct memcache_ctxt *ctxt, struct memcache *mc);
void			 mcm_free(const struct memcache_ctxt *ctxt, struct memcache *mc);
void			 mcm_get(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_req *req);
u_int32_t		 mcm_hash_key(const struct memcache_ctxt *ctxt, const char *key, const size_t len);
u_int32_t		 mcm_incr(const struct memcache_ctxt *ctxt, struct memcache *mc,
				  const char *key, const size_t key_len, const u_int32_t val);
struct memcache		*mcm_new(const struct memcache_ctxt *ctxt);
void			 mcm_refresh(const struct memcache_ctxt *ctxt, struct memcache *mc,
				     struct memcache_req *req);
const u_int32_t		 mcm_reldate(const struct memcache_ctxt *ctxt);
int			 mcm_replace(const struct memcache_ctxt *ctxt, struct memcache *mc,
				     const char *key, const size_t key_len,
				     const void *val, const size_t bytes,
				     const time_t expire, const u_int16_t flags);
struct memcache_res	*mcm_req_add(const struct memcache_ctxt *ctxt, struct memcache_req *req,
				     const char *key, const size_t len);
struct memcache_res	*mcm_req_add_ref(const struct memcache_ctxt *ctxt, struct memcache_req *req,
					 const char *key, const size_t len);
void			 mcm_req_free(const struct memcache_ctxt *ctxt, struct memcache_req *req);
struct memcache_req	*mcm_req_new(const struct memcache_ctxt *ctxt);
int			 mcm_res_attempted(const struct memcache_ctxt *ctxt, const struct memcache_res *res);
int			 mcm_res_found(const struct memcache_ctxt *ctxt, const struct memcache_res *res);
void			 mcm_res_free(const struct memcache_ctxt *ctxt, struct memcache_req *req,
				      struct memcache_res *res);
void			 mcm_res_free_on_delete(const struct memcache_ctxt *ctxt, struct memcache_res *res,
						const int free_on_delete);
int			 mcm_res_register_fetch_cb(struct memcache_ctxt *ctxt, struct memcache_req *req,
						   struct memcache_res *res, mcResCallback callback, void *misc);
int			 mcm_server_activate(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_server *ms);
int			 mcm_server_activate_all(const struct memcache_ctxt *ctxt, struct memcache *mc);
int			 mcm_server_add(const struct memcache_ctxt *ctxt, struct memcache *mc,
					const char *hostname, const char *port);
int			 mcm_server_add2(const struct memcache_ctxt *ctxt, struct memcache *mc,
					 const char *hostname, const size_t hostname_len,
					 const char *port, const size_t port_len);
int			 mcm_server_add3(const struct memcache_ctxt *ctxt, struct memcache *mc,
					 struct memcache_server *ms);
int			 mcm_server_add4(const struct memcache_ctxt *ctxt, struct memcache *mc,
					 const char *hostport);
void			 mcm_server_deactivate(const struct memcache_ctxt *ctxt, struct memcache *mc,
					       struct memcache_server *ms);
void			 mcm_server_disconnect(const struct memcache_ctxt *ctxt, const struct memcache *mc,
					       struct memcache_server *ms);
void			 mcm_server_disconnect_all(const struct memcache_ctxt *ctxt, const struct memcache *mc);
struct memcache_server	*mcm_server_find(const struct memcache_ctxt *ctxt,
					 struct memcache *mc, const u_int32_t hash);
void			 mcm_server_free(const struct memcache_ctxt *ctxt, struct memcache_server *ms);
struct memcache_server	*mcm_server_new(const struct memcache_ctxt *ctxt);
void			 mcm_server_stats_free(const struct memcache_ctxt *ctxt, struct memcache_server_stats *s);
struct memcache_server_stats	*mcm_server_stats(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_server *ms);
int			 mcm_set(const struct memcache_ctxt *ctxt, struct memcache *mc,
				 const char *key, const size_t key_len,
				 const void *val, const size_t bytes,
				 const time_t expire, const u_int16_t flags);
struct memcache_server_stats	*mcm_stats(const struct memcache_ctxt *ctxt, struct memcache *mc);
char			*mcm_strdup(const struct memcache_ctxt *ctxt, const char *str);
char			*mcm_strndup(const struct memcache_ctxt *ctxt, const char *str, const size_t len);
void			 mcm_timeout(const struct memcache_ctxt *ctxt, struct memcache *mc,
				     const int sec, const int usec);
const u_int32_t		 mcm_vernum(const struct memcache_ctxt *ctxt);
const char		*mcm_version(const struct memcache_ctxt *ctxt);
/* END memory management API functions */


/* APIs that should be implemented: */

/* Resets all DNS entries */
void mc_server_reset_all_dns(struct memcache *mc);

/* Resets only one host's DNS cache */
void mc_server_reset_dns(struct memcache *mc, const char *hostname, const int port);

#ifdef TCP_NODELAY
/* Enable/disable TCP_NODELAY */
void mc_nodelay_enable(struct memcache *mc, const int enable);

/* Enable/disable TCP_NODELAY for a given server */
void mc_server_nodelay_enable(struct memcache_server *ms, const int enable);
#endif

/* Set the timeout on a per server basis */
void mc_server_timeout(struct memcache_server *ms, const int sec, const int usec);

/* Set the number of seconds you're willing to wait in total for a
 * response. ??? */

#ifdef __cplusplus
}
#endif

#endif
