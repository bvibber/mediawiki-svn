/* 
 * This is a slightly modified version of Sean's original libmemcache.
 * All of my changes are placed in the public domain.
 * Original copyright header follows.
 *
 * @(#) $Header$
 */

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
 * $Nexadesic: src/lib/libmemcache/memcache.c,v 1.36 2005/01/24 07:21:50 sean Exp $
 */

/* The crc32 functions and data was originally written by Spencer
 * Garrett <srg@quick.com> and was cleaned from the PostgreSQL source
 * tree via the files contrib/ltree/crc32.[ch].  No license was
 * included, therefore it is assumed that this code is public
 * domain.  Attribution still noted. */

#include <ctype.h>
#include <err.h>
#include <string.h>
#include <strings.h>
#include <stdio.h>
#include <stdlib.h>
#include <sysexits.h>
#include <errno.h>
#include <sys/types.h>
#include <sys/time.h>
#include <sys/socket.h>
#include <sys/uio.h>
#include <netdb.h>
#include <netinet/in.h>
#include <netinet/tcp.h>
#include <unistd.h>
#include <fcntl.h>

#include "l_memcache.h"

#ifdef USE_PHP_HASH
# include <openssl/md5.h>
#endif

/* Prototypes for static functions that are mcm_*() safe, but don't
 * require a memory context. */
static void			 mcm_reset_buf(struct memcache_server *ms);
static void			 mcm_server_block(struct memcache_server *ms, const int block);
static int			 mcm_server_connect(struct memcache *mc, struct memcache_server *ms);
static void			 mcm_server_init(const struct memcache_ctxt *ctxt, struct memcache_server *ms);
static int			 mcm_server_resolve(struct memcache_server *ms);


/* Prototypes for static functions that require a memory context */
static u_int32_t		 mcm_atomic_cmd(const struct memcache_ctxt *ctxt, struct memcache *mc,
						const char *cmd, const size_t cmd_len,
						const char *key, const size_t key_len, const u_int32_t val);
static void			 mcm_fetch_cmd(const struct memcache_ctxt *ctxt, struct memcache *mc,
					       struct memcache_req *req, const char *cmd, const size_t cmd_len);
static char			*mcm_get_line(const struct memcache_ctxt *ctxt, struct memcache *mc,
					      struct memcache_server *ms);
static void			 mcm_retrieve_data(const struct memcache_ctxt *ctxt, struct memcache_req *req, struct memcache *mc, struct memcache_server *ms);
static void			 mcm_res_cb_free(struct memcache_req *req, struct memcache_res_cb *cb);
static struct memcache_res	*mcm_res_new(const struct memcache_ctxt *ctxt);
static struct memcache_server_stats	*mcm_server_stats_new(const struct memcache_ctxt *ctxt);
static int			 mcm_storage_cmd(const struct memcache_ctxt *ctxt, struct memcache *mc,
						 const char *cmd, const size_t cmd_len,
						 const char *key, const size_t key_len,
						 const void *val, const size_t bytes,
						 const time_t expire, const u_int16_t flags);


#ifdef __STRICT_ANSI__
long long strtoll(const char *, char **, int);
#endif

/* This is kinda ugly, but, it saves on some warnings and a tad of
 * stack space across the library. Note, remember strlen(3) does not
 * include the trailing null character, but sizeoof() does, so when
 * computing the sizeof() commands, subtract one from its return. */
static const char	str_add_cmd[] = "add ";
static const size_t	str_add_len = MCM_CSTRLEN(str_add_cmd);
static const char	str_decr_cmd[] = "decr ";
static const size_t	str_decr_len = MCM_CSTRLEN(str_decr_cmd);
static const char	str_delete_cmd[] = "delete ";
static const size_t	str_delete_len = MCM_CSTRLEN(str_delete_cmd);
static const char	str_endl[] = "\r\n";
static const size_t	str_endl_len = MCM_CSTRLEN(str_endl);
static const char	str_get_cmd[] = "get ";
static const size_t	str_get_len = MCM_CSTRLEN(str_get_cmd);
static const char	str_incr_cmd[] = "incr ";
static const size_t	str_incr_len = MCM_CSTRLEN(str_incr_cmd);
static const char	str_refresh_cmd[] = "refresh ";
static const size_t	str_refresh_len = MCM_CSTRLEN(str_refresh_cmd);
static const char	str_replace_cmd[] = "replace ";
static const size_t	str_replace_len = MCM_CSTRLEN(str_replace_cmd);
static const char	str_set_cmd[] = "set ";
static const size_t	str_set_len = MCM_CSTRLEN(str_set_cmd);
static const char	str_space[] = " ";
static const size_t	str_space_len = MCM_CSTRLEN(str_space);


/* #ifdef HAVE_KQUEUE */
/* /\* kqueue(2) is the preferred method for testing to see if a file */
/*  * descriptor has data on it. *\/ */
/* #else */
/* # ifdef HAVE_POLL */
/* /\* poll(2) is better than select(2), but still sucks*\/ */
/* # else */
/* #  ifdef HAVE_SELECT */
/* /\* If we have to, we'll use select(2). *\/ */
/* #  endif */
/* # endif */
/* #endif */

/* Set the default memory handling routines to be system defaults. */
static struct memcache_ctxt mcGlobalCtxt = {
  (mcFreeFunc)free,
  (mcMallocFunc)malloc,
  (mcMallocFunc)malloc,
  (mcReallocFunc)realloc
};


int
mc_add(struct memcache *mc,
       const char *key, const size_t key_len,
       const void *val, const size_t bytes,
       const time_t expire, const u_int16_t flags) {
  return mcm_storage_cmd(&mcGlobalCtxt, mc, str_add_cmd, str_add_len, key, key_len, val, bytes, expire, flags);
}


void
mc_aget(struct memcache *mc, const char *key, const size_t len, void **value, u_int32_t *value_len) {
  mcm_aget(&mcGlobalCtxt, mc, key, len, value, value_len);
}


void *
mc_arefresh(struct memcache *mc, const char *key, const size_t len) {
  return mcm_arefresh(&mcGlobalCtxt, mc, key, len);
}


u_int32_t
mc_decr(struct memcache *mc, const char *key, const size_t key_len, const u_int32_t val) {
  return mcm_atomic_cmd(&mcGlobalCtxt, mc, str_decr_cmd, str_decr_len, key, key_len, val);
}


int
mc_delete(struct memcache *mc, const char *key, const size_t key_len, const time_t hold) {
  return mcm_delete(&mcGlobalCtxt, mc, key, key_len, hold);
}


int
mc_flush(struct memcache *mc, struct memcache_server *ms) {
  return mcm_flush(&mcGlobalCtxt, mc, ms);
}


int
mc_flush_all(struct memcache *mc) {
  return mcm_flush_all(&mcGlobalCtxt, mc);
}


void
mc_free(struct memcache *mc) {
  mcm_free(&mcGlobalCtxt, mc);
}


void
mc_get(struct memcache *mc, struct memcache_req *req) {
  mcm_fetch_cmd(&mcGlobalCtxt, mc, req, str_get_cmd, str_get_len);
}


u_int32_t
mc_hash_key(const char *key, const size_t len) {
  return mcm_hash_key(&mcGlobalCtxt, key, len);
}


u_int32_t
mc_incr(struct memcache *mc, const char *key, const size_t key_len, const u_int32_t val) {
  return mcm_atomic_cmd(&mcGlobalCtxt, mc, str_incr_cmd, str_incr_len, key, key_len, val);
}


struct memcache *
mc_new(void) {
  return mcm_new(&mcGlobalCtxt);
}


void
mc_refresh(struct memcache *mc, struct memcache_req *req) {
  mcm_fetch_cmd(&mcGlobalCtxt, mc, req, str_refresh_cmd, str_refresh_len);
}


const u_int32_t
mc_reldate(void) {
  return MEMCACHE_RELDATE;
}


int
mc_replace(struct memcache *mc,
	   const char *key, const size_t key_len,
	   const void *val, const size_t bytes,
	   const time_t expire, const u_int16_t flags) {
  return mcm_storage_cmd(&mcGlobalCtxt, mc, str_replace_cmd, str_replace_len, key, key_len, val, bytes, expire, flags);
}


struct memcache_res *
mc_req_add(struct memcache_req *req, const char *key, const size_t len) {
  return mcm_req_add(&mcGlobalCtxt, req, key, len);
}


struct memcache_res *
mc_req_add_ref(struct memcache_req *req, const char *key, const size_t len) {
  return mcm_req_add_ref(&mcGlobalCtxt, req, key, len);
}


void
mc_req_free(struct memcache_req *req) {
  mcm_req_free(&mcGlobalCtxt, req);
}


struct memcache_req *
mc_req_new(void) {
  return mcm_req_new(&mcGlobalCtxt);
}


int
mc_res_attempted(const struct memcache_res *res) {
  return mcm_res_attempted(&mcGlobalCtxt, res);
}


int
mc_res_found(const struct memcache_res *res) {
  return mcm_res_found(&mcGlobalCtxt, res);
}


void
mc_res_free(struct memcache_req *req, struct memcache_res *res) {
  mcm_res_free(&mcGlobalCtxt, req, res);
}


void
mc_res_free_on_delete(struct memcache_res *res, const int fod) {
  mcm_res_free_on_delete(&mcGlobalCtxt, res, fod);
}


int
mc_res_register_fetch_cb(struct memcache_req *req, struct memcache_res *res,
			 mcResCallback cb, void *misc) {
  return mcm_res_register_fetch_cb(&mcGlobalCtxt, req, res, cb, misc);
}


int
mc_server_activate(struct memcache *mc, struct memcache_server *ms) {
  return mcm_server_activate(&mcGlobalCtxt, mc, ms);
}


int
mc_server_activate_all(struct memcache *mc) {
  return mcm_server_activate_all(&mcGlobalCtxt, mc);
}


int
mc_server_add(struct memcache *mc, const char *hostname, const char *port) {
  return mcm_server_add2(&mcGlobalCtxt, mc, hostname, strlen(hostname), port, strlen(port));
}


int
mc_server_add2(struct memcache *mc, const char *hostname, const size_t hostname_len,
	       const char *port, const size_t port_len) {
  return mcm_server_add2(&mcGlobalCtxt, mc, hostname, hostname_len, port, port_len);
}


int
mc_server_add3(struct memcache *mc, struct memcache_server *ms) {
  return mcm_server_add3(&mcGlobalCtxt, mc, ms);
}


int
mc_server_add4(struct memcache *mc, const char *hostport) {
  return mcm_server_add4(&mcGlobalCtxt, mc, hostport);
}


void
mc_server_deactivate(struct memcache *mc, struct memcache_server *ms) {
  mcm_server_deactivate(&mcGlobalCtxt, mc, ms);
}


void
mc_server_disconnect(const struct memcache *mc, struct memcache_server *ms) {
  mcm_server_disconnect(&mcGlobalCtxt, mc, ms);
}


void
mc_server_disconnect_all(const struct memcache *mc) {
  mcm_server_disconnect_all(&mcGlobalCtxt, mc);
}


struct memcache_server *
mc_server_find(struct memcache *mc, const u_int32_t hash) {
  return mcm_server_find(&mcGlobalCtxt, mc, hash);
}


void
mc_server_free(struct memcache_server *ms) {
  mcm_server_free(&mcGlobalCtxt, ms);
}


struct memcache_server *
mc_server_new(void) {
  return mcm_server_new(&mcGlobalCtxt);
}


struct memcache_server_stats *
mc_server_stats(struct memcache *mc, struct memcache_server *ms) {
  return mcm_server_stats(&mcGlobalCtxt, mc, ms);
}


void
mc_server_stats_free(struct memcache_server_stats *s) {
  mcm_server_stats_free(&mcGlobalCtxt, s);
}


int
mc_set(struct memcache *mc,
       const char *key, const size_t key_len,
       const void *val, const size_t bytes,
       const time_t expire, const u_int16_t flags) {
  return mcm_storage_cmd(&mcGlobalCtxt, mc, str_set_cmd, str_set_len, key, key_len, val, bytes, expire, flags);
}


struct memcache_server_stats *
mc_stats(struct memcache *mc) {
  return mcm_stats(&mcGlobalCtxt, mc);
}


char *
mc_strdup(const char *str) {
  return mcm_strndup(&mcGlobalCtxt, str, strlen(str));
}


char *
mc_strndup(const char *str, const size_t len) {
  return mcm_strndup(&mcGlobalCtxt, str, len);
}


void
mc_timeout(struct memcache *mc, const int sec, const int msec) {
  mcm_timeout(&mcGlobalCtxt, mc, sec, msec);
}


const u_int32_t
mc_vernum(void) {
  return MEMCACHE_VERNUM;
}


const char *
mc_version(void) {
  return MEMCACHE_VER;
}
/* END OF THE SINGLE MEMORY CONTEXT API CALLS (ie: mc_*()) */


/* BEGIN MULTI-MEMORY CONTEXT API (ie: mcm_*())  */
int
mcm_add(const struct memcache_ctxt *ctxt, struct memcache *mc,
	const char *key, const size_t key_len,
	const void *val, const size_t bytes,
	const time_t expire, const u_int16_t flags) {
  return mcm_storage_cmd(ctxt, mc, str_add_cmd, str_add_len, key, key_len, val, bytes, expire, flags);
}


/* Issues a "get" command to the memcache server that should contain
 * the key.  The result is mcMalloc(3)'ed and it is assumed that the
 * caller is required to mcFree(3) the memory. */
void
mcm_aget(const struct memcache_ctxt *ctxt, struct memcache *mc, const char *key, const size_t len, void **value, u_int32_t *value_len) {
  struct memcache_req *req;
  struct memcache_res *res;
  void *ret;

  req = mcm_req_new(ctxt);
  res = mcm_req_add_ref(ctxt, req, key, len);
  mcm_res_free_on_delete(ctxt, res, 0);
  mcm_get(ctxt, mc, req);
  *value = res->val;
  *value_len = res->bytes;
  mcm_req_free(ctxt, req);
}


/* Issues a "refresh" command to the memcache server that should
 * contain the key.  The result is mcMalloc(3)'ed and it is assumed
 * that the caller is required to mcFree(3) the memory. */
void *
mcm_arefresh(const struct memcache_ctxt *ctxt, struct memcache *mc, const char *key, const size_t len) {
  struct memcache_req *req;
  struct memcache_res *res;
  void *ret;

  req = mcm_req_new(ctxt);
  res = mcm_req_add_ref(ctxt, req, key, len);
  mcm_res_free_on_delete(ctxt, res, 0);
  mcm_refresh(ctxt, mc, req);
  ret = res->val;
  mcm_req_free(ctxt, req);
  return ret;
}


static u_int32_t
mcm_atomic_cmd(const struct memcache_ctxt *ctxt, struct memcache *mc,
	       const char *cmd, const size_t cmd_len,
	      const char *key, const size_t key_len, const u_int32_t val) {
  struct memcache_server *ms;
  u_int32_t hash;
  char *cp, *cur;
  size_t buf_left, i;
  struct iovec av[5];
  u_int32_t ret;

  /* If we have only one server, don't bother actually hashing. */
  if (mc->num_live_servers == 1)
    hash = 42;
  else
    hash = mcm_hash_key(ctxt, key, key_len);

  ms = mcm_server_find(ctxt, mc, hash);
  if (ms == NULL) {
    warnx("%s:%u\tUnable to find a valid server", __FILE__, __LINE__);
    return 0;
  }

  if (mcm_server_connect(mc, ms) == -1)
    return 0;

  mcm_reset_buf(ms);
  cur = ms->buf;
  buf_left = ms->size;

  av[0].iov_base = cmd;
  av[0].iov_len = cmd_len;
  av[1].iov_base = key;
  av[1].iov_len = key_len;
  av[2].iov_base = str_space;
  av[2].iov_len = str_space_len;

  /* Convert the value to a string */
  i = (size_t)snprintf(cur, buf_left, "%u", val);
  if (i < 1)
    err(EX_SOFTWARE, "%s:%u\tsnprintf()", __FILE__, __LINE__);

  av[3].iov_base = cur;
  av[3].iov_len = i;
  av[4].iov_base = str_endl;
  av[4].iov_len = str_endl_len;

  if (writev(ms->fd, av, 5) < 0) {
    warn("%s:%u\twritev()", __FILE__, __LINE__);
    mcm_server_deactivate(ctxt, mc, ms);
    /* XXX Should we recursively attempt to try this query on the
     * remaining servers in the cluster if the writev() fails?
     * Eventually we'd fail once all servers were exhausted?  For now,
     * just fail and return 0. */
    return 0;
  }

  mcm_server_block(ms, 1);
  mcm_reset_buf(ms);
  cur = mcm_get_line(ctxt, mc, ms);
  if (cur != NULL && memcmp(cur, "NOT_FOUND", MCM_CSTRLEN("NOT_FOUND")) == 0) {
    mcm_server_block(ms, 0);
    return 0;
  } else if (cur == NULL) {
    return 0;
  }

  /* Try converting the value to an integer. If it succeeds, we've got
   * a winner. */
  ret = (u_int32_t)strtol(cur, &cp, 10);
  if (ret == 0 && (errno == EINVAL || errno == ERANGE))
    err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid value \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);

#ifdef PEDANTIC
  if (*cp != '\0')
    errx(EX_PROTOCOL, "%s:%u\tProtocol error: %u", __FILE__, __LINE__, *cp);
#endif

  return ret;
}


u_int32_t
mcm_decr(const struct memcache_ctxt *ctxt, struct memcache *mc, const char *key, const size_t key_len, const u_int32_t val) {
  return mcm_atomic_cmd(ctxt, mc, str_decr_cmd, str_decr_len, key, key_len, val);
}


int
mcm_delete(const struct memcache_ctxt *ctxt, struct memcache *mc,
	   const char *key, const size_t key_len, const time_t hold) {
  struct memcache_server *ms;
  u_int32_t hash;
  char *cur;
  size_t buf_left, i;
  struct iovec dv[5];

  /* If we have only one server, don't bother actually hashing. */
  if (mc->num_live_servers == 1)
    hash = 42;
  else
    hash = mcm_hash_key(ctxt, key, key_len);

  ms = mcm_server_find(ctxt, mc, hash);
  if (ms == NULL) {
    warnx("%s:%u\tUnable to find a valid server", __FILE__, __LINE__);
    return -1;
  }

  if (mcm_server_connect(mc, ms) == -1)
    return -2;

  mcm_reset_buf(ms);
  cur = ms->buf;
  buf_left = ms->size;

  dv[0].iov_base = str_delete_cmd;
  dv[0].iov_len = str_delete_len;
  dv[1].iov_base = key;
  dv[1].iov_len = key_len;
  dv[2].iov_base = str_space;
  dv[2].iov_len = str_space_len;

  /* Convert expiration time to a string */
  i = (size_t)snprintf(cur, buf_left, "%lu", hold);
  if (i < 1)
    err(EX_SOFTWARE, "%s:%u\tsnprintf()", __FILE__, __LINE__);

  dv[3].iov_base = cur;		/* Note where our flags string is located */
  dv[3].iov_len = i;
  dv[4].iov_base = str_endl;
  dv[4].iov_len = str_endl_len;

  if (writev(ms->fd, dv, 5) < 0) {
    warn("%s:%u\twritev()", __FILE__, __LINE__);
    mcm_server_deactivate(ctxt, mc, ms);
    /* XXX Should we recursively attempt to try this query on the
     * remaining servers in the cluster if the writev() fails?
     * Eventually we'd fail once all servers were exhausted?  For now,
     * just fail and return NULL. */
    return -3;
  }

  mcm_server_block(ms, 1);
  mcm_reset_buf(ms);
  cur = mcm_get_line(ctxt, mc, ms);
  if (cur != NULL && memcmp(cur, "DELETED", MCM_CSTRLEN("DELETED")) == 0) {
    mcm_server_block(ms, 0);
    return 0;
  } else if (cur != NULL && memcmp(cur, "NOT_FOUND", MCM_CSTRLEN("NOT_FOUND")) == 0) {
    mcm_server_block(ms, 0);
    return 1;
  } else {
    errx(EX_PROTOCOL, "%s:%u\tProtocol error", __FILE__, __LINE__);
  }
}


static void
mcm_fetch_cmd(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_req *req,
	      const char *cmd, const size_t cmd_len) {
  struct memcache_res *res;
  struct memcache_res_cb *cb;
  struct memcache_server *ms;
  struct iovec *rv;
  u_int32_t i, num_vec;

  if (req->num_keys == 0)
    return;

  /* Need two extra iovec structs, one for the command, one for the
   * trailing \r\n. */
  num_vec = 2 * req->num_keys + 1;
  rv = (struct iovec *)ctxt->mcMalloc(sizeof(struct iovec) * num_vec);
  rv[0].iov_base = cmd;
  rv[0].iov_len = cmd_len;
  for (i = 1, res = req->query.tqh_first; res != NULL; res = res->entries.tqe_next, i++) {
    /* If we have only one live server, don't waste CPU actually
       hashing. */
    if (mc->num_live_servers == 1)
      res->hash = 42;
    else
      res->hash = mcm_hash_key(ctxt, res->key, res->len);

    rv[i].iov_base = res->key;
    rv[i].iov_len = res->len;

    if (res->entries.tqe_next != NULL) {
      rv[++i].iov_base = str_space;
      rv[i].iov_len = 1;
    }

    /* While we're looping, might as well see if we should be auto
     * deleting any of these keys. */
    if ((res->_flags & (MCM_RES_FREE_ON_DELETE | MCM_RES_NO_FREE_ON_DELETE)) ==
	(MCM_RES_FREE_ON_DELETE | MCM_RES_FREE_ON_DELETE))
      mcm_res_free_on_delete(ctxt, res, (res->size > 0 ? 0 : 1));
  }
  rv[i].iov_base = str_endl;
  rv[i].iov_len = str_endl_len;

  /* XXX Yuk.  Right now I'm grabbing the hash from the first key and
   * using that, but that's dangerous in the event that the second key
   * in the request could be stored on a different server.  memcache
   * servers need the ability to query other servers on the users
   * behalf, IMHO. */
  ms = mcm_server_find(ctxt, mc, req->query.tqh_first->hash);
  if (ms == NULL) {
    warnx("%s:%u\tUnable to find a valid server", __FILE__, __LINE__);
    ctxt->mcFree(rv);
    return;
  }

  if (mcm_server_connect(mc, ms) == -1) {
    ctxt->mcFree(rv);
    return;
  }

  if (writev(ms->fd, rv, num_vec) < 0) {
    warn("%s:%u\twritev()", __FILE__, __LINE__);
    mcm_server_deactivate(ctxt, mc, ms);
    /* XXX Should we recursively attempt to try this query on the
     * remaining servers in the cluster if the writev() fails?
     * Eventually we'd fail once all servers were exhausted?  For now,
     * just fail and return NULL. */
    ctxt->mcFree(rv);
    return;
  }

  /* Switch to non-blocking io */
  mcm_server_block(ms, 1);

  /* Reset our buffer */
  mcm_reset_buf(ms);

  /* Grab data from the server */
  mcm_retrieve_data(ctxt, req, mc, ms);

  for (res = req->query.tqh_first; res != NULL; res = res->entries.tqe_next) {
    /* Set the attempted bit.  This is rather late in the process, but
     * setting the bit here allows for server failures to not count as
     * an attempt */
    res->_flags |= MCM_RES_ATTEMPTED;
  }

  /* Now that we've finished the IO, fire off any callbacks that are
   * registered. */
  for (cb = req->cb.tqh_first; cb != NULL; cb = cb->entries.tqe_next) {
    (*cb->cb)(cb->ctxt, cb->res, cb->misc);
  }

  ctxt->mcFree(rv);
  return;
}


int
mcm_flush(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_server *ms) {
  char *cur;

  if (mcm_server_connect(mc, ms) == -1)
    return -1;

  if (write(ms->fd, "flush_all\r\n", MCM_CSTRLEN("flush_all\r\n")) < 0) {
    warn("%s:%u\twrite()", __FILE__, __LINE__);
    mcm_server_deactivate(ctxt, mc, ms);
    return -2;
  }

  mcm_server_block(ms, 1);
  mcm_reset_buf(ms);
  cur = mcm_get_line(ctxt, mc, ms);
  if (cur != NULL && memcmp(cur, "OK", MCM_CSTRLEN("OK")) == 0) {
    mcm_server_block(ms, 0);
    return 0;
  } else {
    errx(EX_PROTOCOL, "%s:%u\tProtocol error", __FILE__, __LINE__);
  }
}


int
mcm_flush_all(const struct memcache_ctxt *ctxt, struct memcache *mc) {
  struct memcache_server *ms;
  int ret = 0,
    tret;

  for (ms = mc->server_list.tqh_first; ms != NULL; ms = ms->entries.tqe_next) {
    tret = mcm_flush(ctxt, mc, ms);

    /* Return the error code of the first non-zero value if there is
     * one.  Not sure if this is correct, but I don't have a better
     * idea right now. XXX */
    if (tret != 0 && ret == 0)
      ret = tret;
  }

  return ret;
}


void
mcm_free(const struct memcache_ctxt *ctxt, struct memcache *mc) {
  struct memcache_server *ms, *tms;

  if (mc == NULL)
    return;

  tms = mc->server_list.tqh_first;
  while(tms != NULL) {
    ms = tms;
    tms = ms->entries.tqe_next;

    mcm_server_free(ctxt, ms);
  }

  if (ms->size > 0)
    ctxt->mcFree(ms->buf);

  if (mc->live_servers != NULL) {
    ctxt->mcFree(mc->live_servers);
  }

  ctxt->mcFree(mc);
}


void
mcm_get(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_req *req) {
  mcm_fetch_cmd(ctxt, mc, req, str_get_cmd, str_get_len);
}


static char *
mcm_get_line(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_server *ms) {
  ssize_t rb;
  char *cp;
  char *new_start;
  size_t read_cur_offset;
#ifdef HAVE_SELECT
  int ret;
#endif

  if (ms->read_cur == NULL) {
    ms->read_cur = ms->start = ms->cur = ms->buf;

    try_read:
#ifdef HAVE_SELECT
    /* Before we read(2) anything, check to make sure there is data
     * available to be read(2).  No sense in wastefully calling
     * read(2) constantly in a loop. */
    ret = select(1, &ms->fds, NULL, NULL, &ms->select_tv);
    if (ret == -1)
      errx(EX_OSERR, "%s:%u\tselect()", __FILE__, __LINE__);
#endif
    rb = read(ms->fd, ms->read_cur, ms->size - (size_t)(ms->read_cur - ms->buf));
    switch(rb) {
    case -1:
      /* We're in non-blocking mode, don't abort because of EAGAIN or
       * EINTR */
      if (errno == EAGAIN || errno == EINTR)
	goto try_read;
      warn("%s:%u\tread() failed.\n", __FILE__, __LINE__);
      mcm_server_deactivate(ctxt, mc, ms);
      return NULL; /* Should we try again depending on errno? */
    case 0:
      /* Seems like an error to me if the server closes its connection
       * here.  deactivate the server instead of just closing it. */
      mcm_server_deactivate(ctxt, mc, ms);
      warnx("%s:%u\tServer unexpectedly closed connection.\n", __FILE__, __LINE__);
      return NULL;
    default:
      ms->read_cur += rb;
    }
    /* If we got the same amount of data as we said was max, then we
     * probably need to read(2) more data.  As such we will double
     * buffer size right now.  Worst case scenario we double it
     * without need, but we won't read more data until later on
     * anyway. */
    if (ms->size - (size_t)(ms->read_cur - ms->buf) == 0) {
      /* Store the difference between ms->read_cur and ms->buf so that
       * it can be restored after mcRealloc. */
      read_cur_offset = ms->read_cur - ms->buf;
      cp = (char *)ctxt->mcRealloc(ms->buf, ms->size * 2);
      if (cp == NULL) {
	/* No sense in continuing if we can't read(2) in a whole
	 * line. There are likely bigger problems on a system if its
	 * experiencing a problem like this. */
	warn("%s:%u\tmcRealloc()", __FILE__, __LINE__);
	return NULL;
      }

      /* Fix ms->read_cur from remalloc mangling. */
      ms->read_cur = cp + read_cur_offset;

      /* Fix the other pointers */
      ms->buf = ms->cur = ms->start = cp;

      /* Note the change in buffer size */
      ms->size *= 2;
    }

    /* Check if we are at the end of a response (assume \r\n will
     * guarantee this.  This is safe for all types of commands except
     * data retrieval (handled in another function), and the stats
     * command.  I'm not too worried about the stats command as it
     * isn't likely to be used in production systems, or at least not
     * often.
     *
     * But it should be understood that this check may tell us we got
     * all the data when we actually have not in the event that we
     * read only part of a stats command response but just happen to
     * be at the end of a line. */
    cp = ms->read_cur - MCM_CSTRLEN("\r\n");
    if(cp >= ms->buf && memcmp(cp, "\r\n", MCM_CSTRLEN("\r\n"))==0) {
      goto done_reading;
    }

    /* No acceptable end of command output was read, and there was no
     * error indicated.  Try to read more data we probably just didn't
     * get it all the first time through. */
    goto try_read;
  }
  done_reading:
  /* There should always be a newline in our response now.  Try to
   * find it so we can put a null in and return just the line to the
   * caller. */
  cp = memchr(ms->start, (int)'\n', ms->size - (size_t)(ms->read_cur - ms->buf));
  if (cp == NULL) {
    /* We suck.  Return null. */
    warnx("%s:%u\tProtocol violation, no \n anywhere in server response.\n", __FILE__, __LINE__);
    mcm_server_deactivate(ctxt, mc, ms);
    return NULL;
  }

#ifdef PEDANTIC
  /* At this point, we're guaranteed to have a complete line in the
   * buffer.  cp should be pointing to a newline character. */
  if (*cp != '\n') abort();

  /* Protocol check, make sure there's a carriage return before the
   * newline. */
  if (*(cp - 1) != '\r') {
    warnx("%s:%u\tProtocol violation, no \\r before the \\n", __FILE__, __LINE__);
    mcm_server_deactivate(ctxt, mc, ms);
    return NULL;
  }
#endif
  new_start = cp + 1; /* Advance the start of the next line */
  *(cp - 1) = '\0'; /* Add a null character */
  cp = ms->start;
  ms->start = new_start;

  /* Handy little debugging function: */
  /* warnx("Line reads: \"%.*s\"", (size_t)(ms->start - cp - 2), cp); */
  return cp;
}


static void
mcm_retrieve_data(const struct memcache_ctxt *ctxt, struct memcache_req *req,
		  struct memcache *mc, struct memcache_server *ms) {
  ssize_t rb;
  char *cp, *end;
  ssize_t len = 0;
  ssize_t bytes_read = 0;
  struct memcache_res *res = NULL;
  size_t bytes;
  size_t read_cur_offset;
  size_t cp_offset;
  u_int16_t flags;
  int ret;

  cp = ms->read_cur = ms->start = ms->cur = ms->buf;
read_more:
#ifdef HAVE_SELECT
  /* Before we read(2) anything, check to make sure there is data
   * available to be read(2).  No sense in wastefully calling read(2)
   * constantly in a loop. */
  ret = select(1, &ms->fds, NULL, NULL, &ms->select_tv);
  if (ret == -1)
    errx(EX_OSERR, "%s:%u\tselect()", __FILE__, __LINE__);
#endif
  rb = read(ms->fd, ms->read_cur, ms->size - (size_t)(ms->read_cur - ms->buf));
  switch (rb) {
  case -1:
    /* We're in non-blocking mode, don't abort because of EAGAIN or
     * EINTR */
    if (errno == EAGAIN || errno == EINTR)
      goto read_more;
    warn("%s:%u\tread() failed.\n", __FILE__, __LINE__);
    mcm_server_deactivate(ctxt, mc, ms);
    return; /* Should we try again depending on errno? */
  case 0:
    /* Seems like an error to me if the server closes its connection
     * here.  deactivate the server instead of just closing it. */
    mcm_server_deactivate(ctxt, mc, ms);
    warnx("%s:%u\tServer unexpectedly closed connection.\n", __FILE__, __LINE__);
    return;
  default:
    ms->read_cur += rb;
  }

  if (ms->size - (size_t)(ms->read_cur - ms->buf) == 0) {
    /* Store the difference between ms->read_cur and ms->buf so that
     * it can be restored after mcRealloc. */
    read_cur_offset = ms->read_cur - ms->buf;
    cp_offset = cp - ms->buf;
    ms->start = (char *)ctxt->mcRealloc(ms->buf, ms->size * 2);
    if (ms->start == NULL) {
      /* No sense in continuing if we can't read(2) in a whole
       * line. There are likely bigger problems on a system if its
       * experiencing a problem like this. */
      warn("%s:%u\tmcRealloc()", __FILE__, __LINE__);
      return;
    }

    /* Fix ms->read_cur and cp from remalloc mangling. */
    ms->read_cur = ms->start + read_cur_offset;
    cp = ms->start + cp_offset;

    /* Fix the other pointers */
    ms->buf = ms->cur = ms->start;

    /* Note the change in buffer size */
    ms->size *= 2;
  }

next_value:
  /* We got some data, try to figure out what it is. */
  if (len == 0) {
    /* We haven't yet read a "header" line VALUE ... .. ..\r\n This
     * should be one. Try to match it. */
    end = memchr(ms->start, (int)'\n', ms->size - (size_t)(ms->read_cur - ms->buf));
    if(end == NULL) {
      /* No \n yet... keep reading... */
      goto read_more;
    }

    if (memcmp(cp, "VALUE ", MCM_CSTRLEN("VALUE ")) == 0) {
      cp = &cp[MCM_CSTRLEN("VALUE ")];

      /* First assume the responses are in order as an optimization.
       * If this fails we will scan the whole list. */
      if (res != NULL && res->entries.tqe_next != NULL) {
	for (res = res->entries.tqe_next; res != NULL; res = res->entries.tqe_next) {
	  if ((size_t)(rb - (cp - ms->cur)) > res->len) {
	    if (memcmp(cp, res->key, res->len) == 0) {
	      break;
	    }
	  }
	}
      } else {
	for (res = req->query.tqh_first; res != NULL; res = res->entries.tqe_next) {
	  if((size_t)(rb - (cp - ms->cur)) > res->len) {
	    if(memcmp(cp, res->key, res->len) == 0) {
	      break;
	    }
	  }
	}
      }

      if (res == NULL) {
	warnx("%s:%u\tServer sent data for key not in request.", __FILE__, __LINE__);
	exit(1);
      }

      cp = &cp[res->len];
      end = ms->read_cur;

      flags = (u_int16_t)strtol(cp, &end, 10);
      if (flags == 0 && (errno == EINVAL || errno == ERANGE)) {
	warn("%s:%u\tstrtol(): invalid flags", __FILE__, __LINE__);
      }
      res->flags = flags;
      cp = end;

      bytes = (size_t)strtol(cp, &end, 10);
      if (bytes == 0 && (errno == EINVAL || errno == ERANGE)) {
	warn("%s:%u\tstrtol(): invalid bytes", __FILE__, __LINE__);
	mcm_server_deactivate(ctxt, mc, ms);
	return;
      }
      res->bytes = bytes;
      cp = end;
      len = bytes;
      bytes_read = 0;

      if (cp[0] == '\r' && cp[1] == '\n') {
	cp += 2;
      } else {
	warn("%s:%u\tprotocol error", __FILE__, __LINE__);
	mcm_server_deactivate(ctxt, mc, ms);
	return;
      }
    } else if (memcmp(cp, "END\r\n", MCM_CSTRLEN("END\r\n")) == 0) {
      return;
    }
  }

  bytes_read = ms->read_cur - cp;

  /* Check if we have read all the data the plus a \r\n */
  if (bytes_read >= len + 2) {
    res->_flags |= MCM_RES_FOUND;
    if (res->size == 0) {
      res->val = ctxt->mcMallocAtomic(res->bytes);
      if (res->val == NULL) {
	warn("%s:%u\t memory was not allocated for res->val.", __FILE__, __LINE__);
	mcm_server_deactivate(ctxt, mc, ms);
	return;
      }
      res->size = res->bytes;
    }

    if (res->size >= res->bytes) {
      /* We have room for the whole value.  Read it. */
      memcpy(res->val, cp, res->bytes);
    } else {
      /* Part of the value will be truncated. */
      memcpy(res->val, cp, res->size);
    }
    cp += bytes;
    len = 0;
    bytes_read = 0;
    if (cp < ms->read_cur - 2 && cp[0] == '\r' && cp[1] =='\n') {
      cp += 2;
    } else {
      warn("%s:%u\tprotocol error", __FILE__, __LINE__);
      mcm_server_deactivate(ctxt, mc, ms);
      return;
    }

    if ((size_t)ms->read_cur-(size_t)cp >= MCM_CSTRLEN("END\r\n") && memcmp(cp, "END\r\n", MCM_CSTRLEN("END\r\n")) == 0) {
      /* Done reading data. */
      return;
    } else {
      goto next_value;
    }
  } else if(bytes_read <= len + 2) {
    goto read_more;
  }
}


#ifdef USE_CRC32_HASH
#include "crc32_table.h"
#endif /* USE_CRC32_HASH */


u_int32_t
mcm_hash_key(const struct memcache_ctxt *ctxt, const char *key, const size_t len) {
#ifdef USE_CRC32_HASH
  size_t i;
  u_int32_t crc;

  crc = ~0;

  for (i = 0; i < len; i++)
    crc = (crc >> 8) ^ crc32tab[(crc ^ (key[i])) & 0xff];

  return((~crc >> 16) & 0x7fff);
#else
# ifdef USE_PERL_HASH
  u_int32_t h, i;
  char *p;

  i = len;	/* Work back through the key length */
  p = key;	/* Character pointer */
  h = 0;	/* The hash value */

  while (i--) {
    h += *p++;
    h += (h << 10);
    h ^= (h >> 6);
  }
  h += (h << 3);
  h ^= (h >> 11);
  h += (h << 15);

  return h;
# else
#  ifdef USE_ELF_HASH
  u_int32_t g, h, i;
  char *p;

  i = len;	/* Work back through the key length */
  p = key;	/* Character pointer */
  h = 0;	/* The hash value */

  while (i--) {
    h = (h << 4) + *p++;
    if (g = h & 0xF0000000)
      h ^= g >> 24;
    h &= ~g;
  }

  return h;
#  else
#   ifdef USE_PHP_HASH
  unsigned char res[MD5_DIGEST_LENGTH];
  u_int32_t i;
  int j;
  char result[33];
  char php[9] = {};

  /* calculate md5 hash of key */
  MD5(key, len, res);

  /* convert to ascii */
  for (j = 0; i < MD5_DIGEST_LENGTH; ++i) {
    sprintf(&(result[i*2]), "%x", (int)(res[i] & 0xF0));
    sprintf(&(result[(i*2)+1]), "%x", (int)(res[i] & 0x0F));
  }

  /* take the first 8 bytes */
  memcpy(php, result, 8);
  sscanf(php, "%x", &i);
  i &= 0x7FFFFFFF;
  return i;
#    else
#      error "Please choose USE_CRC32_HASH, USE_ELF_HASH, or USE_PERL_HASH as a hashing scheme when compiling memcache"
#    endif
#  endif
# endif
#endif
}


u_int32_t
mcm_incr(const struct memcache_ctxt *ctxt, struct memcache *mc,
	 const char *key, const size_t key_len, const u_int32_t val) {
  return mcm_atomic_cmd(ctxt, mc, str_incr_cmd, str_incr_len, key, key_len, val);
}


struct memcache *
mcm_new(const struct memcache_ctxt *ctxt) {
  struct memcache *mc;

  mc = (struct memcache *)ctxt->mcMalloc(sizeof(struct memcache));
  if (mc != NULL) {
    bzero(mc, sizeof(struct memcache));

    TAILQ_INIT(&mc->server_list);

    /* Set any default values */
    mc->tv.tv_sec = 2;
    mc->tv.tv_usec = 600;
  }

  return mc;
}


void
mcm_refresh(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_req *req) {
  mcm_fetch_cmd(ctxt, mc, req, str_refresh_cmd, str_refresh_len);
}


const u_int32_t
mcm_reldate(const struct memcache_ctxt *ctxt) {
  return MEMCACHE_RELDATE;
}


int
mcm_replace(const struct memcache_ctxt *ctxt, struct memcache *mc,
	    const char *key, const size_t key_len,
	    const void *val, const size_t bytes,
	    const time_t expire, const u_int16_t flags) {
  return mcm_storage_cmd(ctxt, mc, str_replace_cmd, str_replace_len, key, key_len, val, bytes, expire, flags);
}


struct memcache_res *
mcm_req_add(const struct memcache_ctxt *ctxt, struct memcache_req *req, const char *key, const size_t len) {
  struct memcache_res *res;
  res = mcm_res_new(ctxt);

  res->key = mcm_strdup(ctxt, key);
  res->_flags |= MCM_RES_NEED_FREE_KEY;
  res->len = len;

  TAILQ_INSERT_TAIL(&req->query, res, entries);
  req->num_keys++;

  return res;
}


struct memcache_res *
mcm_req_add_ref(const struct memcache_ctxt *ctxt, struct memcache_req *req, const char *key, const size_t len) {
  struct memcache_res *res;
  res = mcm_res_new(ctxt);

  res->key = key;
  res->len = len;

  TAILQ_INSERT_TAIL(&req->query, res, entries);
  req->num_keys++;

  return res;
}


void
mcm_req_free(const struct memcache_ctxt *ctxt, struct memcache_req *req) {
  while (req->query.tqh_first != NULL)
    mcm_res_free(ctxt, req, req->query.tqh_first);

  while (req->cb.tqh_first != NULL)
    mcm_res_cb_free(req, req->cb.tqh_first);

  ctxt->mcFree(req);
}


struct memcache_req *
mcm_req_new(const struct memcache_ctxt *ctxt) {
  struct memcache_req *req;

  req = (struct memcache_req *)ctxt->mcMalloc(sizeof(struct memcache_req));
  if (req != NULL) {
    bzero(req, sizeof(struct memcache_req));

    TAILQ_INIT(&req->query);
    TAILQ_INIT(&req->cb);
  }

  return req;
}


int
mcm_res_attempted(const struct memcache_ctxt *ctxt,
		  const struct memcache_res *res) {
  return res->_flags & MCM_RES_ATTEMPTED ? 1 : 0;
}


int
mcm_res_found(const struct memcache_ctxt *ctxt,
	      const struct memcache_res *res) {
  return ((res->_flags & (MCM_RES_ATTEMPTED | MCM_RES_FOUND)) == (MCM_RES_ATTEMPTED | MCM_RES_FOUND) ? 1 : 0);
}


void
mcm_res_free(const struct memcache_ctxt *ctxt, struct memcache_req *req, struct memcache_res *res) {
  TAILQ_REMOVE(&req->query, res, entries);
  if ((res->_flags & MCM_RES_NEED_FREE_KEY) == MCM_RES_NEED_FREE_KEY)
    ctxt->mcFree(res->key);

  if (((res->_flags & (MCM_RES_FREE_ON_DELETE | MCM_RES_NO_FREE_ON_DELETE)) ==
       (MCM_RES_FREE_ON_DELETE | MCM_RES_NO_FREE_ON_DELETE)) ||
      res->_flags & MCM_RES_FREE_ON_DELETE) {
    if (res->size > 0)
      ctxt->mcFree(res->val);
  }

  ctxt->mcFree(res);
}


void
mcm_res_free_on_delete(const struct memcache_ctxt *ctxt, struct memcache_res *res, const int fod) {
  if (fod) {
    res->_flags &= ~MCM_RES_NO_FREE_ON_DELETE;
    res->_flags |= MCM_RES_FREE_ON_DELETE;
  } else {
    res->_flags &= ~MCM_RES_FREE_ON_DELETE;
    res->_flags |= MCM_RES_NO_FREE_ON_DELETE;
  }
}


static struct memcache_res *
mcm_res_new(const struct memcache_ctxt *ctxt) {
  struct memcache_res *res;

  res = (struct memcache_res *)ctxt->mcMalloc(sizeof(struct memcache_res));
  if (res != NULL) {
    bzero(res, sizeof(struct memcache_res));

    /* Default values */
    res->_flags = MCM_RES_FREE_ON_DELETE | MCM_RES_NO_FREE_ON_DELETE; /* unset */
  }

  return res;
}


static void
mcm_res_cb_free(struct memcache_req *req, struct memcache_res_cb *cb) {
  mcFreeFunc freeFunc;

  if (cb == NULL || cb->ctxt == NULL)
    return;

  TAILQ_REMOVE(&req->cb, cb, entries);
  freeFunc = cb->ctxt->mcFree;
  (freeFunc)(cb);
}


static struct memcache_res_cb *
mcm_res_cb_new(const struct memcache_ctxt *ctxt) {
  struct memcache_res_cb *cb;

  cb = (struct memcache_res_cb *)ctxt->mcMalloc(sizeof(struct memcache_res_cb));
  if (cb != NULL) {
    bzero(cb, sizeof(struct memcache_res_cb));
  }

  return cb;
}


int
mcm_res_register_fetch_cb(struct memcache_ctxt *ctxt, struct memcache_req *req,
			  struct memcache_res *res, mcResCallback callback, void *misc) {
  struct memcache_res_cb *cb;

  if (callback == NULL || req == NULL || res == NULL || ctxt == NULL)
    return -1;

  cb = mcm_res_cb_new(ctxt);
  if (cb == NULL)
    return -2;

  cb->ctxt = ctxt;
  cb->req = req;
  cb->cb = callback;
  cb->res = res;
  cb->misc = misc;

  TAILQ_INSERT_TAIL(&req->cb, cb, entries);

  return 0;
}


static void
mcm_reset_buf(struct memcache_server *ms) {
  /* Reset our cursor to the front of the buffer */
  ms->cur = ms->start = ms->buf;
  ms->read_cur = NULL;
}


int
mcm_server_activate(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_server *ms) {
  switch (ms->active) {
  case 'd':
    ms->active = 'u';
    mc->live_servers[mc->num_live_servers] = ms;
    mc->live_servers++;

    return 0;
  case 'n':
    warnx("unable to activate a server that does not exist");
    return -1;
  case 't':
    warnx("unable to activate a server that hasn't been added to the server list");
    return -2;
  case 'u':
    warnx("unable to activate a server that is active");
    return -3;
  default:
    abort();
  }
}


int
mcm_server_activate_all(const struct memcache_ctxt *ctxt, struct memcache *mc) {
  struct memcache_server *ms;

  for (ms = mc->server_list.tqh_first; ms != NULL; ms = ms->entries.tqe_next) {
    mcm_server_activate(ctxt, mc, ms);
  }

  return 0;
}


int
mcm_server_add(const struct memcache_ctxt *ctxt, struct memcache *mc, const char *hostname, const char *port) {
  return mcm_server_add2(ctxt, mc, hostname, strlen(hostname), port, strlen(port));
}


int
mcm_server_add2(const struct memcache_ctxt *ctxt, struct memcache *mc, const char *hostname,
		const size_t hostname_len, const char *port, const size_t port_len) {
  struct memcache_server *ms;

  ms = mcm_server_new(ctxt);
  if (ms == NULL)
    return -1;

  if (hostname == NULL || hostname_len == 0) {
    ms->hostname = mcm_strdup(ctxt, "localhost");
  } else {
    ms->hostname = ctxt->mcMallocAtomic(hostname_len + 1);
    memcpy(ms->hostname, hostname, hostname_len);
    ms->hostname[hostname_len] = '\0';
  }

  if (ms->hostname == NULL) {
    mcm_server_free(ctxt, ms);
    return -2;
  }


  if (port == NULL || port_len == 0) {
    ms->port = mcm_strdup(ctxt, "11211");
  } else {
    ms->port = ctxt->mcMallocAtomic(port_len + 1);
    memcpy(ms->port, port, port_len);
    ms->port[port_len] = '\0';
  }

  if (ms->port == NULL) {
    mcm_server_free(ctxt, ms);
    return -3;
  }

  return mcm_server_add3(ctxt, mc, ms);
}


int
mcm_server_add3(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_server *ms) {
  int ret;

  ret = mcm_server_resolve(ms);
  if (ret != 0) {
    warn("memcache: host %s does not exist: %s.  Not adding to server list.", ms->hostname, gai_strerror(ret));
    mcm_server_free(ctxt, ms);
    return -4;
  }

  /* Defaults from mc */
  if (ms->tv.tv_sec == 0 && ms->tv.tv_usec == 0) {
    ms->tv.tv_sec = mc->tv.tv_sec;
    ms->tv.tv_usec = mc->tv.tv_usec;
  }

  TAILQ_INSERT_TAIL(&mc->server_list, ms, entries);

  /* Add ms to the array of servers to try */
  if (mc->live_servers == NULL) {
    mc->num_live_servers = 1;
    mc->live_servers = (struct memcache_server**)ctxt->mcMalloc(sizeof(struct memcache_server*) * mc->num_live_servers);
    mc->live_servers[0] = ms;
  } else {
    /* Reallocate mc->live_servers to fit the number of struct
     * memcache_servers entries. */
    mc->num_live_servers++;
    mc->live_servers = (struct memcache_server**)ctxt->mcRealloc(mc->live_servers, sizeof(struct memcache_server*) * mc->num_live_servers);
    if (mc->live_servers == NULL) {
      warn("libmemcache: Unable to mcRealloc() enough memory to add a new server");
      mcm_server_free(ctxt, ms);
      return -5;
    }

    /* Add the new server to the end of the list */
    mc->live_servers[mc->num_live_servers - 1] = ms;
  }

  return 0;
}


int
mcm_server_add4(const struct memcache_ctxt *ctxt, struct memcache *mc, const char *hostport) {
  struct memcache_server *ms;
  char *cp;

  ms = mcm_server_new(ctxt);
  if (ms == NULL)
    return -1;

  /* Tease out the hostname and portname from a string that we expect
   * to look like "host:port". */
  if (hostport == NULL) {
    ms->hostname = mcm_strdup(ctxt, "localhost");
    if (ms->hostname == NULL) {
      mcm_server_free(ctxt, ms);
      return -2;
    }

    ms->port = mcm_strdup(ctxt, "11211");
    if (ms->port == NULL) {
      mcm_server_free(ctxt, ms);
      return -3;
    }
  } else {
    cp = strchr(hostport, ':');
    if (*cp == '\0') {
      ms->hostname = mcm_strdup(ctxt, hostport);
      if (ms->hostname == NULL) {
	mcm_server_free(ctxt, ms);
	return -2;
      }

      ms->port = mcm_strdup(ctxt, "11211");
      if (ms->port == NULL) {
	mcm_server_free(ctxt, ms);
	return -3;
      }
    } else {
      ms->hostname = mcm_strndup(ctxt, hostport, (size_t)(cp - hostport));
      if (ms->hostname == NULL) {
	mcm_server_free(ctxt, ms);
	return -2;
      }

      /* advance past the ':' and copy whatever is left as the port */
      cp++;
      ms->port = mcm_strdup(ctxt, cp);
      if (ms->port == NULL) {
	mcm_server_free(ctxt, ms);
	return -3;
      }
    }
  }

  return mcm_server_add3(ctxt, mc, ms);
}


static void
mcm_server_block(struct memcache_server *ms, const int use_nbio) {
  if (ms->flags == -1) {
    ms->flags = fcntl(ms->fd, F_GETFL, 0);
    if (ms->flags == -1)
      err(EX_OSERR, "%s:%u\tfcntl(F_GETFL)", __FILE__, __LINE__);
  }

  if (use_nbio != 0) {
    /* Switch to non-blocking io */
    if ((ms->flags & O_NDELAY) != 0) {
      ms->flags |= O_NDELAY;
      goto set_flags;
    }
  } else {
    if (ms->flags & O_NDELAY) {
      ms->flags &= ~O_NDELAY;
      goto set_flags;
    }
  }
  return;

  set_flags:
  if (fcntl(ms->fd, F_SETFL, ms->flags) < 0)
    err(EX_OSERR, "%s:%u\tfcntl(F_SETFL)", __FILE__, __LINE__);
}


static int
mcm_server_connect(struct memcache *mc, struct memcache_server *ms) {
  struct addrinfo *res;
  int i;
#ifdef TCP_NODELAY
  int val;
#endif

  if (ms->fd != -1)
    return ms->fd;

  if (ms->active == 'd' || ms->active == 'n')
    return -1;

  if (ms->hostinfo == NULL || ms->hostinfo->ai_addrlen == 0) {
    i = mcm_server_resolve(ms);
    if (i != 0) {
      warn("host %s does not exist: %s.  Not adding to server list.", ms->hostname, gai_strerror(i));
      ms->active = 'n';
      return -1;
    }
  }

  for (i = 0, res = ms->hostinfo; res != NULL; res = res->ai_next, i++) {
    ms->fd = socket(res->ai_family, res->ai_socktype, res->ai_protocol);
    if (ms->fd < 0) {
      warn("%s:%u\tsocket()", __FILE__, __LINE__);
      continue;
    }

#ifdef TCP_NODELAY
    val = 1;
    if (setsockopt(ms->fd, IPPROTO_TCP, TCP_NODELAY, &val, (socklen_t)sizeof(val)) != 0) {
      warn("%s:%u\tsetsockopt(TCP_NODELAY)", __FILE__, __LINE__);
    }
#endif

    if (setsockopt(ms->fd, SOL_SOCKET, SO_SNDTIMEO, &ms->tv, (socklen_t)sizeof(struct timeval)) != 0) {
      warn("%s:%u\tsetsockopt(SO_SNDTIMEO)", __FILE__, __LINE__);
      /* Close the socket, set the file descriptor to -1, and continue
       * trying to connect to the rest of the servers that match this
       * hostname.  More than likely there is only one IP per host
       * name, but, in the event there isn't, continue to the next
       * entry. */
      if (close(ms->fd) != 0)
	warn("%s:%u\tclose()", __FILE__, __LINE__);
      ms->fd = -1;
      continue;
    }

    if (connect(ms->fd, res->ai_addr, (socklen_t)res->ai_addrlen) != 0) {
      warn("%s:%u\tconnect()", __FILE__, __LINE__);
      if (close(ms->fd) != 0)
	warn("%s:%u\tclose()", __FILE__, __LINE__);
      ms->fd = -1;
      continue;
    } else {
#ifdef HAVE_SELECT
      /* Before we return, add our file descriptor to ms->fds. */
      FD_SET(ms->fd, &ms->fds);
#endif
      return ms->fd;
    }
  }

#ifdef PEDANTIC
  if (ms->fd != -1) abort();
#endif

  /* If none of the IP addresses for this hostname work, remove the
   * server from the live_server list (we assume they're live by
   * default) and return -1. */
  mcm_server_deactivate(NULL, mc, ms);
  return -1;
}


void
mcm_server_deactivate(const struct memcache_ctxt *ctxt, struct memcache *mc,
		      struct memcache_server *ms) {
  u_int32_t i, found;

  /* Since adding servers is so rare, and servers do come back, don't
   * bother mcRealloc(3)'ing mc->live_servers.  Instead, just find the
   * dead server in the array, remove it, shift the remaining servers
   * down a place in the array, and decrement number of servers in the
   * array.  This should making adding servers back to the list more
   * efficient. */
  for (i = 0, found = 0; i < mc->num_live_servers; i++) {
    if (found == 0) {
      if (mc->live_servers[i] == ms)
	found = 1;
      else
	continue;
    }

    mc->live_servers[i] = mc->live_servers[i + 1];
  }

  if (found != 0) {
    mc->num_live_servers--;
    /* If we've still got the server marked up, then set it to down.
     * If we've already marked it as down, keep the original reason
     * since it's going to be more verbose than saying 'down'.  A
     * 'down' server can be resurrected, where as a server that's
     * marked 'no host' won't ever be resurrected. */
    if (ms->active == 'u')
      ms->active = 'd';

    if (ms->fd != -1) {
      if (close(ms->fd) != 0)
	warn("%s:%u\tclose()", __FILE__, __LINE__);
      ms->fd = -1;
    }
  }
}


void
mcm_server_disconnect(const struct memcache_ctxt *ctxt, const struct memcache *mc, struct memcache_server *ms) {
  if (ms->fd != -1) {
    if (close(ms->fd) != 0)
      warn("%s:%u\tclose()", __FILE__, __LINE__);
    mcm_server_init(ctxt, ms);
  }
}


void
mcm_server_disconnect_all(const struct memcache_ctxt *ctxt, const struct memcache *mc) {
  struct memcache_server *ms;

  for (ms = mc->server_list.tqh_first; ms != NULL; ms = ms->entries.tqe_next)
    mcm_server_disconnect(ctxt, mc, ms);
}


struct memcache_server *
mcm_server_find(const struct memcache_ctxt *ctxt, struct memcache *mc, const u_int32_t hash) {
  if (mc->num_live_servers < 1)
    return NULL;

  /* Grab the correct server from the list. */
  return mc->live_servers[hash % mc->num_live_servers];
}


void
mcm_server_free(const struct memcache_ctxt *ctxt, struct memcache_server *ms) {
  if (ms == NULL)
    return;

  if (ms->hostinfo != NULL)
    freeaddrinfo(ms->hostinfo);

  if (ms->hostname != NULL)
    ctxt->mcFree(ms->hostname);

  if (ms->port != NULL)
    ctxt->mcFree(ms->port);

  if (ms->fd != -1) {
    if (close(ms->fd) != 0)
      warn("%s:%u\tclose()", __FILE__, __LINE__);
    ms->fd = -1;
  }

  ctxt->mcFree(ms);
}


static void
mcm_server_init(const struct memcache_ctxt *ctxt, struct memcache_server *ms) {
  ms->active = 't';
  ms->fd = -1;
  ms->flags = -1;
  ms->size = GET_INIT_BUF_SIZE;
}


struct memcache_server *
mcm_server_new(const struct memcache_ctxt *ctxt) {
  struct memcache_server *ms;

  ms = (struct memcache_server *)ctxt->mcMalloc(sizeof(struct memcache_server));
  if (ms != NULL) {
    bzero(ms, sizeof(struct memcache_server));

    ms->buf = (char *)ctxt->mcMallocAtomic(GET_INIT_BUF_SIZE);
    if (ms->buf == NULL) {
      ctxt->mcFree(ms);
      return NULL;
    }

    /* Set any default values */
    mcm_server_init(ctxt, ms);
  }

  return ms;
}


static int
mcm_server_resolve(struct memcache_server *ms) {
  struct addrinfo hints, *res;
  int ret;

  /* Resolve the hostname ahead of time */
  bzero(&hints, sizeof(struct addrinfo));
  hints.ai_family = PF_UNSPEC;
  hints.ai_socktype = SOCK_STREAM;
  ret = getaddrinfo(ms->hostname, ms->port, &hints, &(ms->hostinfo));
  if (ret != 0)
    return ret;

  for (res = ms->hostinfo; res != NULL; res = res->ai_next)
    ms->num_addrs++;

  return 0;
}


struct memcache_server_stats *
mcm_server_stats(const struct memcache_ctxt *ctxt, struct memcache *mc, struct memcache_server *ms) {
  struct memcache_server_stats *s;
  char *cp, *cur;

  if (mcm_server_connect(mc, ms) == -1)
    return NULL;

  if (write(ms->fd, "stats\r\n", MCM_CSTRLEN("stats\r\n")) < 0) {
    warn("%s:%u\twrite()", __FILE__, __LINE__);
    mcm_server_deactivate(ctxt, mc, ms);
    return NULL;
  }

  s = mcm_server_stats_new(ctxt);
  if (s == NULL)
    return NULL;

  /* Switch to non-blocking io */
  mcm_server_block(ms, 1);

  /* Reset our buffer */
  mcm_reset_buf(ms);

  for(;;) {
    cur = mcm_get_line(ctxt, mc, ms);

    if (cur != NULL && memcmp(cur, "STAT ", MCM_CSTRLEN("STAT ")) == 0) {
      cur = &cur[MCM_CSTRLEN("STAT ")];

      /* Time to loop through the potential stats keys.  Joy.  This is
       * going to complete in O(1 + 2 + 3 ... N) operations (currently
       * 190).  Ugh.  Don't know of a better way to handle this
       * without a hash.  Besides, this is just stats. */
      if (memcmp(cur, "pid ", MCM_CSTRLEN("pid ")) == 0) {
	cur = &cur[MCM_CSTRLEN("pid ")];
	s->pid = (pid_t)strtol(cur, &cp, 10);
	if (s->pid == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid pid \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "uptime ", MCM_CSTRLEN("uptime ")) == 0) {
	cur = &cur[MCM_CSTRLEN("uptime ")];
	s->uptime = (time_t)strtol(cur, &cp, 10);
	if (s->uptime == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid uptime \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "time ", MCM_CSTRLEN("time ")) == 0) {
	cur = &cur[MCM_CSTRLEN("time ")];
	s->time = (time_t)strtol(cur, &cp, 10);
	if (s->time == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid time \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "version ", MCM_CSTRLEN("version ")) == 0) {
	cur = &cur[MCM_CSTRLEN("version ")];
	for (cp = cur; !isspace(*cp); cp++);
	s->version = (char *)ctxt->mcMallocAtomic((size_t)(cp - cur + 1));
	if (s->version == NULL) {
	  warn("%s:%u\tmcMallocAtomic()", __FILE__, __LINE__);
	} else {
	  memcpy(s->version, cur, (size_t)(cp - cur));
	  s->version[(size_t)(cp - cur)] = '\0';
	}
      } else if (memcmp(cur, "rusage_user ", MCM_CSTRLEN("rusage_user ")) == 0) {
	cur = &cur[MCM_CSTRLEN("rusage_user ")];
	s->rusage_user.tv_sec = (int32_t)strtol(cur, &cp, 10);
	if (s->rusage_user.tv_sec == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid rusage_user seconds \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
	else {
	  cur = cp; /* advance cursor */
#ifdef PEDANTIC
	  if (!(*cur == '.' || *cur == ':'))
	    warn("%s:%u\tProtocol violation: invalid separator: %x", __FILE__, __LINE__, *cur);
	  else {
#endif
	    cur++; /* advance past colon */
	    s->rusage_user.tv_usec = (int32_t)strtol(cur, &cp, 10);
	    if (s->rusage_user.tv_usec == 0 && (errno == EINVAL || errno == ERANGE))
	      err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid rusage_user microseconds \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
#ifdef PEDANTIC
	  }
#endif
	}

      } else if (memcmp(cur, "rusage_system ", MCM_CSTRLEN("rusage_system ")) == 0) {
	cur = &cur[MCM_CSTRLEN("rusage_system ")];
	s->rusage_system.tv_sec = (int32_t)strtol(cur, &cp, 10);
	if (s->rusage_system.tv_sec == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid rusage_system seconds \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
	else {
	  cur = cp; /* advance cursor */
#ifdef PEDANTIC
	  if (!(*cur == '.' || *cur == ':'))
	    err(EX_PROTOCOL, "%s:%u\tProtocol violation: invalid separator: %x", __FILE__, __LINE__, *cur);
	  else {
#endif
	    cur++; /* advance past colon */
	    s->rusage_system.tv_usec = (int32_t)strtol(cur, &cp, 10);
	    if (s->rusage_system.tv_usec == 0 && (errno == EINVAL || errno == ERANGE))
	      err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid rusage_system microseconds \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
#ifdef PEDANTIC
	  }
#endif
	}
      } else if (memcmp(cur, "curr_items ", MCM_CSTRLEN("curr_items ")) == 0) {
	cur = &cur[MCM_CSTRLEN("curr_items ")];
	s->curr_items = (u_int32_t)strtol(cur, &cp, 10);
	if (s->curr_items == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid curr_items \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "total_items ", MCM_CSTRLEN("total_items ")) == 0) {
	cur = &cur[MCM_CSTRLEN("total_items ")];
	s->total_items = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->total_items == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid total_items \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "bytes ", MCM_CSTRLEN("bytes ")) == 0) {
	cur = &cur[MCM_CSTRLEN("bytes")];
	s->bytes = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->bytes == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid bytes \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "curr_connections ", MCM_CSTRLEN("curr_connections ")) == 0) {
	cur = &cur[MCM_CSTRLEN("curr_connections ")];
	s->curr_connections = (u_int32_t)strtol(cur, &cp, 10);
	if (s->curr_connections == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid curr_connections \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "total_connections ", MCM_CSTRLEN("total_connections ")) == 0) {
	cur = &cur[MCM_CSTRLEN("total_connections ")];
	s->total_connections = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->total_connections == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid total_connections \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "connection_structures ", MCM_CSTRLEN("connection_structures ")) == 0) {
	cur = &cur[MCM_CSTRLEN("connection_structures ")];
	s->connection_structures = (u_int32_t)strtol(cur, &cp, 10);
	if (s->connection_structures == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtol(): invalid connection_structures \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "cmd_get ", MCM_CSTRLEN("cmd_get ")) == 0) {
	cur = &cur[MCM_CSTRLEN("cmd_get ")];
	s->cmd_get = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->cmd_get == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid cmd_get \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "cmd_refresh ", MCM_CSTRLEN("cmd_refresh ")) == 0) {
	cur = &cur[MCM_CSTRLEN("cmd_refresh ")];
	s->cmd_refresh = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->cmd_refresh == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid cmd_refresh \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "cmd_set ", MCM_CSTRLEN("cmd_set ")) == 0) {
	cur = &cur[MCM_CSTRLEN("cmd_set ")];
	s->cmd_set = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->cmd_set == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid cmd_set \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "get_hits ", MCM_CSTRLEN("get_hits ")) == 0) {
	cur = &cur[MCM_CSTRLEN("get_hits ")];
	s->get_hits = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->get_hits == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid get_hits \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "get_misses ", MCM_CSTRLEN("get_misses ")) == 0) {
	cur = &cur[MCM_CSTRLEN("get_misses ")];
	s->get_misses = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->get_misses == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid get_misses \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "refresh_hits ", MCM_CSTRLEN("refresh_hits ")) == 0) {
	cur = &cur[MCM_CSTRLEN("refresh_hits ")];
	s->refresh_hits = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->refresh_hits == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid refresh_hits \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "refresh_misses ", MCM_CSTRLEN("refresh_misses ")) == 0) {
	cur = &cur[MCM_CSTRLEN("refresh_misses ")];
	s->refresh_misses = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->refresh_misses == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid refresh_misses \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "bytes_read ", MCM_CSTRLEN("bytes_read ")) == 0) {
	cur = &cur[MCM_CSTRLEN("bytes_read ")];
	s->bytes_read = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->bytes_read == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid bytes_read \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "bytes_written ", MCM_CSTRLEN("bytes_written ")) == 0) {
	cur = &cur[MCM_CSTRLEN("bytes_written ")];
	s->bytes_written = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->bytes_written == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid bytes_written \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else if (memcmp(cur, "limit_maxbytes ", MCM_CSTRLEN("limit_maxbytes ")) == 0) {
	cur = &cur[MCM_CSTRLEN("limit_maxbytes ")];
	s->limit_maxbytes = (u_int64_t)strtoll(cur, &cp, 10);
	if (s->limit_maxbytes == 0 && (errno == EINVAL || errno == ERANGE))
	  err(EX_PROTOCOL, "%s:%u\tstrtoll(): invalid limit_maxbytes \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      } else {
	for (cp = cur; !isspace(*cp); cp++);
	warn("%s:%u\tProtocol error: unknown stat \"%.*s\"", __FILE__, __LINE__, (int)(cp - cur), cur);
      }

      /* Now that we've sucked in our stats value, set our cursor to
       * the end of the value. */
      cp = memchr(cur, (int)'\r', ms->size - (size_t)(cur - ms->buf));
      if (cp == NULL || cp[1] != '\n') {
	warnx("Protocol error: anticipated end of stats value: not at end of stats value");
	mcm_server_stats_free(ctxt, s);
	mcm_server_deactivate(ctxt, mc, ms);
	return NULL;
      }
    } else if (cur != NULL && memcmp(cur, "END", MCM_CSTRLEN("END")) == 0) {
      /* We're done reading in stats. */
      break;
    } else {
      errx(EX_PROTOCOL, "%s:%u\tUnable to handle response: \"%.*s\"", __FILE__, __LINE__, 15, cur);
    }
  }

  /* Switch to blocking io */
  mcm_server_block(ms, 0);

  return s;
}


void
mcm_server_stats_free(const struct memcache_ctxt *ctxt, struct memcache_server_stats *s) {
  if (s->version != NULL)
    ctxt->mcFree(s->version);
  ctxt->mcFree(s);
}


static struct memcache_server_stats *
mcm_server_stats_new(const struct memcache_ctxt *ctxt) {
  struct memcache_server_stats *s;
  s = (struct memcache_server_stats *)ctxt->mcMalloc(sizeof(struct memcache_server_stats));
  if (s != NULL) {
    bzero(s, sizeof(struct memcache_server_stats));
  }

  return s;
}


int
mcm_set(const struct memcache_ctxt *ctxt, struct memcache *mc,
	const char *key, const size_t key_len,
	const void *val, const size_t bytes,
	const time_t expire, const u_int16_t flags) {
  return mcm_storage_cmd(ctxt, mc, str_set_cmd, str_set_len, key, key_len, val, bytes, expire, flags);
}


struct memcache_server_stats *
mcm_stats(const struct memcache_ctxt *ctxt, struct memcache *mc) {
  struct memcache_server *ms;
  struct memcache_server_stats *s, *ts;

  s = mcm_server_stats_new(ctxt);
  for (ms = mc->server_list.tqh_first; ms != NULL; ms = ms->entries.tqe_next) {
    ts = mcm_server_stats(ctxt, mc, ms);
    if (ts == NULL)
      continue;

    /* Merge the values from ts into s.  Any per-server specific data
     * is pulled from the last server. */
    s->pid = ts->pid;
    s->uptime = ts->uptime;
    s->time = ts->time;
    if (s->version == NULL && ts->version != NULL)
      s->version = mcm_strdup(ctxt, ts->version);

    s->rusage_user.tv_sec += ts->rusage_user.tv_sec;
    s->rusage_user.tv_usec += ts->rusage_user.tv_usec;
    if (s->rusage_user.tv_usec > 1000000) {
      s->rusage_user.tv_sec += s->rusage_user.tv_usec / 1000000;
      s->rusage_user.tv_usec -= 1000000 * (s->rusage_user.tv_usec / 1000000);
    }

    s->rusage_system.tv_sec += ts->rusage_system.tv_sec;
    s->rusage_system.tv_usec += ts->rusage_system.tv_usec;
    if (s->rusage_system.tv_usec > 1000000) {
      s->rusage_system.tv_sec += s->rusage_system.tv_usec / 1000000;
      s->rusage_system.tv_usec -= 1000000 * (s->rusage_system.tv_usec / 1000000);
    }

    s->curr_items += ts->curr_items;
    s->total_items += ts->total_items;
    s->bytes = s->bytes + ts->bytes;
    s->curr_connections += ts->curr_connections;
    s->total_connections += ts->total_connections;
    s->connection_structures += ts->connection_structures;
    s->cmd_get += ts->cmd_get;
    s->cmd_refresh += ts->cmd_refresh;
    s->cmd_set += ts->cmd_set;
    s->get_hits += ts->get_hits;
    s->get_misses += ts->get_misses;
    s->refresh_hits += ts->refresh_hits;
    s->refresh_misses += ts->refresh_misses;
    s->bytes_read += ts->bytes_read;
    s->bytes_written += ts->bytes_written;
    s->limit_maxbytes += ts->limit_maxbytes;

    mcm_server_stats_free(ctxt, ts);
  }

  return s;
}


static int
mcm_storage_cmd(const struct memcache_ctxt *ctxt, struct memcache *mc,
		const char *cmd, const size_t cmd_len,
		const char *key, const size_t key_len,
		const void *val, const size_t bytes,
		const time_t expire, const u_int16_t flags) {
  struct memcache_server *ms;
  u_int32_t hash;
  char *cur;
  size_t buf_left, i;
  struct iovec wv[11];

  /* If there's only one server, don't bother actually hashing. */
  if (mc->num_live_servers == 1)
    hash = 42;
  else
    hash = mcm_hash_key(ctxt, key, key_len);

  ms = mcm_server_find(ctxt, mc, hash);
  if (ms == NULL) {
    warnx("%s:%u\tUnable to find a valid server", __FILE__, __LINE__);
    return -1;
  }

  if (mcm_server_connect(mc, ms) == -1)
    return -2;

  /* Reset the buffer so that I can chop it up to use it as a scratch
   * pad for converting numbers from binary to ASCII.  *hears the baby
   * jebus crying* */
  mcm_reset_buf(ms);
  cur = ms->buf;
  buf_left = ms->size;

  wv[0].iov_base = cmd;
  wv[0].iov_len = cmd_len;
  wv[1].iov_base = key;
  wv[1].iov_len = key_len;
  wv[2].iov_base = str_space;
  wv[2].iov_len = str_space_len;

  /* Convert flags to a string */
  i = (size_t)snprintf(cur, buf_left, "%u", flags);
  if (i < 1) {
    warnx("%s:%u\tsnprintf()", __FILE__, __LINE__);
    return -3;
  }

  wv[3].iov_base = cur;		/* Note where our flags string is located */
  wv[3].iov_len = i;
  wv[4].iov_base = wv[2].iov_base;	/* Add a space */
  wv[4].iov_len = wv[2].iov_len;

  cur = &cur[++i];	/* advance cursor past trailing '\0' */
  buf_left -= i;	/* Note our consumption of some buffer */

  /* Convert expiration time to a string */
  i = (size_t)snprintf(cur, buf_left, "%lu", expire);
  if (i < 1) {
    warnx("%s:%u\tsnprintf()", __FILE__, __LINE__);
    return -4;
  }

  wv[5].iov_base = cur;		/* Note where our flags string is located */
  wv[5].iov_len = i;
  wv[6].iov_base = wv[2].iov_base;	/* Add a space */
  wv[6].iov_len = wv[2].iov_len;

  cur = &cur[++i];	/* advance cursor past trailing '\0' */
  buf_left -= i;	/* Note our consumption of some buffer */

  /* Convert bytes to a string */
  i = (size_t)snprintf(cur, buf_left, "%u", bytes);
  if (i < 1) {
    warnx("%s:%u\tsnprintf()", __FILE__, __LINE__);
    return -5;
  }

  wv[7].iov_base = cur;	/* Note where our flags string is located */
  wv[7].iov_len = i;
  wv[8].iov_base = str_endl;	/* Newline */
  wv[8].iov_len = str_endl_len;

  cur = &cur[++i];	/* advance cursor past trailing '\0' */
  buf_left -= i;	/* Note our consumption of some buffer */

  /* Add the data */
  wv[9].iov_base = val;
  wv[9].iov_len = bytes;

  /* Add another carriage return */
  wv[10].iov_base = str_endl;
  wv[10].iov_len = str_endl_len;

  if (writev(ms->fd, wv, 11) < 0) {
    warn("%s:%u\twritev()", __FILE__, __LINE__);
    mcm_server_deactivate(ctxt, mc, ms);
    /* XXX Should we recursively attempt to try this query on the
     * remaining servers in the cluster if the writev() fails?
     * Eventually we'd fail once all servers were exhausted?  For now,
     * just fail and return NULL. */
    return -6;
  }

  mcm_server_block(ms, 1);
  mcm_reset_buf(ms);
  cur = mcm_get_line(ctxt, mc, ms);
  if (cur != NULL && memcmp(cur, "STORED", MCM_CSTRLEN("STORED")) == 0) {
    /* Groovy Tuesday */
    mcm_server_block(ms, 0);
    return 0;
  } else if (cur != NULL && memcmp(cur, "NOT_STORED", MCM_CSTRLEN("NOT_STORED")) == 0) {
    /* Fuck beans.  That was them, wasn't it? */
    mcm_server_block(ms, 0);
    warnx("%s:%u\tUnable to store", __FILE__, __LINE__);
    return 1;
  }
  warnx("%s:%u\tProtocol error: %s", __FILE__, __LINE__, cur);
  return 2;
}


char *
mcm_strdup(const struct memcache_ctxt *ctxt, const char *str) {
  return mcm_strndup(ctxt, str, strlen(str));
}


char *
mcm_strndup(const struct memcache_ctxt *ctxt, const char *str, const size_t len) {
  char *cp;

  cp = ctxt->mcMallocAtomic(len + 1);
  if (cp != NULL) {
    memcpy(cp, str, len);
    cp[len] = '\0';
  }

  return cp;
}


void
mcm_timeout(const struct memcache_ctxt *ctxt, struct memcache *mc, const int sec, const int msec) {
  mc->tv.tv_sec = sec;
  mc->tv.tv_usec = msec;
}


const u_int32_t
mcm_vernum(const struct memcache_ctxt *ctxt) {
  return MEMCACHE_VERNUM;
}


const char *
mcm_version(const struct memcache_ctxt *ctxt) {
  return MEMCACHE_VER;
}


/* BEGIN memcache memory API */
int
mcMemGet(mcFreeFunc *freeFunc, mcMallocFunc *mallocFunc, mcMallocFunc *mallocAtomicFunc,
	 mcReallocFunc *reallocFunc) {
  if (freeFunc != NULL)
    *freeFunc = mcGlobalCtxt.mcFree;

  if (mallocFunc != NULL)
    *mallocFunc = mcGlobalCtxt.mcMalloc;

  if (mallocAtomicFunc != NULL)
    *mallocAtomicFunc = mcGlobalCtxt.mcMallocAtomic;

  if (reallocFunc != NULL)
    *reallocFunc = mcGlobalCtxt.mcRealloc;

  return(0);
}


struct memcache_ctxt *
mcMemNewCtxt(mcFreeFunc freeFunc, mcMallocFunc mallocFunc, mcMallocFunc mallocAtomicFunc,
	     mcReallocFunc reallocFunc) {
  struct memcache_ctxt *ctxt;

  if (freeFunc == NULL || mallocFunc == NULL || reallocFunc == NULL)
    return NULL;

  ctxt = mallocFunc(sizeof(struct memcache_ctxt));
  if (ctxt != NULL) {
    bzero(ctxt, sizeof(struct memcache_ctxt));
    if (mcMemSetupCtxt(ctxt, freeFunc, mallocFunc, mallocAtomicFunc, reallocFunc) != 0) {
      bzero(ctxt, sizeof(struct memcache_ctxt));
      freeFunc(ctxt);
      return NULL;
    }
  }
  return ctxt;
}


void
mcMemFreeCtxt(struct memcache_ctxt *ctxt) {
  mcFreeFunc freeFunc;

  if (ctxt == NULL || ctxt->mcFree == NULL)
    return;

  freeFunc = ctxt->mcFree;
  freeFunc(ctxt);
}

int
mcMemSetup(mcFreeFunc freeFunc, mcMallocFunc mallocFunc,
	   mcMallocFunc mallocAtomicFunc, mcReallocFunc reallocFunc) {
  return mcMemSetupCtxt(&mcGlobalCtxt, freeFunc, mallocFunc, mallocAtomicFunc, reallocFunc);
}


int
mcMemSetupCtxt(struct memcache_ctxt *ctxt, mcFreeFunc freeFunc, mcMallocFunc mallocFunc,
	       mcMallocFunc mallocAtomicFunc, mcReallocFunc reallocFunc) {
  if (ctxt == NULL || freeFunc == NULL || mallocFunc == NULL || reallocFunc == NULL)
    return(1);

  ctxt->mcFree = freeFunc;
  ctxt->mcMalloc = mallocFunc;
  ctxt->mcMallocAtomic = (mallocAtomicFunc != NULL ? mallocAtomicFunc : mallocFunc);
  ctxt->mcRealloc = reallocFunc;

  return(0);
}
/* END memcache memory API */
