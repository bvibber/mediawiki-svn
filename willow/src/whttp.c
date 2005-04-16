/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp: HTTP implementation.
 */

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <errno.h>

#include "willow.h"
#include "whttp.h"
#include "wnet.h"
#include "wbackend.h"

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

struct http_header {
	int	 hdr_off;	/* offset from start of buffer	*/
	int	 hdr_valoff;	/* offset of value		*/
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
struct	readbuf		 cl_readbuf;
struct	http_header	 cl_headers[MAX_HEADERS];	/* headers				*/
	int		 cl_num;			/* # of headers				*/
struct	fde		*cl_fde;			/* backref to fd			*/
	int		 cl_reqtype;			/* request type or 0			*/
	int		 cl_ps;				/* parse state				*/
	char		*cl_path;			/* path they want			*/
	char		*cl_wrtbuf;			/* write buf (either to client or be)	*/
struct	backend		*cl_backend;			/* backend servicing this client	*/
struct	fde		*cl_backendfde;			/* fde for backend			*/
};
#define CL_HEADER(c, i) ((c)->cl_readbuf.rb_p + (c)->cl_headers[i].hdr_off)
#define CL_HEADERVAL(c, u) ((c)->cl_readbuf.rb_p + (c)->cl_headers[i].hdr_valoff)

static int http_read(struct fde *);
static int parse_headers(struct http_client *);
static int parse_reqtype(struct http_client *, char *);
static void client_close(struct http_client *);
static void proxy_request(struct http_client *);
static void proxy_start_backend(struct backend *, struct fde *, void *);
static int proxy_backend_read(struct fde *);
static void proxy_backend_write(struct fde *, void *, int);
static void proxy_write_done(struct fde *, void *, int);
static int readbuf_getdata(int fd, struct readbuf *);
static void readbuf_free(struct readbuf *);
static void readbuf_reset(struct readbuf *);

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
	if (parse_headers(c) == -1) {
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
parse_headers(client)
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
					if (parse_reqtype(client, client->cl_readbuf.rb_p) == -1)
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
					if (client->cl_num + 1 > MAX_HEADERS)
						return -1;
					client->cl_headers[client->cl_num].hdr_off = client->cl_readbuf.rb_dpos;
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
					client->cl_headers[client->cl_num].hdr_valoff = client->cl_readbuf.rb_dpos;
					client->cl_ps = PS_VALUE;
					break;
			}
			break;
		case PS_VALUE:
			switch(c) {
				case '\r': 
					*READBUF_CUR_POS(&client->cl_readbuf) = '\0';
					client->cl_ps = PS_CR;
					client->cl_num++;
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

static int parse_reqtype(client, request)
	struct http_client *client;
	char *request;
{
	char	*p;

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
	int		 i, got_xff = 0;
	size_t		 bufsz;
	char		*wrtbuf;
	
	client->cl_backend = backend;
	client->cl_backendfde = e;

	bufsz = 4 + 11 + strlen(client->cl_path) + 3 + 16 + 4 + 15 + 5;
	for (i = 0; i < client->cl_num; ++i) {
		if (!strcmp(CL_HEADER(client, i), "Connection"))
			bufsz += 19;
		else
			bufsz += strlen(CL_HEADER(client, i)) + strlen(CL_HEADERVAL(client, i)) + 4;
	}

	if ((wrtbuf = wmalloc(bufsz)) == NULL) {
		fputs("out of memory\n", stderr);
		abort();
	}

	strcpy(wrtbuf, "GET ");
	strcat(wrtbuf, client->cl_path);
	strcat(wrtbuf, " HTTP/1.0\r\n");

	for (i = 0; i < client->cl_num; ++i) {
		if (!strcmp(CL_HEADER(client, i), "Connection"))
			strcat(wrtbuf, "Connection: close\r\n");
		else if (!strcmp(CL_HEADER(client, i), "X-Forwarded-For")) {
			got_xff = 1;
			strcat(wrtbuf, CL_HEADER(client, i));
			strcat(wrtbuf, ": ");
			strcat(wrtbuf, CL_HEADERVAL(client, i));
			strcat(wrtbuf, ", ");
			strcat(wrtbuf, client->cl_fde->fde_straddr);
			strcat(wrtbuf, "\r\n");
		} else {
			strcat(wrtbuf, CL_HEADER(client, i));
			strcat(wrtbuf, ": ");
			strcat(wrtbuf, CL_HEADERVAL(client, i));
			strcat(wrtbuf, "\r\n");
		}
	}
	if (!got_xff) {
		strcat(wrtbuf, "X-Forwarded-For: ");
		strcat(wrtbuf, client->cl_fde->fde_straddr);
		strcat(wrtbuf, "\r\n");
	}

	strcat(wrtbuf, "\r\n");
	client->cl_wrtbuf = wrtbuf;
	wnet_write(e->fde_fd, wrtbuf, bufsz - 1, proxy_write_done, client);
}

static void
proxy_write_done(e, data, i)
	struct fde *e;
	void *data;
{
struct	http_client	*client = data;

	wfree(client->cl_wrtbuf);

	if (i == -1) {
		client_close(client);
		wnet_close(e->fde_fd);
		return;
	}
		
	/*
	 * Re-appropriate the readbuf for the backend...
	 */
	readbuf_reset(&client->cl_readbuf);

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

	wnet_write(client->cl_fde->fde_fd, client->cl_readbuf.rb_p, client->cl_readbuf.rb_dsize, proxy_backend_write, client);
	readbuf_reset(&client->cl_readbuf);
	return 1;
}

static void
proxy_backend_write(e, data, res)
	struct fde *e;
	void *data;
	int res;
{
struct	http_client	*client = data;

	/*
	 * Write to client completed.  Wait for the backend to send more data.
	 */
	if (res == -1) {
		client_close(client);
		return;
	}

	wnet_register(client->cl_backendfde->fde_fd, FDE_READ, proxy_backend_read, client);
}
