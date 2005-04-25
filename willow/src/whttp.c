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
#include "whttp_entity.h"
#include "wlog.h"

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

const char *request_string[] = {
	"GET",
	"POST",
	"HEAD",
	"TRACE",
	"OPTIONS",
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
struct	http_entity	 cl_entity;			/* reply to send back			*/
};

static void http_read(struct fde *);static void client_close(struct http_client *);
static void proxy_start_backend(struct backend *, struct fde *, void *);
static void client_read_done(struct http_entity *, void *, int);
static void client_response_done(struct http_entity *, void *, int);
static void backend_headers_done(struct http_entity *, void *, int);
static void client_headers_done(struct http_entity *, void *, int);

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
	cl->cl_entity.he_source_type = ENT_SOURCE_FDE;
	cl->cl_entity.he_source.fde = e;
	
	DEBUG((WLOG_DEBUG, "http_new: starting header read for %d", cl->cl_fde->fde_fd));
	entity_read_headers(&cl->cl_entity, client_read_done, cl);
}

static void
client_read_done(entity, data, res)
	struct http_entity *entity;
	void *data;
	int res;
{
struct	http_client	*client = data;

	DEBUG((WLOG_DEBUG, "client_read_done: called"));
	/*
	 * Got the headers from the client.  Find a backend.
	 */

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
struct	header_list	 *it;
	
	DEBUG((WLOG_DEBUG, "proxy_start_backend: called"));
	
	client->cl_backend = backend;
	client->cl_backendfde = e;

	for (it = client->cl_entity.he_headers.hl_next; it; it = it->hl_next) {
		if (!strcmp(it->hl_name, "Connection")) {
			header_remove(&client->cl_entity.he_headers, it);
			continue;
		}
	}

	header_add(&client->cl_entity.he_headers, "X-Forwarded-For", client->cl_fde->fde_straddr);
	client->cl_entity.he_source_type = ENT_SOURCE_NONE;
	entity_send(e, &client->cl_entity, backend_headers_done, client);
}

static void
backend_headers_done(entity, data, res)
	struct http_entity *entity;
	void *data;
	int res;
{
struct	http_client	*client = data;
	
	DEBUG((WLOG_DEBUG, "backend_headers_done: called"));
	if (res == -1) {
		client_send_error(client, ERR_GENERAL, strerror(errno));
		return;
	}
	
	memset(&client->cl_entity, 0, sizeof(client->cl_entity));
	client->cl_entity.he_source_type = ENT_SOURCE_FDE;
	client->cl_entity.he_source.fde = client->cl_backendfde;
	client->cl_entity.he_flags.response = 1;

	/*
	 * This should probably be handled somewhere inside
	 * whttp_entity.c ...
	 */
	entity_read_headers(&client->cl_entity, client_headers_done, client);
}

static void
client_headers_done(entity, data, res)
	struct http_entity *entity;
	void *data;
	int res;
{
struct	http_client	*client = data;

	DEBUG((WLOG_DEBUG, "client_headers_done: called"));
	
	if (res == -1) {
		client_close(client);
		return;
	}
	
	entity_send(client->cl_fde, &client->cl_entity, client_response_done, client);
}

static void
client_response_done(entity, data, res)
	struct http_entity *entity;
	void *data;
	int res;
{
struct	http_client	*client = data;

	DEBUG((WLOG_DEBUG, "client_response_done: called"));
	client_close(client);
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
	if (client->cl_backendfde)
		wnet_close(client->cl_backendfde->fde_fd);
	wfree(client);
}

int
readbuf_getdata(fd, buffer)
	int fd;
	struct readbuf *buffer;
{
	int	i;

	DEBUG((WLOG_DEBUG, "readbuf_getdata: called"));
//	for (;;) {
		if (READBUF_SPARE_SIZE(buffer) == 0) {
			DEBUG((WLOG_DEBUG, "readbuf_getdata: no space in buffer"));
			buffer->rb_size += RDBUF_INC;
			buffer->rb_p = realloc(buffer->rb_p, buffer->rb_size);
		}

		if ((i = read(fd, READBUF_SPARE_START(buffer), READBUF_SPARE_SIZE(buffer))) < 1)
			return i;
		buffer->rb_dsize += i;
		DEBUG((WLOG_DEBUG, "readbuf_getdata: read %d bytes", i));
//	}
	return i;
}

void
readbuf_free(buffer)
	struct readbuf *buffer;
{
	if (buffer->rb_p)
		free(buffer->rb_p);
	memset(buffer, 0, sizeof(*buffer));
}

void
readbuf_reset(buffer)
	struct readbuf *buffer;
{
	buffer->rb_dpos = buffer->rb_dsize = 0;
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

	memset(&client->cl_entity.he_headers, 0, sizeof(client->cl_entity.he_headers));
	header_add(&client->cl_entity.he_headers, "Date", current_time_str);
	header_add(&client->cl_entity.he_headers, "Expires", current_time_str);
	header_add(&client->cl_entity.he_headers, "Server", my_version);
	header_add(&client->cl_entity.he_headers, "Content-Type", "text/html");
	header_add(&client->cl_entity.he_headers, "Connection", "close");

	client->cl_entity.he_rdata.response.status = 503;
	client->cl_entity.he_rdata.response.status_str = "Service unavailable";
	client->cl_entity.he_source_type = ENT_SOURCE_BUFFER;
	client->cl_entity.he_source.buffer.addr = client->cl_wrtbuf;
	assert(u >= client->cl_wrtbuf);
	/*LINTED possible ptrdiff_t overflow*/
	client->cl_entity.he_source.buffer.len = u - client->cl_wrtbuf;

	client->cl_entity.he_flags.cachable = 0;
	entity_send(client->cl_fde, &client->cl_entity, client_response_done, client);
}

static void
client_log_request(client)
	struct http_client *client;
{
	if (!config.access_log)
		return;
#if 0
	fprintf(alf, "[%s] %s %s \"%s\" %d %s\n",
			current_time_short, client->cl_fde->fde_straddr,
			request_string[client->cl_reqtype],
			client->cl_path, client->cl_response.hr_status,
			client->cl_backend->be_name);
	fflush(alf);
#endif
}
