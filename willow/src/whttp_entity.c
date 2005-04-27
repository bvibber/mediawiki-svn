/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp_entity: HTTP entity handling.
 */

/* How does this work?
 * 
 * Each HTTP request can be divided into two entities: the request and the
 * response.  The client sends the request, i.e. the headers and possibly
 * a body, to the server, which considers it and sends a reply.
 * 
 * Internally, we read the request headers and ignore the
 * body [entity_read_headers].  We then examine the headers
 * [whttp:client_read_done] and decide if it has a body.  We modify
 * the entry slightly, and send it to the backend with either no source
 * or, if it had a body, the client's FDE as the source [entity_send].
 * We then wait for the server to reply with its header.  When it does
 * [whttp:backend_headers_done], we send the request to the client, using
 * the backend's FDE as the body, if it has one, and close it.
 * 
 * See "Entity sending" below for a detailed description of how entity
 * sending works.
 * 
 * TODO: We don't have to buffer the headers, _but_ it makes things easier
 * for now and doesn't cost much.  if we start not buffering we need to
 * decide what to do when the client goes away unexpectedly.  probably it's
 * easiest to just drop the backend connection (this is wasteful of backends
 * but we don't cache them at the moment anyway).  what do we do when the
 * client sends "Foo: bar\r\n  baz\r\n" and we decide after baz that we
 * shouldn't send that header after all?
 * 
 * There is a trade-off in some places between excessive copying and
 * excessive syscalls.  In some cases we copy data (headers) when we could
 * undo the parser mangling and send them as-is.  IMO this is not likely
 * to be a worthwhile optimisation, needs profiling.
 * 
 * As for FDE backending, Unix sucks:
 * 
 *    The sendfile() function copies data  from  in_fd  to  out_fd starting
 *    at  offset  off and of length len bytes. The in_fd argument should
 *    be a  file  descriptor  to  a  regular  file opened for reading.
 */

#include <sys/sendfile.h>
 
#include <unistd.h>
#include <errno.h>
#include <string.h>
#include <stdlib.h>
#include <stdio.h>
#include <assert.h>
#include <fcntl.h>

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
static void entity_send_file_done(struct fde *, void *, int);
static void *thr_sendfile(void *);

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
entity_read_callback(fde)
	struct fde *fde;
{
struct	http_entity	*entity = fde->fde_rdata;

	DEBUG((WLOG_DEBUG, "entity_read_callback: called"));
	
	if (readbuf_data_left(&entity->he_source.fde->fde_readbuf) == 0) {
		switch (readbuf_getdata(entity->he_source.fde)) {
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
 * I don't like this.  There should be an easier/faster way to do it, but this
 * is  the most understandable form for me...
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
	assert(entity->he_source_type == ENT_SOURCE_FDE);
	
	while (readbuf_data_left(&entity->he_source.fde->fde_readbuf) > 0) {
		char c = *readbuf_cur_pos(&entity->he_source.fde->fde_readbuf);

		switch(entity->_he_state) {
		case ENTITY_STATE_START:
			/* should be reading a request type */
			switch(c) {
				case '\r':
					*readbuf_cur_pos(&entity->he_source.fde->fde_readbuf) = '\0';
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
					entity->_he_hdrbuf = entity->he_source.fde->fde_readbuf.rb_p 
							+ entity->he_source.fde->fde_readbuf.rb_dpos;
					break;
			}
			break;
		case ENTITY_STATE_HDR:
			switch(c) {
				case ':':
					entity->_he_state = ENTITY_STATE_COLON;
					*readbuf_cur_pos(&entity->he_source.fde->fde_readbuf) = '\0';
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
						entity->he_source.fde->fde_readbuf.rb_p + 
							entity->he_source.fde->fde_readbuf.rb_dpos);
					/*
					 * Check for "interesting" headers that we want to do
					 * extra processing on.
					 */
					if (!strcmp(entity->_he_hdrbuf, "Host"))
						entity->he_rdata.request.host = entity->he_source.fde->fde_readbuf.rb_p +
								entity->he_source.fde->fde_readbuf.rb_dpos;
					entity->_he_state = ENTITY_STATE_VALUE;
					break;
			}
			break;
		case ENTITY_STATE_VALUE:
			switch(c) {
				case '\r': 
					*readbuf_cur_pos(&entity->he_source.fde->fde_readbuf) = '\0';
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
					readbuf_inc_data_pos(&entity->he_source.fde->fde_readbuf, 1);
					return 0;
				default:
					return -1;
			}
		case ENTITY_STATE_DONE:
			/*
			 * We're done parsing headers on this client, but they
			 * sent more data.  Shouldn't ever happen, but kill
			 * them if it does.
			 */
			return -1;
		default:
			abort(); /* ??? This should be handled above for
				  * consistency (ENTITY_STATE_BODY) but i
				  * don't know which is correct.
				  */
		}
		readbuf_inc_data_pos(&entity->he_source.fde->fde_readbuf, 1);
	}
	return 0;
}

static int 
parse_reqtype(entity)
	struct http_entity *entity;
{
	char	*p, *s;
	char	*request = entity->he_source.fde->fde_readbuf.rb_p;
	int	i;

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

	for (i = 0; supported_reqtypes[i].name; i++)
		if (!strcmp(request, supported.reqtypes[i].name))
			break;

	entity->he_rdata.request.reqtype = supported.reqtypes[i].type;
	if (entity->he_rdata.request.reqtype == REQTYPE_INVALID)
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
		if (head->hl_flags & HDR_ALLOCED)
			wfree((char *)this->hl_name);
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
	*buf = '\0';
	while (head->hl_next) {
		head = head->hl_next;
		buflen += sprintf(buf + buflen, "%s: %s\r\n", head->hl_name, head->hl_value);
	}
	strcat(buf, "\r\n");

	return buf;
}

void
header_dump(head, fd)
	struct header_list *head;
	int fd;
{
	int i = 0;
struct	header_list	*h;

	h = head->hl_next;
	while (h) {
		h = h->hl_next;
		++i;
	}
	
	write(fd, &i, sizeof(i));	
	
	while (head->hl_next) {
		int i, j;
		head = head->hl_next;
		i = strlen(head->hl_name);
		write(fd, &i, sizeof(i));
		j = strlen(head->hl_value);
		write(fd, &j, sizeof(j));
		write(fd, head->hl_name, i);
		write(fd, head->hl_value, j);
	}
}

void
header_undump(head, fd, len)
	struct header_list *head;
	int fd;
	off_t *len;
{
	int		 i, j, n;
struct	header_list	*it = head;

	*len = 0;
	memset(head, 0, sizeof(*head));
	head->hl_flags |= HDR_ALLOCED;
	*len += read(fd, &n, sizeof(n));
	DEBUG((WLOG_DEBUG, "header_undump: %d entries", n));
	
	while (n--) {
		char *n, *v, *s;
		int k;
		
		it->hl_next = wmalloc(sizeof(struct header_list));
		it = it->hl_next;
		*len += read(fd, &i, sizeof(i));	
		*len += read(fd, &j, sizeof(j));
		DEBUG((WLOG_DEBUG, "header_undump: i=%d j=%d", i, j));
		n = wmalloc(i + j + 2);
		i = read(fd, n, i);
		*len += i;
		s = n + i;
		*s++ = '\0';
		v = s;
		k = read(fd, s, j);
		*len += k;
		s += k;
		*s = '\0';
		it->hl_name = n;
		it->hl_value = v;
		head->hl_len += i + j + 4;
	}
	
	head->hl_tail = it;
	
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
 *   registers a read callback for the source FDE with entity_send_source_read
 *   as the callback.  entity_send_source_read reads the available data,  then
 *   writes it with entity_send_source_write as the callback.  _write CALLS
 *   SOURCE_READ AGAIN.  this is important because otherwise we run into bad
 *   interactions with the edge-triggered wnet [? i don't think this is
 *   actually true but it's what the old code did and it's simpler than
 *   registering in two places].  source_read handles EAGAIN and wnet_register
 *   itself.
 *       
 * entity_send_from_buf:
 *   calls wnet_write on the buffer with entity_send_buf_done as the callback.
 *   entity_send_buf_done calls the user's callback and returns.
 *
 * WARNING: if wnet_write completes immediately, i.e. does not block, it will
 * call your callback before it returns.  after this, the entity may no longer
 * exist.  wnet_write should generally be the last thing a function does
 * before it returns.
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
		struct iovec vec[5];
		
		sprintf(status, "%d", entity->he_rdata.response.status);
		vec[0].iov_base = "HTTP/1.0 ";
		vec[0].iov_len = 9;
		vec[1].iov_base = status;
		vec[1].iov_len = strlen(status);
		vec[2].iov_base = " ";
		vec[2].iov_len = 1;
		vec[3].iov_base = entity->he_rdata.response.status_str;
		vec[3].iov_len = strlen(entity->he_rdata.response.status_str);
		vec[4].iov_base = "\r\n";
		vec[4].iov_len = 2;
		writev(fde->fde_fd, vec, 5);
	} else {
		struct iovec vec[4];
		
		vec[0].iov_base =  request_string[entity->he_rdata.request.reqtype];
		vec[0].iov_len = strlen(request_string[entity->he_rdata.request.reqtype]);
		vec[1].iov_base = " ";
		vec[1].iov_len = 1;
		vec[2].iov_base = entity->he_rdata.request.path;
		vec[2].iov_len = strlen(entity->he_rdata.request.path);
		vec[3].iov_base = " HTTP/1.0\r\n";
		vec[3].iov_len = 11;
		writev(fde->fde_fd, vec, 4);
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
	
	if (entity->he_source_type == ENT_SOURCE_FILE) {
		/* write file */
#ifdef THREADED_IO
		pthread_create(&entity->_he_thread, NULL, thr_sendfile, entity);
#else
		wnet_sendfile(fde->fde_fd, entity->he_source.fd.fd, 
			entity->he_source.fd.size - entity->he_source.fd.off,
			entity->he_source.fd.off, entity_send_file_done, entity);
#endif
		return;
	}
	
	/* FDE backended write */
	/*
	 * fde_read reads some amount of data (not necessarily all of it), and
	 * then calls wnet_write to write it.  it then unregisters the fd as
	 * readable. when wnet_write completes and calls fde_write_done, it
	 * registers the fd as readable again..
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
	DEBUG((WLOG_DEBUG, "entity_send_fde_read: %d bytes left in readbuf",
			readbuf_data_left(&entity->he_source.fde->fde_readbuf)));

	if (readbuf_data_left(&entity->he_source.fde->fde_readbuf) == 0) {
		len = readbuf_getdata(fde);
	} else
		len = readbuf_data_left(&entity->he_source.fde->fde_readbuf);
	
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
	
	if (entity->he_cache_callback) {
		entity->he_cache_callback(readbuf_cur_pos(&entity->he_source.fde->fde_readbuf),
				readbuf_data_left(&entity->he_source.fde->fde_readbuf),
				entity->he_cache_callback_data);
	}
	
	wnet_write(entity->_he_target->fde_fd, readbuf_cur_pos(&entity->he_source.fde->fde_readbuf),
			readbuf_data_left(&entity->he_source.fde->fde_readbuf), entity_send_fde_write_done, entity);
	readbuf_inc_data_pos(&entity->he_source.fde->fde_readbuf, readbuf_data_left(&entity->he_source.fde->fde_readbuf));
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

static void
entity_send_file_done(fde, data, res)
	struct fde *fde;
	void *data;
	int res;
{
struct	http_entity	*entity = data;

	DEBUG((WLOG_DEBUG, "entity_send_file_done: called for %d [%s], res=%d", fde->fde_fd, fde->fde_desc, res));
	entity->_he_func(entity, entity->_he_cbdata, res);
	return;
}	

#ifdef THREADED_IO
static void *
thr_sendfile(data)
	void *data;
{
struct	http_entity	*entity = data;
	int i, val;

	val = fcntl(entity->_he_target->fde_fd, F_GETFL, 0);
	fcntl(entity->_he_target->fde_fd, F_SETFL, val & ~O_NONBLOCK);

	i = sendfile(entity->_he_target->fde_fd, entity->he_source.fd.fd, &entity->he_source.fd.off,
			entity->he_source.fd.size - entity->he_source.fd.off);

	val = fcntl(entity->_he_target->fde_fd, F_GETFL, 0);
	fcntl(entity->_he_target->fde_fd, F_SETFL, val | O_NONBLOCK);
	
	entity->_he_func(entity, entity->_he_cbdata, i);
	return NULL;
}
#endif
