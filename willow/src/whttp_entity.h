/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp_entity: HTTP entity handling.
 */

#ifndef WHTTP_ENTITY
#define WHTTP_ENTITY

#define ENT_SOURCE_BUFFER	1
#define ENT_SOURCE_FDE		2

#define REQTYPE_GET	0
#define REQTYPE_POST	1
#define REQTYPE_HEAD	2
#define REQTYPE_TRACE	3
#define REQTYPE_OPTIONS	4

struct http_entity;
struct http_client;
struct readbuf;

typedef void (*header_cb)(struct http_entity *, void *);

struct header_list {
	const char	*hl_name;
	const char	*hl_value;
struct	header_list	*hl_next;
struct	header_list	*hl_tail;
	int		 hl_len;
};

struct http_entity {
	union {
		struct {
			int		 status;
			const char	*status_str;	
		} response;
		struct {
			int	 reqtype;
			char	*path;
		} request;
	}		 he_rdata;
	
struct	header_list	 he_headers;
	int		 he_source_type;
	
	union {
		struct {
			const char	*addr;
			int		 len;
		}		 buffer;
		struct fde	*fde;
	}		 he_source;

	struct {
		int	 cachable:1;
	}		 he_flags;

	/*
	 * This is internal to whttp_entity.  Don't touch it.
	 */
	void		*_he_cbdata;
	header_cb	 _he_func;
};

void entity_read_headers(struct http_entity *, struct readbuf *, header_cb, void *);

void entity_send(struct http_client *, struct http_entity *, header_cb);
s
void header_add(struct header_list *, const char *, const char *);
void header_free(struct header_list *);
char *header_build(struct header_list *);

#endif
