/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp: HTTP implementation.
 */

/*
 * The logic of whttp is explained in whttp_entity.c
 */
 
#include <sys/param.h>

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <errno.h>
#include <netdb.h>
#include <assert.h>

#include "willow.h"
#include "whttp.h"
#include "wnet.h"
#include "wbackend.h"
#include "wconfig.h"
#include "wlogwriter.h"

#ifndef MAXHOSTNAMELEN
# define MAXHOSTNAMELEN HOST_NAME_MAX /* SysV / BSD disagreement */
#endif

/*
 * Error handling.
 */
#define ERR_GENERAL	0
#define ERR_BADREQUEST	1
#define ERR_BADRESPONSE	2

static const char *error_files[] = {
	/* ERR_GENERAL		*/	DATADIR "/errors/ERR_GENERAL",
	/* ERR_BADREQUEST	*/	DATADIR "/errors/ERR_BADREQUEST",
	/* ERR_BADRESPONSE	*/	DATADIR "/errors/ERR_BADRESPONSE",
};

#define MAX_HEADERS	64	/* maximum # of headers to allow	*/
#define RDBUF_INC	8192	/* buffer in 8 KiB incrs		*/

static const char *request_string[] = {
	"GET",
	"POST",
	"HEAD",
	"TRACE",
	"OPTIONS",
};

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
struct	http_entity	 cl_entity;			/* reply to send back			*/
};

static void http_read(struct fde *);
static void client_close(struct http_client *);
static void proxy_request(struct http_client *);
static void proxy_start_backend(struct backend *, struct fde *, void *);
static void proxy_backend_read(struct fde *);
static void proxy_write_done(struct fde *, void *, int);
static void proxy_write_done_request(struct fde *, void *, int);
static int readbuf_getdata(int fd, struct readbuf *);
static void readbuf_free(struct readbuf *);
static void readbuf_reset(struct readbuf *);

static void client_send_error(struct http_client *, int, const char *);
static void client_log_request(struct http_client *);

static char via_hdr[1024];
static char my_hostname[MAXHOSTNAMELEN + 1];
static char my_version[1024];
static int logwr_pipe[2];
static FILE *alf;

void
whttp_init(void)
{
	if (gethostname(my_hostname, MAXHOSTNAMELEN) < 0) {
		perror("gethostname");
		exit(8);
	}

	strcpy(my_version, "Willow/" VERSION);
	sprintf(via_hdr, "1.0 %s (%s)", my_hostname, my_version);

	/*
	 * Fork the logwriter.
	 */
	if (pipe(logwr_pipe) < 0) {
		perror("pipe");
		exit(8);
	}
	wlogwriter_start(logwr_pipe[1]);
	if ((alf = fdopen(logwr_pipe[0], "w")) == NULL) {
		perror("fdopen");
		exit(8);
	}
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

void
http_new(e)
	struct fde *e;
{
struct	http_client	*cl;

	cl = new_client(e);
	entity_read_headers(&cl->cl_entity, &cl->cl_readbuf, client_read_done, cl);
}

static void
client_close(client)
	struct http_client *client;
{
	readbuf_free(&client->cl_readbuf);
	header_free(&client->cl_headers);
	if (client->cl_wrtbuf)
		wfree(client->cl_wrtbuf);
	if (client->cl_path)
		wfree(client->cl_path);
	wnet_close(client->cl_fde->fde_fd);
	wfree(client);
}

static int
readbuf_getdata(fd, buffer)
	int fd;
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
	size_t		 bufsz;
struct	header_list	 response_headers, *it;
	
	client->cl_backend = backend;
	client->cl_backendfde = e;

	memset(&response_headers, 0, sizeof(response_headers));

	bufsz = strlen(client->cl_path) + 12 + strlen(request_string[client->cl_reqtype]);
	client->cl_wrtbuf = wmalloc(bufsz + 1);
	sprintf(client->cl_wrtbuf, "%s %s HTTP/1.0\r\n", request_string[client->cl_reqtype], client->cl_path);

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
	int i;
{
struct	http_client	*client = data;

	wfree(client->cl_wrtbuf);
	client->cl_wrtbuf = NULL;

	if (i == -1) {
		client_send_error(client, ERR_GENERAL, strerror(errno));
		wnet_close(e->fde_fd);
		return;
	}

	wnet_write(e->fde_fd, client->cl_hdrbuf, strlen(client->cl_hdrbuf), proxy_write_done, client);
}

static void
proxy_write_done(e, data, i)
	struct fde *e;
	void *data;
	int i;
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
struct	header_list	*head;

	if (readbuf_getdata(e->fde_fd, &client->cl_readbuf) < 1) {
		if (READBUF_DATA_LEFT(&client->cl_readbuf) == 0) {
			wnet_close(e->fde_fd);
			client_send_error(client, ERR_GENERAL, strerror(errno));
			return;
		}
	}

	if (parse_headers(client, 1) == -1) {
		wnet_close(e->fde_fd);
		client_send_error(client, ERR_BADRESPONSE, NULL);
		return;
	}

	if (client->cl_ps < PS_DONE)
		return;

	wnet_register(client->cl_fde->fde_fd, FDE_READ, NULL, NULL);
	wnet_register(e->fde_fd, FDE_READ, NULL, NULL);

	for (head = client->cl_headers.hl_next; head; head = head->hl_next)
		header_add(&client->cl_response.hr_headers, head->hl_name, head->hl_value);
	header_add(&client->cl_response.hr_headers, "Via", via_hdr);

	client->cl_response.hr_status = 200;
	client->cl_response.hr_status_str = "OK";
	client->cl_response.hr_source_type = RESP_SOURCE_FDE;
	client->cl_response.hr_source.fde = e;
	client->cl_response.hr_flags.cachable = 1;

	client_send_response(client);
}

static void
client_send_error(client, errnum, errdata)
	struct http_client *client;
	int errnum;
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
		client->cl_path = strdup("NONE");

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
	header_add(&client->cl_response.hr_headers, "Expires", current_time_str);
	header_add(&client->cl_response.hr_headers, "Server", my_version);
	header_add(&client->cl_response.hr_headers, "Content-Type", "text/html");
	header_add(&client->cl_response.hr_headers, "Connection", "close");

	client->cl_response.hr_status = 503;
	client->cl_response.hr_status_str = "Service unavailable";
	client->cl_response.hr_source_type = RESP_SOURCE_BUFFER;
	client->cl_response.hr_source.buffer.addr = client->cl_wrtbuf;
	assert(u >= client->cl_wrtbuf);
	/*LINTED possible ptrdiff_t overflow*/
	client->cl_response.hr_source.buffer.len = u - client->cl_wrtbuf;

	client->cl_response.hr_flags.cachable = 0;

	client_send_response(client);
}

static void
client_log_request(client)
	struct http_client *client;
{
	if (!config.access_log)
		return;

	fprintf(alf, "[%s] %s %s \"%s\" %d %s\n",
			current_time_short, client->cl_fde->fde_straddr,
			request_string[client->cl_reqtype],
			client->cl_path, client->cl_response.hr_status,
			client->cl_backend->be_name);
	fflush(alf);
}
