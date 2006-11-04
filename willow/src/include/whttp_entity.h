/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp_entity: HTTP entity handling.
 */

#ifndef WHTTP_ENTITY
#define WHTTP_ENTITY

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <set>
using std::set;

#include <zlib.h>

#include "whttp.h"
#include "whttp_header.h"
#include "queue.h"

#define ENT_SOURCE_BUFFER	1
#define ENT_SOURCE_FDE		2
#define ENT_SOURCE_NONE		3
#define ENT_SOURCE_FILE		4

#define ENT_ERR_READERR	-1	/* read error while parsing headers	*/
#define ENT_ERR_INVHDR	-2	/* invalid headers 			*/
#define ENT_ERR_INVHOST	-3	/* invalid Host:			*/
#define ENT_ERR_INVREQ	-4	/* invalid request type			*/
#define ENT_ERR_2MANY	-5	/* too many headers			*/
#define ENT_ERR_LOOP	-6	/* forwarding loop detected		*/
#define ENT_ERR_INVAE	-7	/* invalid accept-encoding		*/

extern struct request_type supported_reqtypes[];

#define TE_CHUNKED	0x1	/* Chunked encoding			*/

#define ENT_CHUNKED_OKAY	0x1

extern const char *ent_errors[];

#define MAX_HEADERS	64	/* maximum # of headers to allow	*/

#define HAS_BODY(x)	((x) != 304)

struct http_entity;
struct http_client;

struct bufferevent;

typedef void (*header_cb)(struct http_entity *, void *, int);
typedef void (*cache_callback)(const char *, size_t, void *);

enum encoding {
	E_NONE = 0,
	E_DEFLATE,
	E_X_DEFLATE,
	E_GZIP,
	E_X_GZIP
};

struct http_entity : freelist_allocator<http_entity> {
	http_entity() {
		he_extraheaders = evbuffer_new();
	}

	~http_entity() {
		if (_he_frombuf) {
			bufferevent_disable(_he_frombuf, EV_READ | EV_WRITE);
			bufferevent_free(_he_frombuf);
		}
		if (_he_tobuf) {
			bufferevent_disable(_he_tobuf, EV_READ | EV_WRITE);
			bufferevent_free(_he_tobuf);
		}
		if (he_reqstr)
			wfree(he_reqstr);
		delete[] _he_hdrbuf;
		evbuffer_free(he_extraheaders);
	}

	struct {
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
			set<qvalue>	accept_encoding;
		} request;
	}		 he_rdata;

	char		*he_h_pragma;
	char		*he_h_cache_control;
	char		*he_h_transfer_encoding;
	char		*he_h_if_modified_since;
	char		*he_h_last_modified;
	char		*he_reqstr;	
struct	header_list	 he_headers;
struct	evbuffer	*he_extraheaders;
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
		unsigned int	 cachable:1;
		unsigned int	 response:1;
		unsigned int	 error:1;
		unsigned int	 hdr_only:1;
		unsigned int	 eof:1;
		unsigned int	 drained:1;
		unsigned int	 chunked:1;
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
	size_t		 _he_hdroff;		/* internal use by entity_read_callback	*/
	char		*_he_hdrbuf;		/*          ''     ''			*/
};

	void	entity_read_headers	(struct http_entity *, header_cb, void *);
	void	entity_send		(struct fde *, struct http_entity *, header_cb, void *, int);
	void	entity_set_response	(struct http_entity *, int isresp);

	int 		 qvalue_parse		(set<qvalue> &list, const char *header);
	bool		 qvalue_remove_best	(set<qvalue> &list, qvalue &);
enum	encoding	 accept_encoding	(const char *ent);

#endif
