/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp: HTTP implementation.
 */

#include <sys/param.h>

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <errno.h>
#include <netdb.h>

#include "willow.h"
#include "whttp.h"
#include "wnet.h"
#include "wbackend.h"

#ifndef MAXHOSTNAMELEN
# define MAXHOSTNAMELEN HOST_NAME_MAX
#endif

#define MAX_HEADERS	25
#define RDBUF_INC	8192	/* buffer in 8KB incrs	*/

#define REQTYPE_GET	1
#define REQTYPE_POST	2

#define PS_START	0
#define PS_CR		1
#define PS_NL		2
#define PS_HDR		3
#define PS_COLON	4
#define PS_SPACE	5
#define PS_VALUE	6
#define PS_CREMPTY	7
#define PS_DONE		8
#define PS_BODY		9

struct header_list {
	const char	*hl_name;
	const char	*hl_value;
struct	header_list	*hl_next;
};

struct readbuf {
	char	*rb_p;		/* start of allocated region	*/
	int	 rb_size;	/* size of allocated region	*/
	int	 rb_dsize;	/* [p,p+dsize) is valid data	*/
	int	 rb_dpos;	/* current data position	*/
};
#define READBUF_SPARE_SIZE(b) ((b)->rb_size - (b)->rb_dsize)
#define READBUF_SPARE_START(b) ((b)->rb_p + (b)->rb_dsize)
#define READBUF_DATA_LEFT(b) ((b)->rb_dsize - (b)->rb_dpos)
#define READBUF_INC_DATA_POS(b) ((b)->rb_dpos++)
#define READBUF_CUR_POS(b) ((b)->rb_p + (b)->rb_dpos)

struct http_client {
struct	readbuf		 cl_readbuf;			/* read buffer				*/
struct	header_list	 cl_headers;			/* HTTP headers				*/
struct	fde		*cl_fde;			/* backref to fd			*/
	int		 cl_reqtype;			/* request type or 0			*/
	int		 cl_ps;				/* parse state				*/
	char		*cl_path;			/* path they want			*/
	char		*cl_wrtbuf;			/* write buf (either to client or be)	*/
struct	backend		*cl_backend;			/* backend servicing this client	*/
struct	fde		*cl_backendfde;			/* fde for backend			*/
	char 		*cl_hdrbuf;			/* temp offset for hdr parsing		*/
};

static int http_read(struct fde *);
static int parse_headers(struct http_client *, int);
static int parse_reqtype(struct http_client *, char *, int);
static void client_close(struct http_client *);
static void proxy_request(struct http_client *);
static void proxy_start_backend(struct backend *, struct fde *, void *);
static int proxy_backend_read(struct fde *);
static void proxy_backend_write(struct fde *, void *, int);
static void proxy_backend_write_request(struct fde *, void *, int);
static void proxy_write_done(struct fde *, void *, int);
static void proxy_write_done_request(struct fde *, void *, int);
static int readbuf_getdata(int fd, struct readbuf *);
static void readbuf_free(struct readbuf *);
static void readbuf_reset(struct readbuf *);
static void header_add(struct header_list *, const char *, const char *);
static void header_free(struct header_list *);
static char *header_build(struct header_list *);

static char via_hdr[1024];

void
whttp_init(void)
{
	char	hostname[MAXHOSTNAMELEN + 1];

	if (gethostname(hostname, MAXHOSTNAMELEN) < 0) {
		perror("gethostname");
		exit(8);
	}

	sprintf(via_hdr, "1.0 %s (Willow/" VERSION ")", hostname);
}


static struct http_client *
new_client(e)
	struct fde *e;
{
struct	http_client	*cl;

	if ((cl = wmalloc(sizeof(*cl))) == NULL) {
		fputs("out of memory\n", stderr);
		abort();
	}

	memset(cl, 0, sizeof(*cl));
	cl->cl_fde = e;
	return cl;
}

static void
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

static void
header_add(head, name, value)
	struct header_list *head;
	const char *name, *value;
{
	while (head->hl_next)
		head = head->hl_next;
	head->hl_next = wmalloc(sizeof(*head->hl_next));
	head = head->hl_next;
	head->hl_name = name;
	head->hl_value = value;
	head->hl_next = NULL;
}

static char *
header_build(head)
	struct header_list *head;
{
	char	*buf = NULL;
	size_t	 bufsz = 0;
	size_t	 buflen = 0;
	size_t	 newsize, need;

	while (head->hl_next) {
		head = head->hl_next;

		newsize = strlen(head->hl_name) + strlen(head->hl_value) + 7;
		need = buflen + newsize;

		if (need > bufsz)
			buf = realloc(buf, need);

		bufsz = need;
		buflen += sprintf(buf + buflen, "%s: %s\r\n", head->hl_name, head->hl_value);
	}
	strcat(buf, "\r\n");

	return buf;
}

void
http_new(e)
	struct fde *e;
{
struct	http_client	*cl;

	cl = new_client(e);
	wnet_register(e->fde_fd, FDE_READ, http_read, cl);
	return;
}

static void
client_close(client)
	struct http_client *client;
{
	readbuf_free(&client->cl_readbuf);
	wnet_close(client->cl_fde->fde_fd);
	wfree(client);
}

static int
readbuf_getdata(fd, buffer)
	struct readbuf *buffer;
{
	int	i;

	for (;;) {
		if (READBUF_SPARE_SIZE(buffer) == 0) {
			buffer->rb_size += RDBUF_INC;
			buffer->rb_p = realloc(buffer->rb_p, buffer->rb_size);
		}

		if ((i = read(fd, READBUF_SPARE_START(buffer), READBUF_SPARE_SIZE(buffer))) < 1)
			return i;
		buffer->rb_dsize += i;

	}
	return 1;
}

static void
readbuf_free(buffer)
	struct readbuf *buffer;
{
	if (buffer->rb_p)
		wfree(buffer->rb_p);
	memset(buffer, 0, sizeof(*buffer));
}

static void
readbuf_reset(buffer)
	struct readbuf *buffer;
{
	buffer->rb_dpos = buffer->rb_dsize = 0;
}

static int
http_read(e)
	struct fde *e;
{
struct	http_client	*c = e->fde_rdata;
	int		 i;
	
	if ((i = readbuf_getdata(e->fde_fd, &c->cl_readbuf)) < 1) {
		if (errno == EWOULDBLOCK && (READBUF_DATA_LEFT(&c->cl_readbuf) == 0))
			return 0;
		else if (i == 0 || (i == -1 && errno != EWOULDBLOCK)) {
			client_close(c);
			return 1;
		}
	}
	if (parse_headers(c, 0) == -1) {
		/* parse error */
		client_close(c);
		return 1;
	}
	if (c->cl_ps == PS_DONE) {
		proxy_request(c);
		/*
		 * Don't care about this fd now.  If we're ever interested
		 * in it again, it'll be reregistered.
		 */
		return 1;
	}
	return 0;
}

static int
parse_headers(client, isresp)
	struct http_client *client;
{
	while (READBUF_DATA_LEFT(&client->cl_readbuf) > 0) {
		char c = *READBUF_CUR_POS(&client->cl_readbuf);

		switch(client->cl_ps) {
		case PS_START:
			/* should be reading a request type */
			switch(c) {
				case '\r':
					*READBUF_CUR_POS(&client->cl_readbuf) = '\0';
					if (parse_reqtype(client, client->cl_readbuf.rb_p, isresp) == -1)
						return -1;
					client->cl_ps = PS_CR;
					break;
				case '\n':
					return -1;
				default:
					break;
			}
			break;
		case PS_CR:
			switch(c) {
				case '\n':
					client->cl_ps = PS_NL;
					break;
				default:
					return -1;
			}
			break;
		case PS_NL:
			switch(c) {
				case '\r':
					client->cl_ps = PS_CREMPTY;
					break;
				case '\n': case ' ': case ':':
					return -1;
				default: /* header name */
					client->cl_ps = PS_HDR;
					client->cl_hdrbuf = client->cl_readbuf.rb_p + client->cl_readbuf.rb_dpos;
					break;
			}
			break;
		case PS_HDR:
			switch(c) {
				case ':':
					client->cl_ps = PS_COLON;
					*READBUF_CUR_POS(&client->cl_readbuf) = '\0';
					break;
				case ' ': case '\r': case '\n':
					return -1;
				default:
					break;
			}
			break;
		case PS_COLON:
			switch(c) {
				case ' ':
					client->cl_ps = PS_SPACE;
					break;
				default:
					return -1;
			}
			break;
		case PS_SPACE:
			switch(c) {
				case '\r': case '\n': case ':': case ' ':
					return -1;
				default:
					header_add(&client->cl_headers, client->cl_hdrbuf, 
							client->cl_readbuf.rb_p + client->cl_readbuf.rb_dpos);
					client->cl_ps = PS_VALUE;
					break;
			}
			break;
		case PS_VALUE:
			switch(c) {
				case '\r': 
					*READBUF_CUR_POS(&client->cl_readbuf) = '\0';
					client->cl_ps = PS_CR;
					break;
				case '\n': 
					return -1;
				default:
					break;
			}
			break;
		case PS_CREMPTY:
			switch(c) {
				case '\n':
					client->cl_ps = PS_DONE;
					return 0;
				default:
					return -1;
			}
			break;
		case PS_DONE:
			/*
			 * We're done parsing headers on this client, but they sent
			 * more data.  Shouldn't ever happen, but kill them if it does.
			 */
			return -1;
		default:
			abort();
		}
		READBUF_INC_DATA_POS(&client->cl_readbuf);
	}
	return 0;
}

static int 
parse_reqtype(client, request, isresp)
	struct http_client *client;
	char *request;
{
	char	*p;

	/*
	 * Ignore responses for now...
	 */
	if (isresp)
		return 0;

	/* GET */
	if ((p = strchr(request, ' ')) == NULL)
		return -1;

	*p++ = '\0';

	if (!strcmp(request, "GET"))
		client->cl_reqtype = REQTYPE_GET;
	else if (!strcmp(request, "POST"))
		client->cl_reqtype = REQTYPE_POST;
	else
		return -1;

	client->cl_path = p;

	/* /path/to/file */
	if ((p = strchr(p, ' ')) == NULL)
		return -1;

	*p++ = '\0';
	
	/* HTTP/1.0 */
	/*
	 * Ignore this for now...
	 */
	return 0;
}

static void
proxy_request(client)
	struct http_client *client;
{
	if (get_backend(proxy_start_backend, client) == -1) {
		client_close(client);
		return;
	}
}
	
static void
proxy_start_backend(backend, e, data)
	struct backend *backend;
	struct fde *e;
	void *data;
{
struct	http_client	*client = data;
	int		 i;
	size_t		 bufsz;
	char		*wrtbuf;
struct	header_list	 response_headers, *it;
	
	client->cl_backend = backend;
	client->cl_backendfde = e;

	memset(&response_headers, 0, sizeof(response_headers));

	bufsz = strlen(client->cl_path) + 15;
	client->cl_wrtbuf = wmalloc(bufsz + 1);
	sprintf(client->cl_wrtbuf, "GET %s HTTP/1.0\r\n", client->cl_path);

	for (it = client->cl_headers.hl_next; it; it = it->hl_next) {
		if (!strcmp(it->hl_name, "Connection"))
			continue;
		header_add(&response_headers, it->hl_name, it->hl_value);
	}

	header_add(&response_headers, "X-Forwarded-For", client->cl_fde->fde_straddr);
	client->cl_hdrbuf = header_build(&response_headers);	
	header_free(&response_headers);

	wnet_write(e->fde_fd, client->cl_wrtbuf, bufsz, proxy_write_done_request, client);
}

static void
proxy_write_done_request(e, data, i)
	struct fde *e;
	void *data;
{
struct	http_client	*client = data;

	wfree(client->cl_wrtbuf);
	wnet_write(e->fde_fd, client->cl_hdrbuf, strlen(client->cl_hdrbuf), proxy_write_done, client);
}

static void
proxy_write_done(e, data, i)
	struct fde *e;
	void *data;
{
struct	http_client	*client = data;

	wfree(client->cl_hdrbuf);

	if (i == -1) {
		client_close(client);
		wnet_close(e->fde_fd);
		return;
	}
		
	/*
	 * Re-appropriate the readbuf for the backend...
	 */
	readbuf_reset(&client->cl_readbuf);
	client->cl_ps = PS_START;
	header_free(&client->cl_headers);

	wnet_register(e->fde_fd, FDE_READ, proxy_backend_read, client);
}	

static int
proxy_backend_read(e)
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
	if ((i = readbuf_getdata(e->fde_fd, &client->cl_readbuf)) < 1) {
		if (READBUF_DATA_LEFT(&client->cl_readbuf) == 0) {
			wnet_close(e->fde_fd);
			client_close(client);
			return 1;
		}
	}

	if (client->cl_ps < PS_DONE) {
		if (parse_headers(client, 1) == -1) {
			wnet_close(e->fde_fd);
			client_close(client);
			return 1;
		}
		if (client->cl_ps == PS_DONE) {
			struct header_list *head;
			struct header_list response_headers;
			memset(&response_headers, 0, sizeof(response_headers));

			for (head = client->cl_headers.hl_next; head; head = head->hl_next) {
				header_add(&response_headers, head->hl_name, head->hl_value);
			}
			header_add(&response_headers, "Via", via_hdr);
			client->cl_hdrbuf = header_build(&response_headers);
			client->cl_ps = PS_BODY;

			wnet_write(client->cl_fde->fde_fd, "HTTP/1.0 200 OK\r\n", 17, proxy_backend_write_request, client);
			return 1;
		}
		return 0;
	}

	wnet_write(client->cl_fde->fde_fd, READBUF_CUR_POS(&client->cl_readbuf), 
			READBUF_DATA_LEFT(&client->cl_readbuf), proxy_backend_write, client);
	return 0;
}

static void
proxy_backend_write_header(e, data, res)
	struct fde *e;
	void *data;
{
struct http_client	*client = data;

	//wnet_register(client->cl_fde->fde_fd, FDE_READ, proxy_backend_read, client);
	proxy_backend_read(client->cl_fde);
}

static void
proxy_backend_write_request(e, data, res)
	struct fde *e;
	void *data;
{
struct	http_client	*client = data;

	wnet_write(client->cl_fde->fde_fd, client->cl_hdrbuf, strlen(client->cl_hdrbuf),
			proxy_backend_write_header, client);
}

static void
proxy_backend_write(e, data, res)
	struct fde *e;
	void *data;
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

	wnet_register(client->cl_backendfde->fde_fd, FDE_READ, proxy_backend_read, client);
}
