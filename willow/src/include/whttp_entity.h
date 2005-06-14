/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp_entity: HTTP entity handling.
 */

#ifndef WHTTP_ENTITY
#define WHTTP_ENTITY

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include <zlib.h>

#include "whttp.h"
#include "queue.h"

#define ENT_SOURCE_BUFFER	1
#define ENT_SOURCE_FDE		2
#define ENT_SOURCE_NONE		3
#define ENT_SOURCE_FILE		4

#define REQTYPE_GET	0
#define REQTYPE_POST	1
#define REQTYPE_HEAD	2
#define REQTYPE_TRACE	3
#define REQTYPE_OPTIONS	4
#define REQTYPE_INVALID	-1

#define ENT_ERR_READERR	-1	/* read error while parsing headers	*/
#define ENT_ERR_INVHDR	-2	/* invalid headers 			*/
#define ENT_ERR_INVHOST	-3	/* invalid Host:			*/
#define ENT_ERR_INVREQ	-4	/* invalid request type			*/
#define ENT_ERR_2MANY	-5	/* too many headers			*/
#define ENT_ERR_LOOP	-6	/* forwarding loop detected		*/

#define TE_CHUNKED	0x1	/* Chunked encoding			*/

#define ENT_CHUNKED_OKAY	0x1

extern const char *ent_errors[];

#define MAX_HEADERS	64	/* maximum # of headers to allow	*/

struct http_entity;
struct http_client;

struct bufferevent;

typedef void (*header_cb)(struct http_entity *, void *, int);
typedef void (*cache_callback)(const char *, size_t, void *);

struct header_list {
	char		*hl_name;
	char		*hl_value;
struct	header_list	*hl_next;
struct	header_list	*hl_tail;
	int		 hl_len;
	int		 hl_num;
	int		 hl_flags;
};

struct qvalue {
	float	 val;
const	char	*name;

	TAILQ_ENTRY(qvalue) entries;
};
TAILQ_HEAD(qvalue_head, qvalue);

enum encoding {
	E_NONE = 0,
	E_DEFLATE,
	E_X_DEFLATE,
	E_GZIP,
	E_X_GZIP,
};

struct http_entity {
	union {
		/* response-only data */
		struct {
			int		 status;
			const char	*status_str;	
		} response;
		/* request-only data */
		struct {
			int	 reqtype;
			char	*path;
			int	 httpmaj, httpmin;
			/*
			 * Interesting headers.
			 */
			char	*host;		/* Host			*/
			int	 contlen;	/* Content-Length	*/
		struct	qvalue_head accept_encoding;
		} request;
	}		 he_rdata;

	char		*he_reqstr;	
struct	header_list	 he_headers;
	int		 he_source_type;
	
	union {
		/* buffer data */
		struct {
			const char	*addr;
			int		 len;
		}		 buffer;
		/* sendfile data */
		struct {
			int	fd;
			size_t	size;
			off_t	off;
		}		 fd;
		/* fde data */
		struct {
			struct fde	*fde;
			int		 len; /* or -1 */
			int		 _wrt;	/* amount left to write */
		}		 fde;
	}		 he_source;

	struct {
		int	 cachable:1;
		int	 response:1;
		int	 error:1;
		int	 hdr_only:1;
		int	 eof:1;
		int	 drained:1;
		int	 chunked:1;
	}		 he_flags;

	int		 he_te;		/* transfer encoding		*/
enum	encoding	 he_encoding;

	/*
	 * If you want a callback when each piece of data is written, set this.  
	 * This only works when sending from an FDE, not when using a buffer 
	 * (if you use a buffer, you already have the data...)
	 */
	cache_callback	 he_cache_callback;
	void		*he_cache_callback_data;
	
	size_t		 he_size;

	/*
	 * This is internal to whttp_entity.  Don't touch it.
	 */
	void		*_he_cbdata;
	header_cb	 _he_func;
struct	fde		*_he_target;
	int		 _he_state;
	int		 _he_chunk_size;	/* For chunked encoding			*/
struct	bufferevent	*_he_frombuf;
struct	bufferevent	*_he_tobuf;
	z_stream	 _he_zbuf;
};

	void	entity_read_headers	(struct http_entity *, header_cb, void *);
	void	entity_send		(struct fde *, struct http_entity *, header_cb, void *, int);
	void	entity_free		(struct http_entity *);
	void	entity_set_response	(struct http_entity *, int isresp);

	int 		 qvalue_parse		(struct qvalue_head *list, const char *header);
struct	qvalue		*qvalue_remove_best	(struct qvalue_head *list);
enum	encoding	 accept_encoding	(const char *ent);

	void		 header_add		(struct header_list *, char *, char *);
	void		 header_append_last	(struct header_list *, const char *);
	void		 header_free		(struct header_list *);
	char		*header_build		(struct header_list *);
	void		 header_remove		(struct header_list *, struct header_list *);
	void		 header_dump		(struct header_list *, int);
	int		 header_undump		(struct header_list *, int, off_t *);
struct	header_list	*header_find		(struct header_list *, const char *);

#endif
