/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp_entity: HTTP entity handling.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

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

#include <sys/uio.h>

#include <unistd.h>
#include <errno.h>
#include <string.h>
#include <stdlib.h>
#include <stdio.h>
#include <assert.h>
/*LINTED*/
#include <fcntl.h>
#include <strings.h>

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
static int validhost(const char *);

static void entity_send_headers_done(struct fde *, void *, int);
static void entity_send_fde_write_done(struct fde *, void *, int);
static void entity_send_buf_done(struct fde *, void *, int);
static void entity_send_fde_read(struct fde *);
static void entity_send_file_done(struct fde *, void *, int);

const char *ent_errors[] = {
	/* 0  */	"Unknown error",
	/* -1 */	"Read error",
	/* -2 */	"Could not parse request headers",
	/* -3 */	"Invalid Host",
	/* -4 */	"Invalid request type",
	/* -5 */	"Too many headers",
};
	
void
entity_free(entity)
	struct http_entity *entity;
{
	if (!entity->he_flags.response)
		if (entity->he_rdata.request.path)
			wfree(entity->he_rdata.request.path);
	header_free(&entity->he_headers);
}

void
entity_read_headers(entity, func, udata)
	struct http_entity *entity;
	header_cb func;
	void *udata;
{
	entity->_he_cbdata = udata;
	entity->_he_func = func;

	WDEBUG((WLOG_DEBUG, "entity_read_headers: starting"));
	/* XXX source for an entity header read is _always_ an fde */
	wnet_register(entity->he_source.fde.fde->fde_fd, FDE_READ, entity_read_callback, entity);
	//entity_read_callback(entity->he_source.fde);
}

static void
entity_read_callback(fde)
	struct fde *fde;
{
struct	http_entity	*entity = fde->fde_rdata;
	int		 i;
	
	WDEBUG((WLOG_DEBUG, "entity_read_callback: called, source %d, left=%d", 
			entity->he_source.fde.fde->fde_fd, 
			readbuf_data_left(&entity->he_source.fde.fde->fde_readbuf)));
	
	if (readbuf_data_left(&entity->he_source.fde.fde->fde_readbuf) == 0) {
		switch (readbuf_getdata(entity->he_source.fde.fde)) {
		case -1:
			WDEBUG((WLOG_DEBUG, "entity_read_callback: readbuf_getdata returned -1, errno=%d %s", 
					errno, strerror(errno)));
			if (errno == EWOULDBLOCK)
				return;
		/*FALLTHRU*/
		case 0:
			WDEBUG((WLOG_DEBUG, "entity_read_callback: readbuf_getdata returned 0"));
			wnet_register(entity->he_source.fde.fde->fde_fd, FDE_READ, NULL, NULL);
			entity->he_flags.error = 1;
			entity->_he_func(entity, entity->_he_cbdata, -1);
			return;
		}
	}

	WDEBUG((WLOG_DEBUG, "entity_read_callback: running header parse, %d left in buffer",
			readbuf_data_left(&entity->he_source.fde.fde->fde_readbuf)));
	if ((i = parse_headers(entity)) < 0) {
		WDEBUG((WLOG_DEBUG, "entity_read_callback: parse_headers returned -1"));
		wnet_register(entity->he_source.fde.fde->fde_fd, FDE_READ, NULL, NULL);
		entity->he_flags.error = 1;
		entity->_he_func(entity, entity->_he_cbdata, i);
		return;
	}

	if (entity->_he_state == ENTITY_STATE_DONE) {
		WDEBUG((WLOG_DEBUG, "entity_read_callback: client is ENTITY_STATE_DONE"));
		wnet_register(entity->he_source.fde.fde->fde_fd, FDE_READ, NULL, NULL);
		entity->_he_func(entity, entity->_he_cbdata, 0);
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
	
	while (readbuf_data_left(&entity->he_source.fde.fde->fde_readbuf) > 0) {
		char c = *readbuf_cur_pos(&entity->he_source.fde.fde->fde_readbuf);

		if (c == 0)
			return ENT_ERR_INVHDR; /* NUL not allowed */
		
		switch(entity->_he_state) {
		case ENTITY_STATE_START:
			/* should be reading a request type */
			switch(c) {
				case '\r':
					*readbuf_cur_pos(&entity->he_source.fde.fde->fde_readbuf) = '\0';
					if (parse_reqtype(entity) == -1)
						return ENT_ERR_INVREQ;
					entity->_he_state = ENTITY_STATE_CR;
					break;
				case '\n':
					return ENT_ERR_INVHDR;
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
					return ENT_ERR_INVHDR;
			}
			break;
		case ENTITY_STATE_NL:
			switch(c) {
				case '\r':
					entity->_he_state = ENTITY_STATE_CREMPTY;
					break;
				case '\n': case ' ': case ':':
					return ENT_ERR_INVHDR;
				default: /* header name */
					entity->_he_state = ENTITY_STATE_HDR;
					entity->_he_hdrbuf = entity->he_source.fde.fde->fde_readbuf.rb_p 
							+ entity->he_source.fde.fde->fde_readbuf.rb_dpos;
					entity->_he_lastname = entity->_he_hdrbuf;
					break;
			}
			break;
		case ENTITY_STATE_HDR:
			switch(c) {
				case ':':
					entity->_he_state = ENTITY_STATE_COLON;
					*readbuf_cur_pos(&entity->he_source.fde.fde->fde_readbuf) = '\0';
					break;
				case ' ': case '\r': case '\n':
					return ENT_ERR_INVHDR;
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
					return ENT_ERR_INVHDR;
			}
			break;
		case ENTITY_STATE_SPACE:
			switch(c) {
				case '\r': case '\n': case ':': case ' ':
					return ENT_ERR_INVHDR;
				default:
					entity->_he_valstart = entity->he_source.fde.fde->fde_readbuf.rb_p +
							entity->he_source.fde.fde->fde_readbuf.rb_dpos;
					entity->_he_state = ENTITY_STATE_VALUE;
					break;
			}
			break;
		case ENTITY_STATE_VALUE:
			switch(c) {
				case '\r': 
					*readbuf_cur_pos(&entity->he_source.fde.fde->fde_readbuf) = '\0';
					/*
					 * Check for "interesting" headers that we want to do
					 * extra processing on.
					 */
					if (entity->he_headers.hl_num++ > MAX_HEADERS)
						return ENT_ERR_2MANY;
					header_add(&entity->he_headers, entity->_he_lastname, entity->_he_valstart);
					if (!strcmp(entity->_he_lastname, "Host")) {
						if (!validhost(entity->_he_valstart))
							return ENT_ERR_INVHOST;
						
						entity->he_rdata.request.host = entity->_he_valstart;
						WDEBUG((WLOG_DEBUG, "host: [%s]", entity->_he_valstart));
					} else if (!strcmp(entity->_he_lastname, "Content-Length")) {
						char *cl = entity->_he_valstart;
						entity->he_rdata.request.contlen = atoi(cl);
						WDEBUG((WLOG_DEBUG, "got content-length: %d [%s]", 
								entity->he_rdata.request.contlen, cl));
					}
					entity->_he_state = ENTITY_STATE_CR;
					break;
				case '\n': 
					return ENT_ERR_INVHDR;
				default:
					break;
			}
			break;
		case ENTITY_STATE_CREMPTY:
			switch(c) {
				case '\n':
					entity->_he_state = ENTITY_STATE_DONE;
					readbuf_inc_data_pos(&entity->he_source.fde.fde->fde_readbuf, 1);
					return 0;
				default:
					return ENT_ERR_INVHDR;
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
		readbuf_inc_data_pos(&entity->he_source.fde.fde->fde_readbuf, 1);
	}
	return 0;
}

static int 
parse_reqtype(entity)
	struct http_entity *entity;
{
	char	*p, *s;
	char	*request = entity->he_source.fde.fde->fde_readbuf.rb_p;
	int	i;

	WDEBUG((WLOG_DEBUG, "parse_reqtype: called, response=%d", (int)entity->he_flags.response));
	
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
		
		WDEBUG((WLOG_DEBUG, "parse_reqtype: \"%s\" \"%d\" \"%s\"",
				request, entity->he_rdata.response.status,
				entity->he_rdata.response.status_str));
		return 0;
	}	
	
	/* GET */
	if ((p = strchr(request, ' ')) == NULL)
		return -1;

	*p++ = '\0';

	for (i = 0; supported_reqtypes[i].name; i++)
		if (!strcmp(request, supported_reqtypes[i].name))
			break;

	entity->he_rdata.request.reqtype = supported_reqtypes[i].type;
	if (entity->he_rdata.request.reqtype == REQTYPE_INVALID)
		return -1;

	/* /path/to/file */
	if ((s = strchr(p, ' ')) == NULL)
		return -1;

	*s++ = '\0';
	
	entity->he_rdata.request.path = wstrdup(p);

	/* HTTP/1.0 */
	/*
	 * Ignore this for now...
	 */
	return 0;
}

#ifdef __lint
# pragma error_messages(off, E_GLOBAL_COULD_BE_STATIC)
#endif
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
		if (this->hl_flags & HDR_ALLOCED)
			wfree((char *)this->hl_name);
		wfree(this);
	}

	bzero(head, sizeof(*head));
}
#ifdef __lint
# pragma error_messages(default, E_GLOBAL_COULD_BE_STATIC)
#endif

void
header_add(head, name, value)
	struct header_list *head;
	const char *name, *value;
{
struct	header_list	*new = head;

	head->hl_num++;
	
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
	new->hl_flags = 0;
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

#ifdef __lint
# pragma error_messages(off, E_GLOBAL_COULD_BE_STATIC)
#endif
char *
header_build(head)
	struct header_list *head;
{
	char	*buf;
	size_t	 bufsz;
	size_t	 buflen = 0;

	bufsz = head->hl_len + 3;
	if ((buf = wmalloc(bufsz)) == NULL)
		outofmemory();
	
	*buf = '\0';
	while (head->hl_next) {
		head = head->hl_next;
		buflen += snprintf(buf + buflen, bufsz - buflen - 1, "%s: %s\r\n", head->hl_name, head->hl_value);
	}
	if (strlcat(buf, "\r\n", bufsz) >= bufsz )
		abort();

	return buf;
}
#ifdef __lint
# pragma error_messages(default, E_GLOBAL_COULD_BE_STATIC)
#endif

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

int
header_undump(head, fd, len)
	struct header_list *head;
	int fd;
	off_t *len;
{
	int		 i = 0, j = 0, n = 0;
struct	header_list	*it = head;
	ssize_t		 r;
	
	*len = 0;
	bzero(head, sizeof(*head));
	head->hl_flags |= HDR_ALLOCED;
	if ((r = read(fd, &n, sizeof(n))) < 0) {
		wlog(WLOG_WARNING, "reading cache file: %s", strerror(errno));
		return -1; /* XXX */
	}
	
	*len += r;
	WDEBUG((WLOG_DEBUG, "header_undump: %d entries", n));

	while (n--) {
		char *n, *v, *s;
		int k;
		
		if ((it->hl_next = wcalloc(1, sizeof(struct header_list))) == NULL)
			outofmemory();
		it = it->hl_next;
		*len += read(fd, &i, sizeof(i));	
		*len += read(fd, &j, sizeof(j));
		WDEBUG((WLOG_DEBUG, "header_undump: i=%d j=%d", i, j));
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
		it->hl_flags = HDR_ALLOCED;
		head->hl_len += i + j + 4;
	}
	
	head->hl_tail = it;
	return 0;
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
entity_send(fde, entity, cb, data, flags)
	struct fde *fde;
	struct http_entity *entity;
	header_cb cb;
	void *data;
	int flags;
{
	char	status[4];
	int	wn_flags = 0;

	entity->_he_func = cb;
	entity->_he_cbdata = data;
	
	WDEBUG((WLOG_DEBUG, "entity_send: writing to %d [%s]", fde->fde_fd, fde->fde_desc));
	
	if (flags & ENT_IMMED) {
		wnet_set_blocking(fde->fde_fd);
		wn_flags = WNET_IMMED;
		entity->he_flags.immed = 1;
	}

	if (entity->he_flags.response) {
		struct iovec vec[5];
		
		safe_snprintf(4, (status, 4, "%d", entity->he_rdata.response.status));
		vec[0].iov_base = "HTTP/1.0 ";
		vec[0].iov_len = 9;
		vec[1].iov_base = status;
		vec[1].iov_len = strlen(status);
		vec[2].iov_base = " ";
		vec[2].iov_len = 1;
		vec[3].iov_base = (void *)entity->he_rdata.response.status_str;
		vec[3].iov_len = strlen(entity->he_rdata.response.status_str);
		vec[4].iov_base = "\r\n";
		vec[4].iov_len = 2;
		writev(fde->fde_fd, vec, 5);
	} else {
		struct iovec vec[4];
		
		vec[0].iov_base = (void *)request_string[entity->he_rdata.request.reqtype];
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
			entity_send_headers_done, entity, wn_flags);
}

/*ARGSUSED*/
static void
entity_send_headers_done(fde, data, res)
	struct fde *fde;
	void *data;
	int res;
{
struct	http_entity	*entity = data;
	int		 wn_flags = 0;

	if (entity->he_flags.immed)
		wn_flags = WNET_IMMED;

	wfree(entity->_he_hdrbuf);

	WDEBUG((WLOG_DEBUG, "entity_send_headers_done: called for %d [%s], res=%d", fde->fde_fd, fde->fde_desc, res));
	
	if (res == -1) {
		entity->_he_func(entity, entity->_he_cbdata, -1);
		return;
	}

	if (entity->he_source_type == ENT_SOURCE_NONE) {
		/* no body for this request */
		WDEBUG((WLOG_DEBUG, "entity_send_headers_done: no body, return immediately"));
		entity->_he_func(entity, entity->_he_cbdata, 0);
		return;
	}
	
	if (entity->he_source_type == ENT_SOURCE_BUFFER) {
		/* write buffer, callback when done */
		WDEBUG((WLOG_DEBUG, "entity_send_headers_done: source is buffer, %d bytes", 
				entity->he_source.buffer.len));
		wnet_write(fde->fde_fd, entity->he_source.buffer.addr,
			       entity->he_source.buffer.len, entity_send_buf_done, entity, wn_flags);
		return;
	}
	
	if (entity->he_source_type == ENT_SOURCE_FILE) {
		/* write file */
		if (wnet_sendfile(fde->fde_fd, entity->he_source.fd.fd, 
			entity->he_source.fd.size - entity->he_source.fd.off,
			entity->he_source.fd.off, entity_send_file_done, entity, wn_flags) == -1) {
			entity->_he_func(entity, entity->_he_cbdata, -1);
		}
		return;
	}
	
	/* FDE backended write */
	/*
	 * fde_read reads some amount of data (not necessarily all of it), and
	 * then calls wnet_write to write it.  it then unregisters the fd as
	 * readable. when wnet_write completes and calls fde_write_done, it
	 * registers the fd as readable again..
	 */ 
	WDEBUG((WLOG_DEBUG, "entity_send_headers_done: source is FDE"));
	/* FDE backended writes _cannot_ be immediate... */
	assert(!entity->he_flags.immed);
	entity->he_source.fde._wrt = entity->he_source.fde.len;
	wnet_register(entity->he_source.fde.fde->fde_fd, FDE_READ, entity_send_fde_read, entity);
	entity_send_fde_read(entity->he_source.fde.fde);
}

static void
entity_send_fde_read(fde)
	struct fde *fde;
{
struct	http_entity	*entity = fde->fde_rdata;
	ssize_t		 len;
	size_t		 wrt;
	void		*cur_pos;
	
	WDEBUG((WLOG_DEBUG, "entity_send_fde_read: called for %d [%s]", fde->fde_fd, fde->fde_desc));
	WDEBUG((WLOG_DEBUG, "entity_send_fde_read: %d bytes left in readbuf",
			readbuf_data_left(&entity->he_source.fde.fde->fde_readbuf)));

	if (entity->he_source.fde._wrt == 0) {
		entity->_he_func(entity, entity->_he_cbdata, 0);
		return;
	}
	
	if (readbuf_data_left(&entity->he_source.fde.fde->fde_readbuf) == 0) {
		len = readbuf_getdata(fde);
	} else
		len = readbuf_data_left(&entity->he_source.fde.fde->fde_readbuf);
	
	WDEBUG((WLOG_DEBUG, "entity_send_fde_read: read %d bytes", len));
	
	if (len == 0) {
		/* remote closed */
		entity->_he_func(entity, entity->_he_cbdata, 0);
		return;
	}
	
	if (len == -1) {
		if (errno == EAGAIN) {
			return;
		}
		
		entity->_he_func(entity, entity->_he_cbdata, -1);
		return;
	}
	
	if (entity->he_cache_callback) {
		entity->he_cache_callback(readbuf_cur_pos(&entity->he_source.fde.fde->fde_readbuf),
				readbuf_data_left(&entity->he_source.fde.fde->fde_readbuf),
				entity->he_cache_callback_data);
	}
	
	if (entity->he_source.fde._wrt == -1)
		wrt = readbuf_data_left(&entity->he_source.fde.fde->fde_readbuf);
	else {
		wrt = min(readbuf_data_left(&entity->he_source.fde.fde->fde_readbuf),
				entity->he_source.fde._wrt);
		entity->he_source.fde._wrt -= wrt;
	}

	WDEBUG((WLOG_DEBUG, "_wrt=%d, writing %d", entity->he_source.fde._wrt, wrt));
	cur_pos = readbuf_cur_pos(&entity->he_source.fde.fde->fde_readbuf);
	readbuf_inc_data_pos(&entity->he_source.fde.fde->fde_readbuf, wrt);
	wnet_write(entity->_he_target->fde_fd, cur_pos,
			wrt, entity_send_fde_write_done, entity, 0);
}

/*ARGSUSED*/
static void
entity_send_fde_write_done(fde, data, res)
	struct fde *fde;
	void *data;
	int res;
{
struct	http_entity	*entity = data;

	WDEBUG((WLOG_DEBUG, "entity_send_fde_write_done: called"));
	
	if (entity->he_source.fde._wrt == 0) {
		wnet_register(entity->he_source.fde.fde->fde_fd, FDE_READ, NULL, NULL);
		WDEBUG((WLOG_DEBUG, "entity_send_fde_write_done: _wrt = 0, fd=%d, target=%d",
				entity->he_source.fde.fde->fde_fd, entity->_he_target->fde_fd));
		readbuf_free(&entity->_he_target->fde_readbuf);
		entity->_he_func(entity, entity->_he_cbdata, 0);
		return;
	}
	
	wnet_register(entity->he_source.fde.fde->fde_fd, FDE_READ, entity_send_fde_read, entity);
}

/*ARGSUSED*/
static void
entity_send_buf_done(fde, data, res)
	struct fde *fde;
	void *data;
	int res;
{
struct	http_entity	*entity = data;

	WDEBUG((WLOG_DEBUG, "entity_send_buf_done: called for %d [%s], res=%d", fde->fde_fd, fde->fde_desc, res));
	entity->_he_func(entity, entity->_he_cbdata, res);
	return;
}

/*ARGSUSED*/
static void
entity_send_file_done(fde, data, res)
	struct fde *fde;
	void *data;
	int res;
{
struct	http_entity	*entity = data;

	WDEBUG((WLOG_DEBUG, "entity_send_file_done: called for %d [%s], res=%d", fde->fde_fd, fde->fde_desc, res));
	entity->_he_func(entity, entity->_he_cbdata, res);
	return;
}	

static int
validhost(host)
	const char *host;
{
	for (; *host; ++host) {
		WDEBUG((WLOG_DEBUG, "char %c, char_table[%d] = %d", *host, 
				(int)(unsigned char)*host, char_table[(unsigned char)*host]));
		if (!(char_table[(unsigned char)*host] & CHAR_HOST))
			return 0;
	}
	return 1;
}
