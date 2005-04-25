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
 
#include "willow.h"
#include "whttp.h"
#include "whttp_entity.h"

static void entity_read_callback(struct fde *, void *);
static int parse_headers(struct http_entity*);
static int parse_reqtype(struct http_entity *, char *, int);

static void entity_send_headers_done(struct fde *, void *, int);
static void entity_send_body_done(struct fde *, void *, int);
static void entity_send_fde_write(struct fde *, void *, int);

void
entity_read_headers(entity, readbuf, func, udata)
	struct http_entity *entity;
	struct readbuf *readbuf;
	header_cb func;
	void *udata;
{
	entity->_hr_data = udata;
	entity->_hr_func = func;
	entity->_hr_readbuf = readbuf;

	/* XXX source for an entity read is _always_ an fde */
	wnet_register(entity->hr_source.fde, FDE_READ, entity_read_callback, entity);
	entity_read_callback(entity->hr_source.fde, entity);
}

static void
entity_read_callback(e, data)
	struct fde *e;
	void *data;
{
struct	http_entity	*entity = data;

	if (READBUF_DATA_LEFT(entity->_hr_readbuf) == 0) {
		switch (readbuf_getdata(entity->he_source.fde->fde_fd, entity->_he_readbuf)) {
		case -1:
			if (errno == EWOULDBLOCK)
				return;
		case 0:
			wnet_register(entity->he_source.fde->fde_fd, FDE_READ, NULL, NULL);
			entity->he_flags.error = 1;
			entity->_he_func(entity->_he_data);
			return;
		}
	}

	if (parse_headers(entity, 0) == -1) {
		wnet_register(entity->he_source.fde->fde_fd, FDE_READ, NULL, NULL);
		entity->he_flags.error = 1;
		entity->_he_func(entity->_he_data);
		return;
	}

	if (entity->_hr_state == HE_STATE_DONE) {
		wnet_register(entity->he_source.fde->fde_fd, FDE_READ, NULL, NULL);
		entity->_he_func(entity->_he_data);
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
parse_headers(entity, isresp)
	struct http_entity *entity;
	int isresp;
{
	while (READBUF_DATA_LEFT(entity->_he_readbuf) > 0) {
		char c = *READBUF_CUR_POS(entity->_he_readbuf);

		switch(entity->_he_ps) {
		case ENTITY_STATE_START:
			/* should be reading a request type */
			switch(c) {
				case '\r':
					*READBUF_CUR_POS(entity->_he_readbuf) = '\0';
					if (parse_reqtype(entity->_he_readbuf->rb_p, isresp) == -1)
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
					entity->_he_state = PS_NL;
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
					client->cl_hdrbuf = client->cl_readbuf.rb_p + client->cl_readbuf.rb_dpos;
					break;
			}
			break;
		case ENTIRY_STATE_HDR:
			switch(c) {
				case ':':
					client->cl_ps = ENTITY_STATE_COLON;
					*READBUF_CUR_POS(&client->cl_readbuf) = '\0';
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
					client->cl_ps = ENTITY_STATE_SPACE;
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
					header_add(&client->cl_headers, client->cl_hdrbuf, 
							client->cl_readbuf.rb_p + client->cl_readbuf.rb_dpos);
					client->cl_ps = ENTITY_STATE_VALUE;
					break;
			}
			break;
		case ENTITY_STATE_VALUE:
			switch(c) {
				case '\r': 
					*READBUF_CUR_POS(&client->cl_readbuf) = '\0';
					client->cl_ps = ENTITY_STATE_CR;
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
					client->cl_ps = ENTITY_STATE_DONE;
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
		READBUF_INC_DATA_POS(&client->cl_readbuf);
	}
	return 0;
}

static int 
parse_reqtype(entity, request, isresp)
	struct http_entity *entity;
	char *request;
	int isresp;
{
	char	*p, *s;

	/*
	 * Ignore responses for now...
	 */
	if (isresp)
		return 0;

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
	
	client->cl_path = strdup(p);

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
 */

void
entity_send(client, entity, cb)
	struct http_client *client;
	struct http_entity *entity;
	header_cb cb;
{
	char	status[4];

	client_log_request(client);

	sprintf(status, "%d", client->cl_response.hr_status);
	write(client->cl_fde->fde_fd, "HTTP/1.0 ", 9);
	write(client->cl_fde->fde_fd, status, strlen(status));
	write(client->cl_fde->fde_fd, " ", 1);
	write(client->cl_fde->fde_fd, client->cl_response.hr_status_str,
			strlen(client->cl_response.hr_status_str));
	write(client->cl_fde->fde_fd, "\r\n", 2);

	client->cl_hdrbuf = header_build(&client->cl_response.hr_headers);
	wnet_write(client->cl_fde->fde_fd, client->cl_hdrbuf, strlen(client->cl_hdrbuf),
			client_send_response_headers_done, client);
}

static void
entity_send_fde_read(e)
	struct fde *e;
{
struct	http_client	*client = e->fde_rdata;
	int		 i;

	/*
	 * Read possible from the backend.
	 *
	 * This is slightly tricky.  Don't ask for more events on this
	 * fd until the client write completes, otherwise we overrun 
	 * ourselves.
	 */
	if (READBUF_DATA_LEFT(&client->cl_readbuf) == 0 && 
			(i = readbuf_getdata(e->fde_fd, &client->cl_readbuf)) < 1) {
		if (READBUF_DATA_LEFT(&client->cl_readbuf) == 0) {
			wnet_close(e->fde_fd);
			if (i == -1 && errno != EWOULDBLOCK) {
				client_send_error(client, ERR_GENERAL, strerror(errno));
			}
			client_close(client);
		}
		return;
	}

	wnet_register(client->cl_fde->fde_fd, FDE_READ, NULL, NULL);
	wnet_write(client->cl_fde->fde_fd, READBUF_CUR_POS(&client->cl_readbuf), 
			READBUF_DATA_LEFT(&client->cl_readbuf), client_send_response_fde_write, client);
}


/*ARGSUSED*/
static void
client_send_response_fde_write(e, data, res)
	struct fde *e;
	void *data;
	int res;
{
struct	http_client	*client = data;

	readbuf_free(&client->cl_readbuf);
	/*
	 * Write to client completed.  Wait for the backend to send more data.
	 */
	if (res == -1) {
		client_close(client);
		return;
	}

	wnet_register(client->cl_backendfde->fde_fd, FDE_READ, client_send_response_fde_read, client);
	client_send_response_fde_read(client->cl_backendfde);
}


/*ARGSUSED*/
static void
client_send_response_headers_done(e, data, res)
	struct fde *e;
	void *data;
	int res;
{
struct	http_client	*client = data;

	wfree(client->cl_hdrbuf);

	if (res == -1) {
		client_close(client);
		return;
	}

	if (client->cl_response.hr_source_type == RESP_SOURCE_BUFFER)
		wnet_write(client->cl_fde->fde_fd, client->cl_response.hr_source.buffer.addr,
			       client->cl_response.hr_source.buffer.len, client_send_response_body_done, client);
	else
		client_send_response_fde_read(client->cl_response.hr_source.fde);
}

/*ARGSUSED*/
static void
client_send_response_body_done(e, data, res)
	struct fde *e;
	void *data;
	int res;
{
struct	http_client	*client = data;

	client_close(client);
}
