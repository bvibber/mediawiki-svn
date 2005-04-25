/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp_entity: HTTP entity handling.
 */

/*
 * How does this work?
 *
 * Each HTTP request can be divided into two entities: the request and the response.
 * The client sends the request, i.e. the headers and possibly a body, to the server,
 * which considers it and sends a reply.
 *
 * Internally, we read the request headers and ignore the body [entity_read_headers].  
 * We then examine the headers [whttp:client_read_done] and decide if it has a body.
 * We modify the entry slightly, and send it to the backend with either no source or,
 * if it had a body, the client's FDE as the source [entity_send].  We then wait for
 * the server to reply with its header.  When it does [whttp:backend_headers_done], 
 * we send the request to the client, using the backend's FDE as the body, if it has 
 * one, and close it.
 *
 * See "Entity sending" below for a detailed description of how entity sending works.
 *
 * TODO: We don't have to buffer the headers, _but_ it makes things easier for now and
 * doesn't cost much.  if we start not buffering we need to decide what to do when the
 * client goes away unexpectedly.  probably it's easiest to just drop the backend
 * connection (this is wasteful of backends but we don't cache them at the moment
 * anyway).  what do we do when the client sends "Foo: bar\r\n  baz\r\n" and we decide
 * after baz that we shouldn't send that header after all? 
 *
 * There is a trade-off in some places between excessive copying and excessive syscalls.
 * In some cases we copy data (headers) when we could undo the parser mangling and send
 * them as-is.  IMO this is not likely to be a worthwhile optimisation, needs profiling.
 *
 * As for FDE backending, Unix sucks:
 *
 *    The sendfile() function copies data  from  in_fd  to  out_fd
 *    starting  at  offset  off and of length len bytes. The in_fd
 *    argument should be a  file  descriptor  to  a  regular  file
 *    opened for reading.
 */
 
#include <unistd.h>
#include <errno.h>
#include <string.h>
#include <stdlib.h>
#include <stdio.h>

#include "willow.h"
#include "whttp.h"
#include "whttp_entity.h"
#include "wnet.h"
#include "wlog.h"

#define ENTITY_STATE_START	0
#define ENTITY_STATE_CR		1
#define ENTITY_STATE_NL		2
#define ENTITY_STATE_HDR	3
#define ENTITY_STATE_COLON	4
#define ENTITY_STATE_SPACE	5
#define ENTITY_STATE_VALUE	6
#define ENTITY_STATE_CREMPTY	7
#define ENTITY_STATE_DONE	8
#define ENTITY_STATE_BODY	9

static void entity_read_callback(struct fde *);
static int parse_headers(struct http_entity *);
static int parse_reqtype(struct http_entity *);

static void entity_send_headers_done(struct fde *, void *, int);
static void entity_send_fde_write_done(struct fde *, void *, int);
static void entity_send_buf_done(struct fde *, void *, int);
static void entity_send_fde_read(struct fde *);

void
entity_read_headers(entity, func, udata)
	struct http_entity *entity;
	header_cb func;
	void *udata;
{
	entity->_he_cbdata = udata;
	entity->_he_func = func;

	DEBUG((WLOG_DEBUG, "entity_read_headers: starting"));
	/* XXX source for an entity header read is _always_ an fde */
	wnet_register(entity->he_source.fde->fde_fd, FDE_READ, entity_read_callback, entity);
	//entity_read_callback(entity->he_source.fde);
}

static void
entity_read_callback(e)
	struct fde *e;
{
struct	http_entity	*entity = e->fde_rdata;

	DEBUG((WLOG_DEBUG, "entity_read_callback: called"));
	
	if (READBUF_DATA_LEFT(&entity->_he_readbuf) == 0) {
		switch (readbuf_getdata(entity->he_source.fde->fde_fd, &entity->_he_readbuf)) {
		case -1:
			DEBUG((WLOG_DEBUG, "entity_read_callback: readbuf_getdata returned -1, errno=%d %s", 
					errno, strerror(errno)));
			if (errno == EWOULDBLOCK)
				return;
		case 0:
			DEBUG((WLOG_DEBUG, "entity_read_callback: readbuf_getdata returned 0"));
			wnet_register(entity->he_source.fde->fde_fd, FDE_READ, NULL, NULL);
			entity->he_flags.error = 1;
			entity->_he_func(entity, entity->_he_cbdata, 0);
			return;
		}
	}

	DEBUG((WLOG_DEBUG, "entity_read_callback: running header parse"));
	if (parse_headers(entity) == -1) {
		DEBUG((WLOG_DEBUG, "entity_read_callback: parse_headers returned -1"));
		wnet_register(entity->he_source.fde->fde_fd, FDE_READ, NULL, NULL);
		entity->he_flags.error = 1;
		entity->_he_func(entity, entity->_he_cbdata, -1);
		return;
	}

	if (entity->_he_state == ENTITY_STATE_DONE) {
		DEBUG((WLOG_DEBUG, "entity_read_callback: client is ENTITY_STATE_DONE"));
		wnet_register(entity->he_source.fde->fde_fd, FDE_READ, NULL, NULL);
		entity->_he_func(entity, entity->_he_cbdata, 01);
		return;
	}

	return;
}

/*
 * I don't like this.  There should be an easier/faster way to do it, but this is
 * the most understandable form for me...
 *
 * TODO: handle headers of the form "Name: value\r\n  more value\r\n".
 * 
 * This is more strict than it needs to be in some places, e.g. enforcement of
 * \r\n over \n.
 */
static int
parse_headers(entity)
	struct http_entity *entity;
{
	while (READBUF_DATA_LEFT(&entity->_he_readbuf) > 0) {
		char c = *READBUF_CUR_POS(&entity->_he_readbuf);

		switch(entity->_he_state) {
		case ENTITY_STATE_START:
			/* should be reading a request type */
			switch(c) {
				case '\r':
					*READBUF_CUR_POS(&entity->_he_readbuf) = '\0';
					if (parse_reqtype(entity) == -1)
						return -1;
					entity->_he_state = ENTITY_STATE_CR;
					break;
				case '\n':
					return -1;
				default:
					break;
			}
			break;
		case ENTITY_STATE_CR:
			switch(c) {
				case '\n':
					entity->_he_state = ENTITY_STATE_NL;
					break;
				default:
					return -1;
			}
			break;
		case ENTITY_STATE_NL:
			switch(c) {
				case '\r':
					entity->_he_state = ENTITY_STATE_CREMPTY;
					break;
				case '\n': case ' ': case ':':
					return -1;
				default: /* header name */
					entity->_he_state = ENTITY_STATE_HDR;
					entity->_he_hdrbuf = entity->_he_readbuf.rb_p + entity->_he_readbuf.rb_dpos;
					break;
			}
			break;
		case ENTITY_STATE_HDR:
			switch(c) {
				case ':':
					entity->_he_state = ENTITY_STATE_COLON;
					*READBUF_CUR_POS(&entity->_he_readbuf) = '\0';
					break;
				case ' ': case '\r': case '\n':
					return -1;
				default:
					break;
			}
			break;
		case ENTITY_STATE_COLON:
			switch(c) {
				case ' ':
					entity->_he_state = ENTITY_STATE_SPACE;
					break;
				default:
					return -1;
			}
			break;
		case ENTITY_STATE_SPACE:
			switch(c) {
				case '\r': case '\n': case ':': case ' ':
					return -1;
				default:
					header_add(&entity->he_headers, entity->_he_hdrbuf, 
						entity->_he_readbuf.rb_p + entity->_he_readbuf.rb_dpos);
					entity->_he_state = ENTITY_STATE_VALUE;
					break;
			}
			break;
		case ENTITY_STATE_VALUE:
			switch(c) {
				case '\r': 
					*READBUF_CUR_POS(&entity->_he_readbuf) = '\0';
					entity->_he_state = ENTITY_STATE_CR;
					break;
				case '\n': 
					return -1;
				default:
					break;
			}
			break;
		case ENTITY_STATE_CREMPTY:
			switch(c) {
				case '\n':
					entity->_he_state = ENTITY_STATE_DONE;
					return 0;
				default:
					return -1;
			}
		case ENTITY_STATE_DONE:
			/*
			 * We're done parsing headers on this client, but they sent
			 * more data.  Shouldn't ever happen, but kill them if it does.
			 */
			return -1;
		default:
			abort(); /* ??? This should be handled above for consistency
				  * (ENTITY_STATE_BODY) but i don't know which is correct.
				  */
		}
		READBUF_INC_DATA_POS(&entity->_he_readbuf);
	}
	return 0;
}

static int 
parse_reqtype(entity)
	struct http_entity *entity;
{
	char	*p, *s;
	char	*request = entity->_he_readbuf.rb_p;

	DEBUG((WLOG_DEBUG, "parse_reqtype: called, response=%d", (int)entity->he_flags.response));
	
	/*
	 * These probably shouldn't be handled in the same function.
	 */
	if (entity->he_flags.response) {
		/* 
		 * HTTP/1.0
		 */
		if ((p = strchr(request, ' ')) == NULL)
			return -1;
		*p++ = '\0';
		
		/* 200 */
		if ((s = strchr(p, ' ')) == NULL)
			return -1;
		*s++ = '\0';
		entity->he_rdata.response.status = atoi(p);
		
		/* OK */
		entity->he_rdata.response.status_str = s;
		
		DEBUG((WLOG_DEBUG, "parse_reqtype: \"%s\" \"%d\" \"%s\"",
				request, entity->he_rdata.response.status,
				entity->he_rdata.response.status_str));
		return 0;
	}	
	
	/* GET */
	if ((p = strchr(request, ' ')) == NULL)
		return -1;

	*p++ = '\0';

	/* XXX parse this using request_type */
	if (!strcmp(request, "GET"))
		entity->he_rdata.request.reqtype = REQTYPE_GET;
	else if (!strcmp(request, "POST"))
		entity->he_rdata.request.reqtype = REQTYPE_POST;
	else if (!strcmp(request, "HEAD"))
		entity->he_rdata.request.reqtype = REQTYPE_HEAD;
	else if (!strcmp(request, "TRACE"))
		entity->he_rdata.request.reqtype = REQTYPE_TRACE;
	else if (!strcmp(request, "OPTIONS"))
		entity->he_rdata.request.reqtype = REQTYPE_OPTIONS;
	else
		return -1;

	/* /path/to/file */
	if ((s = strchr(p, ' ')) == NULL)
		return -1;

	*s++ = '\0';
	
	entity->he_rdata.request.path = strdup(p);

	/* HTTP/1.0 */
	/*
	 * Ignore this for now...
	 */
	return 0;
}

/*
 * Header handling.
 */
void
header_free(head)
	struct header_list *head;
{
struct	header_list	*next = head->hl_next;

	while (next) {
		struct header_list *this = next;
		next = this->hl_next;
		wfree(this);
	}

	memset(head, 0, sizeof(*head));
}

void
header_add(head, name, value)
	struct header_list *head;
	const char *name, *value;
{
struct	header_list	*new = head;

	if (head->hl_tail)
		new = head->hl_tail;
	else
		while (new->hl_next)
			new = new->hl_next;
	new->hl_next = wmalloc(sizeof(*head->hl_next));
	head->hl_tail = new->hl_next;
	head->hl_len += strlen(name) + strlen(value) + 4;
	new = new->hl_next;
	new->hl_name = name;
	new->hl_value = value;
	new->hl_next = new->hl_tail = NULL;
}

void
header_remove(head, it)
	struct header_list *head, *it;
{
struct	header_list	*jt;

	jt = head;
	while (jt->hl_next && jt->hl_next != it)
		jt = jt->hl_next;
	jt->hl_next = jt->hl_next->hl_next;
	if (it == head->hl_tail)
		head->hl_tail = jt;
	wfree(it);
}

char *
header_build(head)
	struct header_list *head;
{
	char	*buf = NULL;
	size_t	 bufsz = 0;
	size_t	 buflen = 0;

	bufsz = head->hl_len + 2;
	buf = wmalloc(bufsz + 1);
	while (head->hl_next) {
		head = head->hl_next;
		buflen += sprintf(buf + buflen, "%s: %s\r\n", head->hl_name, head->hl_value);
	}
	strcat(buf, "\r\n");

	return buf;
}

/*
 * Entity sending.  This is not pretty.
 *
 * The entry point is entity_send().  This writes the request [XXX: this should
 * be done async.  I'm pretty certain it'll never block, but we can't guarantee
 * that], builds a string from the headers, and wnet_writes it with
 * entity_send_headers_done as the callback.
 *
 * entity_send_headers_done decides what to do next:
 *   NO BODY  -> call user's callback immediately.
 *   FDE BODY -> entity_send_start_proxy
 *   BUF BODY -> entity_send_from_buf
 *
 * entity_send_start_proxy:
 *   registers a read callback for the source FDE with entity_send_source_read as
 *   the callback.  entity_send_source_read reads the available data, then writes
 *   it with entity_send_source_write as the callback.  _write CALLS SOURCE_READ
 *   AGAIN.  this is important because otherwise we run into bad interactions with
 *   the edge-triggered wnet [? i don't think this is actually true but it's what
 *   the old code did and it's simpler than registering in two places].  
 *   source_read handles EAGAIN and wnet_register itself.
 *       
 * entity_send_from_buf:
 *   calls wnet_write on the buffer with entity_send_buf_done as the callback.
 *   entity_send_buf_done calls the user's callback and returns.
 *
 * WARNING: if wnet_write completes immediately, i.e. does not block, it will call
 * your callback before it returns.  after this, the entity may no longer exist.
 * wnet_write should generally be the last thing a function does before it returns.
 */

void
entity_send(fde, entity, cb, data)
	struct fde *fde;
	struct http_entity *entity;
	header_cb cb;
	void *data;
{
	char	status[4];

	entity->_he_func = cb;
	entity->_he_cbdata = data;
	
	DEBUG((WLOG_DEBUG, "entity_send: writing to %d [%s]", fde->fde_fd, fde->fde_desc));
	
	if (entity->he_flags.response) {
		sprintf(status, "%d", entity->he_rdata.response.status);
		write(fde->fde_fd, "HTTP/1.0 ", 9);
		write(fde->fde_fd, status, strlen(status));
		write(fde->fde_fd, " ", 1);
		write(fde->fde_fd, entity->he_rdata.response.status_str,
			strlen(entity->he_rdata.response.status_str));
		write(fde->fde_fd, "\r\n", 2);
	} else {
		write(fde->fde_fd, request_string[entity->he_rdata.request.reqtype],
				strlen(request_string[entity->he_rdata.request.reqtype]));
		write(fde->fde_fd, " ", 1);
		write(fde->fde_fd, entity->he_rdata.request.path,
				strlen(entity->he_rdata.request.path));
		write(fde->fde_fd, " HTTP/1.0\r\n", 11);
	}
		
	entity->_he_target = fde;
	entity->_he_hdrbuf = header_build(&entity->he_headers);
	wnet_write(fde->fde_fd, entity->_he_hdrbuf, strlen(entity->_he_hdrbuf),
			entity_send_headers_done, entity);
}

/*ARGSUSED*/
static void
entity_send_headers_done(fde, data, res)
	struct fde *fde;
	void *data;
	int res;
{
struct	http_entity	*entity = data;

	wfree(entity->_he_hdrbuf);

	DEBUG((WLOG_DEBUG, "entity_send_headers_done: called for %d [%s], res=%d", fde->fde_fd, fde->fde_desc, res));
	
	if (res == -1) {
		entity->_he_func(entity, entity->_he_cbdata, -1);
		return;
	}

	if (entity->he_source_type == ENT_SOURCE_NONE) {
		/* no body for this request */
		DEBUG((WLOG_DEBUG, "entity_send_headers_done: no body, return immediately"));
		entity->_he_func(entity, entity->_he_cbdata, 0);
		return;
	}
	
	if (entity->he_source_type == ENT_SOURCE_BUFFER) {
		/* write buffer, callback when done */
		DEBUG((WLOG_DEBUG, "entity_send_headers_done: source is buffer, %d bytes", 
				entity->he_source.buffer.len));
		wnet_write(fde->fde_fd, entity->he_source.buffer.addr,
			       entity->he_source.buffer.len, entity_send_buf_done, entity);
		return;
	}
	
	/* FDE backended write */
	/*
	 * fde_read reads some amount of data (not necessarily all of it), and then calls
	 * wnet_write to write it.  it then unregisters the fd as readable.
	 * when wnet_write completes and calls fde_write_done, it registers the fd as
	 * readable again..
	 */ 
	DEBUG((WLOG_DEBUG, "entity_send_headers_done: source is FDE"));
	wnet_register(entity->he_source.fde->fde_fd, FDE_READ, entity_send_fde_read, entity);
}

static void
entity_send_fde_read(fde)
	struct fde *fde;
{
struct	http_entity	*entity = fde->fde_rdata;
	ssize_t		 len;
	
	DEBUG((WLOG_DEBUG, "entity_send_fde_read: called for %d [%s]", fde->fde_fd, fde->fde_desc));
	
	/*
	 * This is disgusting.
	 * The problem is that when reading headers, the readbuf_getdata buffers too much
	 * data (i.e. part of the response), so we have to process that first.  This should
	 * move to wnet.
	 */
	if (READBUF_DATA_LEFT(&entity->_he_readbuf) > 0) {
		/* XXX for some reason \r\n gets left at the start of the readbuf */
		DEBUG((WLOG_DEBUG, "entity_send_fde_read: %d bytes left in readbuf",
				READBUF_DATA_LEFT(&entity->_he_readbuf) - 2));
		memcpy(entity->_he_rdbuf, READBUF_CUR_POS(&entity->_he_readbuf) + 2,
				READBUF_DATA_LEFT(&entity->_he_readbuf) - 2);
		len = READBUF_DATA_LEFT(&entity->_he_readbuf);
		readbuf_free(&entity->_he_readbuf);
	} else {
		len = read(entity->he_source.fde->fde_fd, entity->_he_rdbuf, 8192);
		wnet_register(entity->he_source.fde->fde_fd, FDE_READ, NULL, NULL);
	}
	
	DEBUG((WLOG_DEBUG, "entity_send_fde_read: read %d bytes", len));
	
	if (len == 0) {
		/* remote closed */
		entity->_he_func(entity, entity->_he_cbdata, 0);
		return;
	}
	
	if (len == -1) {
		if (errno == EAGAIN) {
			/* ? this shouldn't happen */
			abort();
		}
		
		entity->_he_func(entity, entity->_he_cbdata, -1);
		return;
	}
	
	wnet_write(entity->_he_target->fde_fd, entity->_he_rdbuf, len,
			entity_send_fde_write_done, entity);
}

static void
entity_send_fde_write_done(fde, data, res)
	struct fde *fde;
	void *data;
	int res;
{
struct	http_entity	*entity = data;

	wnet_register(entity->he_source.fde->fde_fd, FDE_READ, entity_send_fde_read, entity);
}

static void
entity_send_buf_done(fde, data, res)
	struct fde *fde;
	void *data;
	int res;
{
struct	http_entity	*entity = data;

	DEBUG((WLOG_DEBUG, "entity_send_buf_done: called for %d [%s], res=%d", fde->fde_fd, fde->fde_desc, res));
	entity->_he_func(entity, entity->_he_cbdata, res);
	return;
}
