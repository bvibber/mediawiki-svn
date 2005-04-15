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

struct http_client {
	char		*cl_readbuf;			/* all data we've read from this client	*/
	char		*cl_bufp;			/* where we are in the buffer		*/
	char		*cl_bufend;			/* end of the buffer			*/
struct	http_header	 cl_headers[MAX_HEADERS];	/* headers				*/
	int		 cl_num;			/* # of headers				*/
struct	fde		*cl_fde;			/* backref to fd			*/
	int		 cl_reqtype;			/* request type or 0			*/
	int		 cl_ps;				/* parse state				*/
	char		*cl_path;			/* path they want			*/
};
#define CL_BUFSIZE(c) ((c)->cl_bufend - (c)->cl_readbuf)
#define CL_BUFLEFT(c) ((c)->cl_bufend - (c)->cl_bufp)
#define CL_CURPOS(c) ((c)->cl_bufp - (c)->cl_readbuf)
#define CL_HEADER(c, i) ((c)->cl_readbuf + (c)->cl_headers[i].hdr_off)
#define CL_HEADERVAL(c, u) ((c)->cl_readbuf + (c)->cl_headers[i].hdr_valoff)

static int http_read(struct fde *);
static int parse_headers(struct http_client *);
static int parse_reqtype(struct http_client *, char *);
static void client_close(struct http_client *);
static void proxy_request(struct http_client *);
static void proxy_start_backend(struct backend *, struct fde *, void *);
static int proxy_backend_read(struct fde *);

static struct http_client *
new_client(e)
	struct fde *e;
{
struct	http_client	*cl;

	if ((cl = wmalloc(sizeof(struct http_client))) == NULL) {
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
	if (client->cl_readbuf)
		free(client->cl_readbuf);

	wnet_close(client->cl_fde->fde_fd);
	wfree(client);
}

static int
http_read(e)
	struct fde *e;
{
struct	http_client	*c = e->fde_data;
	int		 i;
	
	/*
	 * Do we have any buffer room left?
	 */
	if (CL_BUFLEFT(c) == 0) {
		size_t curpos = CL_CURPOS(c);
		size_t cursize = CL_BUFSIZE(c);
		if ((c->cl_readbuf = realloc(c->cl_readbuf, CL_BUFSIZE(c) + RDBUF_INC)) == NULL) {
			fputs("out of memory\n", stderr);
			abort();
		}
		c->cl_bufp = c->cl_readbuf + curpos;
		c->cl_bufend = c->cl_readbuf + cursize + RDBUF_INC - 1;
	}

	while ((i = read(e->fde_fd, c->cl_bufp, CL_BUFLEFT(c))) > 0) {
		if (parse_headers(c) == -1) {
			/* parse error */
			wfree(e->fde_data);
			client_close(c);
			return 1;
		}
	}

	if (c->cl_ps == PS_DONE) {
		proxy_request(c);
		/*
		 * Don't care about this fd now.  If we're ever interested
		 * in it again, it'll be reregistered.
		 */
		return 1;
	}

	/*
	 * Either an error, socket closed or wouldblock.
	 */
	if (i == 0 || (i < 0 && errno != EWOULDBLOCK)) {
		/*
		 * Okay, client exited rudely.
		 */
		client_close(c);
		return 1;
	} 
	return 0;
}

static int
parse_headers(client)
	struct http_client *client;
{
	while (CL_BUFLEFT(client) > 0) {
		char c = *client->cl_bufp;

		switch(client->cl_ps) {
		case PS_START:
			/* should be reading a request type */
			switch(c) {
				case '\r':
					*client->cl_bufp = '\0';
					if (parse_reqtype(client, client->cl_readbuf) == -1)
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
					client->cl_headers[client->cl_num].hdr_off = client->cl_bufp - client->cl_readbuf;
					break;
			}
			break;
		case PS_HDR:
			switch(c) {
				case ':':
					client->cl_ps = PS_COLON;
					*client->cl_bufp = '\0';
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
					client->cl_headers[client->cl_num].hdr_valoff = client->cl_bufp - client->cl_readbuf;
					client->cl_ps = PS_VALUE;
					break;
			}
			break;
		case PS_VALUE:
			switch(c) {
				case '\r': 
					*client->cl_bufp = '\0';
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
		client->cl_bufp++;
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
	int		 i;
	
	/* XXX */
	write(e->fde_fd, "GET ", 4);
	write(e->fde_fd, client->cl_path, strlen(client->cl_path));
	write(e->fde_fd, " HTTP/1.0\r\n", 11);

	for (i = 0; i < client->cl_num; ++i) {
		if (!strcmp(CL_HEADER(client, i), "Connection"))
			write(e->fde_fd, "Connection: close\r\n", 19);
		else {
			write(e->fde_fd, CL_HEADER(client, i), strlen(CL_HEADER(client, i)));
			write(e->fde_fd, ": ", 2);
			write(e->fde_fd, CL_HEADERVAL(client, i), strlen(CL_HEADERVAL(client, i)));
			write(e->fde_fd, "\r\n", 2);
		}
	}

	write(e->fde_fd, "\r\n", 2);
	/* end XXX */

	wnet_register(e->fde_fd, FDE_READ, proxy_backend_read, client);
}	

static int
proxy_backend_read(e)
	struct fde *e;
{
struct	http_client	*client = e->fde_data;
	char		 rdbuf[16384];
	int		 i;

	while ((i = read(e->fde_fd, rdbuf, sizeof(rdbuf))) > 0) {
		if (write(client->cl_fde->fde_fd, rdbuf, i) < 0) { /* XXX */
			wnet_close(e->fde_fd);
			wnet_close(client->cl_fde->fde_fd);
		}
	}

	if (i == 0 || (i == -1 && errno != EWOULDBLOCK)) {
		wnet_close(e->fde_fd);
		client_close(client);
		return 1;
	}

	return 0;
}
