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

/*
 * Error handling.
 */
#define ERR_GENERAL	0
#define ERR_BADREQUEST	1
#define ERR_BADRESPONSE	2

const char *error_files[] = {
	/* ERR_GENERAL		*/	DATADIR "/errors/ERR_GENERAL",
	/* ERR_BADREQUEST	*/	DATADIR "/errors/ERR_BADREQUEST",
	/* ERR_BADRESPONSE	*/	DATADIR "/errors/ERR_BADRESPONSE",
};

#define MAX_HEADERS	64	/* maximum # of headers to allow	*/
#define RDBUF_INC	8192	/* buffer in 8 KiB incrs		*/

#define REQTYPE_GET	1
#define REQTYPE_POST	2
#define REQTYPE_HEAD	3
#define REQTYPE_TRACE	4

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
struct	header_list	*hl_tail;
	int		 hl_len;
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

#define RESP_SOURCE_BUFFER	1
#define RESP_SOURCE_FDE		2

struct http_response {
	int		 hr_status;
	const char	*hr_status_str;	
struct	header_list	 hr_headers;
	int		 hr_source_type;
	union {
		const char	*buffer;
		struct fde	*fde;
	}		 hr_source;
};

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
struct	http_response	 cl_response;			/* reply to send back			*/
};

static void http_read(struct fde *);
static int parse_headers(struct http_client *, int);
static int parse_reqtype(struct http_client *, char *, int);
static void client_close(struct http_client *);
static void proxy_request(struct http_client *);
static void proxy_start_backend(struct backend *, struct fde *, void *);
static void proxy_backend_read(struct fde *);
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

static void client_send_error(struct http_client *, int, const char *);
static void client_send_response(struct http_client *);
static void client_send_response_headers_done(struct fde *, void *, int);
static void client_send_response_body_done(struct fde *, void *, int);

static char via_hdr[1024];
static char my_hostname[MAXHOSTNAMELEN + 1];
static char my_version[1024];

void
whttp_init(void)
{
	if (gethostname(my_hostname, MAXHOSTNAMELEN) < 0) {
		perror("gethostname");
		exit(8);
	}

	strcpy(my_version, "Willow/" VERSION);
	sprintf(via_hdr, "1.0 %s (%s)", my_hostname, my_version);
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

static char *
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
	header_free(&client->cl_headers);
	if (client->cl_wrtbuf)
		wfree(client->cl_wrtbuf);
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
		free(buffer->rb_p);
	memset(buffer, 0, sizeof(*buffer));
}

static void
readbuf_reset(buffer)
	struct readbuf *buffer;
{
	buffer->rb_dpos = buffer->rb_dsize = 0;
}

static void
http_read(e)
	struct fde *e;
{
struct	http_client	*c = e->fde_rdata;
	int		 i;
	
	if ((i = readbuf_getdata(e->fde_fd, &c->cl_readbuf)) < 1) {
		if (errno == EWOULDBLOCK && (READBUF_DATA_LEFT(&c->cl_readbuf) == 0))
			return;
		else if (i == 0 || (i == -1 && errno != EWOULDBLOCK)) {
			client_close(c);
			return;
		}
	}
	if (parse_headers(c, 0) == -1) {
		/* parse error */
		client_send_error(c, ERR_BADREQUEST, NULL);
		return;
	}
	if (c->cl_ps == PS_DONE) {
		/*
		 * Don't care about this fd now.  If we're ever interested
		 * in it again, it'll be reregistered.
		 */
		wnet_register(c->cl_fde->fde_fd, FDE_READ, NULL, NULL);

		proxy_request(c);
	}
	return;
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
	else if (!strcmp(request, "HEAD"))
		client->cl_reqtype = REQTYPE_HEAD;
	else if (!strcmp(request, "TRACE"))
		client->cl_reqtype = REQTYPE_TRACE;
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
		client_send_error(client, ERR_GENERAL, strerror(errno));
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
		client_send_error(client, ERR_GENERAL, strerror(errno));
		wnet_close(e->fde_fd);
		return;
	}
		
	/*
	 * Re-appropriate the readbuf for the backend...
	 */
	readbuf_free(&client->cl_readbuf);
	client->cl_ps = PS_START;
	header_free(&client->cl_headers);

	wnet_register(e->fde_fd, FDE_READ, proxy_backend_read, client);
}	

static void
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
			client_send_error(client, ERR_GENERAL, strerror(errno));
			return;
		}
	}

	if (client->cl_ps < PS_DONE) {
		if (parse_headers(client, 1) == -1) {
			wnet_close(e->fde_fd);
			client_send_error(client, ERR_BADRESPONSE, NULL);
			return;
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
			header_free(&response_headers);

			wnet_write(client->cl_fde->fde_fd, "HTTP/1.0 200 OK\r\n", 17, proxy_backend_write_request, client);
			wnet_register(client->cl_fde->fde_fd, FDE_READ, NULL, NULL);
		}
		return;
	}

	wnet_register(client->cl_fde->fde_fd, FDE_READ, NULL, NULL);
	wnet_write(client->cl_fde->fde_fd, READBUF_CUR_POS(&client->cl_readbuf), 
			READBUF_DATA_LEFT(&client->cl_readbuf), proxy_backend_write, client);
}

static void
proxy_backend_write_header(e, data, res)
	struct fde *e;
	void *data;
{
struct http_client	*client = data;

	free(client->cl_hdrbuf);
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

	wnet_register(e->fde_fd, FDE_READ, proxy_backend_read, client);
}

static void
client_send_error(client, errnum, errdata)
	struct http_client *client;
	const char *errdata;
{
	FILE		*errfile;
	char		 errbuf[8192];
	ssize_t		 size;
	char		*p = errbuf, *u;

	if ((errfile = fopen(error_files[errnum], "r")) == NULL) {
		client_close(client);
		return;
	}

	if ((size = fread(errbuf, 1, sizeof(errbuf) - 1, errfile)) < 0) {
		fclose(errfile);
		client_close(client);
		return;
	}

	fclose(errfile);
	errbuf[size] = '\0';

	if (!errdata)
		errdata = "Unknown error";
	if (!client->cl_path)
		client->cl_path = "NONE";

	size = strlen(errbuf) + strlen(client->cl_path) + strlen(errdata) + strlen(current_time_str) + strlen(my_version)
		+ strlen(my_hostname) + 1;
	u = client->cl_wrtbuf = wmalloc(size);
	memset(u, 0, size);

	while (*p) {
		switch(*p) {
		case '%':
			switch (*++p) {
			case 'U':
				strcat(u, client->cl_path);
				u += strlen(u);
				break;
			case 'D':
				strcat(u, current_time_str);
				u += strlen(u);
				break;
			case 'H':
				strcat(u, my_hostname);
				u += strlen(u);
				break;
			case 'E':
				strcat(u, errdata);
				u += strlen(u);
				break;
			case 'V':
				strcat(u, my_version);
				u += strlen(u);
				break;
			default:
				break;
			}
			p++;
			continue;
		default:
			*u++ = *p;
			break;
		}
		++p;
	}
	*u = '\0';

	memset(&client->cl_response.hr_headers, 0, sizeof(client->cl_response.hr_headers));
	header_add(&client->cl_response.hr_headers, "Date", current_time_str);
	header_add(&client->cl_response.hr_headers, "Server", my_version);
	header_add(&client->cl_response.hr_headers, "Content-Type", "text/html");
	header_add(&client->cl_response.hr_headers, "Connection", "close");

	client->cl_response.hr_status = 503;
	client->cl_response.hr_status_str = "Service unavailable";
	client->cl_response.hr_source_type = RESP_SOURCE_BUFFER;
	client->cl_response.hr_source.buffer = client->cl_wrtbuf;

	client_send_response(client);
}

static void
client_send_response(client)
	struct http_client *client;
{
	char	status[4];

	sprintf(status, "%d", client->cl_response.hr_status);
	write(client->cl_fde->fde_fd, "HTTP/1.0 ", 9);
	write(client->cl_fde->fde_fd, status, strlen(status));
	write(client->cl_fde->fde_fd, client->cl_response.hr_status_str,
			strlen(client->cl_response.hr_status_str));
	write(client->cl_fde->fde_fd, "\r\n", 2);

	client->cl_hdrbuf = header_build(&client->cl_response.hr_headers);
	wnet_write(client->cl_fde->fde_fd, client->cl_hdrbuf, strlen(client->cl_hdrbuf),
			client_send_response_headers_done, client);
}

static void
client_send_response_headers_done(e, data, res)
	struct fde *e;
	void *data;
{
struct	http_client	*client = data;

	wfree(client->cl_hdrbuf);

	if (res == -1) {
		client_close(client);
		return;
	}

	wnet_write(client->cl_fde->fde_fd, client->cl_response.hr_source.buffer, 
			strlen(client->cl_response.hr_source.buffer), client_send_response_body_done, client);
}

static void
client_send_response_body_done(e, data, res)
	struct fde *e;
	void *data;
{
struct	http_client	*client = data;

	client_close(client);
}
