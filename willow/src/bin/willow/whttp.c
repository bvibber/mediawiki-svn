/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp: HTTP implementation.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

/*
 * The logic of whttp is explained in whttp_entity.c
 */
 
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/param.h>

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <errno.h>
#include <netdb.h>
#include <fcntl.h>
#include <strings.h>
#include <assert.h>
#include <time.h>

#include "willow.h"
#include "whttp.h"
#include "wnet.h"
#include "wbackend.h"
#include "wconfig.h"
#include "wlogwriter.h"
#include "whttp_entity.h"
#include "wlog.h"
#include "wcache.h"

#ifndef MAXHOSTNAMELEN
# define MAXHOSTNAMELEN HOST_NAME_MAX /* SysV / BSD disagreement */
#endif

/*
 * Error handling.
 */
#define ERR_GENERAL	0
#define ERR_BADREQUEST	1
#define ERR_BADRESPONSE	2
#define ERR_CACHE_IO	3

static const char *error_files[] = {
	/* ERR_GENERAL		*/	DATADIR "/errors/ERR_GENERAL",
	/* ERR_BADREQUEST	*/	DATADIR "/errors/ERR_BADREQUEST",
	/* ERR_BADRESPONSE	*/	DATADIR "/errors/ERR_BADRESPONSE",
	/* ERR_CACHE_IO		*/	DATADIR "/errors/ERR_CACHE_IO",
};

const char *request_string[] = {
	"GET",
	"POST",
	"HEAD",
	"TRACE",
	"OPTIONS",
};

struct request_type supported_reqtypes[] = {
	{ "GET",	3,	REQTYPE_GET	},
	{ "POST",	4,	REQTYPE_POST	},
	{ "HEAD",	4,	REQTYPE_HEAD	},
	{ "TRACE",	5,	REQTYPE_TRACE	},
	{ "OPTIONS",	7,	REQTYPE_OPTIONS	},
	{ NULL,		0,	REQTYPE_INVALID }
};

struct http_client {
struct	fde		*cl_fde;	/* backref to fd			*/
	int		 cl_reqtype;	/* request type or 0			*/
	char		*cl_path;	/* path they want			*/
	char		*cl_wrtbuf;	/* write buf (either to client or be)	*/
struct	backend		*cl_backend;	/* backend servicing this client	*/
struct	fde		*cl_backendfde;	/* fde for backend			*/
struct	http_entity	 cl_entity;	/* reply to send back			*/

	/* Cache-related data */
	int		 cl_cfd;	/* FD of cache file for writing, or 0	*/
struct	cache_object	*cl_co;		/* Cache object				*/
	struct {
		int	f_cached:1;
		int	f_closed:1;
		int	f_http11:1;	/* Client understands HTTP/1.1		*/
	}		 cl_flags;
	size_t		 cl_dsize;	/* Object size				*/
enum	encoding	 cl_enc;
struct	http_client	*fe_next;	/* freelist 				*/
};

static struct http_client freelist;
static struct http_client deadlist;

static void client_close(struct http_client *);
static void proxy_start_backend(struct backend *, struct fde *, void *);
static void client_read_done(struct http_entity *, void *, int);
static void client_response_done(struct http_entity *, void *, int);
static void backend_headers_done(struct http_entity *, void *, int);
static void client_headers_done(struct http_entity *, void *, int);
static void client_write_cached(struct http_client *);
static int removable_header(const char *);

static void client_send_error(struct http_client *, int errcode, const char *error,
				int status, const char *statusstr);
static void client_log_request(struct http_client *);

static void do_cache_write(const char *, size_t, void *);

static char via_hdr[1024];
static char *cache_hit_hdr;
static char *cache_miss_hdr;

char my_hostname[MAXHOSTNAMELEN + 1];
static char my_version[64];
static int logwr_pipe[2];
static FILE *alf;

/*
 * Initialize whttp, start loggers.
 */
void
whttp_init(void)
{
	int	hsize;
	
	if (gethostname(my_hostname, MAXHOSTNAMELEN) < 0) {
		perror("gethostname");
		exit(8);
	}

	(void)strlcpy(my_version, "Willow/" PACKAGE_VERSION, 64);
	safe_snprintf(1023, (via_hdr, 1023, "1.1 %s (%s)", my_hostname, my_version));

	hsize = sizeof("MISS from ") + strlen(my_hostname);
	cache_hit_hdr = wmalloc(hsize + 1);
	cache_miss_hdr = wmalloc(hsize + 1);
	
	if (cache_hit_hdr == NULL || cache_miss_hdr == NULL)
		outofmemory();
	
	safe_snprintf(hsize, (cache_hit_hdr, hsize, "HIT from %s", my_hostname));
	safe_snprintf(hsize, (cache_miss_hdr, hsize, "MISS from %s", my_hostname));
	
	/*
	 * Fork the logwriter.
	 */

	if (config.access_log) {
		if (pipe(logwr_pipe) < 0) {
			perror("pipe");
			exit(8);
		}
		switch (fork()) {
		case -1:
			wlog(WLOG_ERROR, "starting logwriter: %s", strerror(errno));
			exit(8);
			/*NOTREACHED*/
		case 0:
			(void)dup2(logwr_pipe[0], STDIN_FILENO);
			/*LINTED execl*/
			(void)execl(LIBEXECDIR "/wlogwriter", "wlogwriter", config.access_log, NULL);
			exit(8);
			/*NOTREACHED*/
		default:
			break;
		}
		if ((alf = fdopen(logwr_pipe[1], "a")) == NULL) {
			perror("whttp: fdopen");
			exit(8);
		}
	}
}

void
whttp_shutdown(void)
{
	wfree(cache_hit_hdr);
	wfree(cache_miss_hdr);
}

/*
 * Create a new client associated with the FDE 'e'.
 */
static struct http_client *
new_client(e)
	struct fde *e;
{
struct	http_client	*cl;

	if (freelist.fe_next) {
		cl = freelist.fe_next;
		freelist.fe_next = cl->fe_next;
		bzero(cl, sizeof(*cl));
	} else {
		if ((cl = wcalloc(1, sizeof(*cl))) == NULL)
			outofmemory();
	}

	cl->cl_fde = e;
	return cl;
}

/*
 * Called by wnet_accept to regiister a new client.  Reads the request headers
 * from the client.
 */
void
http_new(e)
	struct fde *e;
{
struct	http_client	*cl;

	cl = new_client(e);
	cl->cl_entity.he_source_type = ENT_SOURCE_FDE;
	cl->cl_entity.he_source.fde.fde = e;
	cl->cl_entity.he_rdata.request.contlen = -1;
		
	WDEBUG((WLOG_DEBUG, "http_new: starting header read for %d", cl->cl_fde->fde_fd));
	entity_read_headers(&cl->cl_entity, client_read_done, cl);
}

/*
 * Called when the initial request has been read.  Checks if the object is
 * cached, and starts a backend request if not.  If it it, sends the cached
 * object to the client.
 */
/*ARGSUSED*/
static void
client_read_done(entity, data, res)
	struct http_entity *entity;
	void *data;
	int res;
{
struct	http_client	*client = data;
struct	cache_object	*cobj;
struct	header_list	*pragma, *cache_control, *ifmod;
struct	qvalue_head	*acceptenc;
struct	qvalue		*val;
	int		 cacheable = 1;

	WDEBUG((WLOG_DEBUG, "client_read_done: called"));

	if (res < -1) {
		client_send_error(client, ERR_BADREQUEST, ent_errors[-res], 400, "Bad request (#10.4.1)");
		return;
	} else if (res == -1) {
		client_close(client);
		return;
	}
	
	if (client->cl_entity.he_rdata.request.httpmaj >= 1 &&
	    client->cl_entity.he_rdata.request.httpmin >= 1)
		client->cl_flags.f_http11 = 1;

	if (client->cl_entity.he_rdata.request.host == NULL)
		client->cl_path = wstrdup(client->cl_entity.he_rdata.request.path);
	else {
		int	len;
		
		len = strlen(client->cl_entity.he_rdata.request.host) +
			strlen(client->cl_entity.he_rdata.request.path)	+ 7;
		
		client->cl_path = wmalloc(len + 1);
		if (client->cl_path == NULL)
			outofmemory();
		safe_snprintf(len + 1, (client->cl_path, len + 1, "http://%s%s",
				client->cl_entity.he_rdata.request.host,
				client->cl_entity.he_rdata.request.path));
	}
	
	client->cl_reqtype = client->cl_entity.he_rdata.request.reqtype;

	pragma = header_find(&client->cl_entity.he_headers, "Pragma");
	cache_control = header_find(&client->cl_entity.he_headers, "Cache-control");

	if (pragma) {
		char **pragmas = wstrvec(pragma->hl_value, ",", 0);
		char **s;
		for (s = pragmas; *s; ++s) {
			if (!strcasecmp(*s, "no-cache")) {
				cacheable = 0;
				break;
			}
		}
		wstrvecfree(pragmas);
	}

	if (cache_control) {
		char **cache_controls = wstrvec(cache_control->hl_value, ",", 0);
		char **s;
		for (s = cache_controls; *s; ++s) {
			if (!strcasecmp(*s, "no-cache")) {
				cacheable = 0;
				break;
			}
		}
		wstrvecfree(cache_controls);
	}

	acceptenc = &entity->he_rdata.request.accept_encoding;
	while (val = qvalue_remove_best(acceptenc)) {
		WDEBUG((WLOG_DEBUG, "client offers [%s] q=%f", val->name, (double) val->val));
		if (client->cl_enc = accept_encoding(val->name)) {
			wfree(val);
			break;
		}
		wfree(val);
	}

	/*
	 * Check for cached object.
	 */
	if (client->cl_reqtype == REQTYPE_GET) {
		if (cacheable)
			client->cl_co = wcache_find_object(client->cl_path, &client->cl_cfd,
				WCACHE_RDWR);
		else
			client->cl_co = wcache_find_object(client->cl_path, &client->cl_cfd,
				WCACHE_WRONLY);

		if (cacheable && client->cl_co && client->cl_co->co_time && 
		    (ifmod = header_find(&client->cl_entity.he_headers, "If-Modified-Since"))) {
			char *s;
			time_t t;
			struct tm m;
			s = strptime(ifmod->hl_value, "%a, %d %b %Y %H:%M:%S", &m);
			if (s) {
				t = mktime(&m);
				WDEBUG((WLOG_DEBUG, "if-mod: %d, last-mod: %d", t, client->cl_co->co_time));
				if (t >= client->cl_co->co_time) {
					/*
					 * Not modified
					 */
					client_send_error(client, -1, NULL, 304, "Not modified (#10.3.5)");
					return;
				}
			}
		}

		if (client->cl_co && client->cl_co->co_complete) {
			WDEBUG((WLOG_DEBUG, "client_read_done: object %s cached", client->cl_path));
			client_write_cached(client);
			return;
		}
		WDEBUG((WLOG_DEBUG, "client_read_done: %s not cached", client->cl_path));
	}
	
	/*
	 * Not cached.  Find a backend.
	 */
	if (get_backend(proxy_start_backend, client, 0) == -1) {
		client_send_error(client, ERR_GENERAL, strerror(errno), 503, 
			"Service unavailable (#10.5.4)");
		return;
	}
}

/*
 * Called when backend is ready.  backend==NULL if none was found.
 */
static void
proxy_start_backend(backend, e, data)
	struct backend *backend;
	struct fde *e;
	void *data;
{
struct	http_client	*client = data;
struct	header_list	*it;
	int		 error = 0, len = sizeof(error);
	
	WDEBUG((WLOG_DEBUG, "proxy_start_backend: called; for client=%d backend=%d", client->cl_fde->fde_fd, e->fde_fd));
	
	if (backend == NULL) {
		client_send_error(client, ERR_GENERAL, strerror(errno), 503, 
			"Service unavailable (#10.5.4)");
		return;
	}
	
	client->cl_backend = backend;
	client->cl_backendfde = e;

	getsockopt(e->fde_fd, SOL_SOCKET, SO_ERROR, &error, &len);
	if (error) {
		client_send_error(client, ERR_GENERAL, strerror(error), 503, 
			"Service unavailable (#10.5.4)");
		return;
	}

	for (it = client->cl_entity.he_headers.hl_next; it; it = it->hl_next) {
		if (removable_header(it->hl_name)) {
			header_remove(&client->cl_entity.he_headers, it);
			it = client->cl_entity.he_headers.hl_next;
			continue;
		}
	}

	header_add(&client->cl_entity.he_headers, wstrdup("X-Forwarded-For"), wstrdup(client->cl_fde->fde_straddr));
	/*
	 * POST requests require Content-Length.
	 */
	if (client->cl_reqtype == REQTYPE_POST) {
		if (client->cl_entity.he_rdata.request.contlen == -1) {
			client_send_error(client, ERR_BADREQUEST, "POST request without Content-Length",
						411, "Length required (#10.4.12)");
			return;
		}
		
		WDEBUG((WLOG_DEBUG, "client content-length=%d", client->cl_entity.he_rdata.request.contlen));
		client->cl_entity.he_source_type = ENT_SOURCE_FDE;
		client->cl_entity.he_source.fde.fde = client->cl_fde;
		client->cl_entity.he_source.fde.len = client->cl_entity.he_rdata.request.contlen;
	} else
		client->cl_entity.he_source_type = ENT_SOURCE_NONE;
	
	entity_send(e, &client->cl_entity, backend_headers_done, client, 0);
}

/*
 * Called when clients request was written to the backend.
 */
/*ARGSUSED*/
static void
backend_headers_done(entity, data, res)
	struct http_entity *entity;
	void *data;
	int res;
{
struct	http_client	*client = data;
	
	WDEBUG((WLOG_DEBUG, "backend_headers_done: called"));
	if (res == -1) {
		client_send_error(client, ERR_GENERAL, strerror(errno), 503,
			"Service unavailable (#10.5.4)");
		return;
	}
	
	entity_free(&client->cl_entity);
	bzero(&client->cl_entity, sizeof(client->cl_entity));
	client->cl_entity.he_source_type = ENT_SOURCE_FDE;
	client->cl_entity.he_source.fde.fde = client->cl_backendfde;
	client->cl_entity.he_source.fde.len = -1;
	
	entity_set_response(&client->cl_entity, 1);

	/*
	 * This should probably be handled somewhere inside
	 * whttp_entity.c ...
	 */
	entity_read_headers(&client->cl_entity, client_headers_done, client);
}

/*
 * Called when backend's headers are finished reading.
 */
static void
client_headers_done(entity, data, res)
	struct http_entity *entity;
	void *data;
	int res;
{
struct	http_client	*client = data;
	char		*cache_path;
	size_t		 plen;
	
	WDEBUG((WLOG_DEBUG, "client_headers_done: called"));
	
	if (res == -1) {
		client_close(client);
		return;
	} else if (res < -1) {
		client_send_error(client, ERR_GENERAL, ent_errors[-res], 503,
			"Service unavailable (#10.5.4)");
		return;
	}
	
	/*
	 * If cachable, open the cache file and write data.
	 *
	 * Don't cache responses to non-GET requests, or non-200 replies.
	 */
	if (client->cl_reqtype != REQTYPE_GET || entity->he_rdata.response.status != 200
	    || !config.ncaches || !client->cl_co) {
		if (client->cl_co)
			wcache_release(client->cl_co, 0);
	} else if (client->cl_co) {
		struct header_list *lastmod;

		entity->he_cache_callback = do_cache_write;
		entity->he_cache_callback_data = client;
		header_dump(&client->cl_entity.he_headers, client->cl_cfd);

		/*
		 * Look for last-modified
		 */
		if (lastmod = header_find(&client->cl_entity.he_headers, "Last-Modified")) {
			struct tm tim;
			char *res;
			res = strptime(lastmod->hl_value, "%a, %d %b %Y %H:%M:%S", &tim);
			if (res) {
				WDEBUG((WLOG_DEBUG, "last-modified: %d", mktime(&tim)));
				client->cl_co->co_time = mktime(&tim);
			}
		}
	}
	
	header_add(&client->cl_entity.he_headers, wstrdup("Via"), wstrdup(via_hdr));
	header_add(&client->cl_entity.he_headers, wstrdup("X-Cache"), wstrdup(cache_miss_hdr));
	client->cl_entity.he_source.fde.len = -1;
	client->cl_entity.he_encoding = client->cl_enc;

	entity_send(client->cl_fde, &client->cl_entity, client_response_done, client,
			client->cl_flags.f_http11 ? ENT_CHUNKED_OKAY : 0);
}

/*
 * Write a cached object to the client.
 */
static void
client_write_cached(client)
	struct http_client *client;
{
	size_t	 plen;
	char	*cache_path;
struct	stat	 sb;
	char	 size[64];

	plen = strlen(config.caches[0].dir) + client->cl_co->co_plen + 12 + 2;
	if ((cache_path = wcalloc(1, plen + 1)) == NULL)
		outofmemory();

	if (fstat(client->cl_cfd, &sb) < 0) {
		wlog(WLOG_WARNING, "stat(%s): %s", cache_path, strerror(errno));
		client_send_error(client, ERR_CACHE_IO, strerror(errno),
			500, "Internal server error (#10.5.1)");
		wfree(cache_path);
		return;
	}
		
	wfree(cache_path);
	
	entity_free(&client->cl_entity);
	bzero(&client->cl_entity, sizeof(client->cl_entity));
	header_undump(&client->cl_entity.he_headers, client->cl_cfd, &client->cl_entity.he_source.fd.off);
	header_add(&client->cl_entity.he_headers, wstrdup("Via"), wstrdup(via_hdr));
	header_add(&client->cl_entity.he_headers, wstrdup("X-Cache"), wstrdup(cache_hit_hdr));
	if (!header_find(&client->cl_entity.he_headers, "Content-Length")) {
		safe_snprintf(sizeof size, (size, sizeof size, "%d", client->cl_co->co_size));
		header_add(&client->cl_entity.he_headers, wstrdup("Content-Length"), wstrdup(size));
	}

	entity_set_response(&client->cl_entity, 1);
	client->cl_entity.he_rdata.response.status = 200;
	client->cl_entity.he_rdata.response.status_str = "OK";
			
	client->cl_entity.he_source.fd.fd = client->cl_cfd;
	client->cl_entity.he_source.fd.size = sb.st_size;
	//client->cl_entity.he_encoding = client->cl_enc;

	client->cl_entity.he_source_type = ENT_SOURCE_FILE;

	client->cl_flags.f_cached = 1;
	entity_send(client->cl_fde, &client->cl_entity, client_response_done, client, 0);
}

/*
 * Called when response was finished writing to the client.
 */
/*ARGSUSED*/
static void
client_response_done(entity, data, res)
	struct http_entity *entity;
	void *data;
	int res;
{
struct	http_client	*client = data;

	WDEBUG((WLOG_DEBUG, "client_response_done: called, res=%d", res));

	if (client->cl_cfd) {
		if (close(client->cl_cfd) < 0) {
			wlog(WLOG_WARNING, "closing cache file: %s", strerror(errno));
		}
	}
	
	if (client->cl_co) {
		int complete = (res != -1);
		struct header_list *hdr;

		/*
		 * The server may have indicated that we shouldn't cache this document.
		 * If so, release it in an incomplete state so it gets evicted.
		 */

		/*
		 * HTTP/1.0 Pragma
		 */
		hdr = header_find(&client->cl_entity.he_headers, "Pragma");
		if (hdr) {
			char **pragmas = wstrvec(hdr->hl_value, ",", 0);
			char **s;
			for (s = pragmas; *s; ++s) {
				if (!strcasecmp(*s, "no-cache")) {
					complete = 0;
					break;
				}
			}
			wstrvecfree(pragmas);
		}

		/*
		 * HTTP/1.1 Cache-Control
		 */
		hdr = header_find(&client->cl_entity.he_headers, "Cache-Control");
		if (hdr) {
			char **controls = wstrvec(hdr->hl_value, ",", 0);
			char **s;
			for (s = controls; *s; ++s) {
				/*
				 * According to the standard, we can still cache no-cache
				 * documents, but we have to revalidate them on every request.
				 */
				if (!strcasecmp(*s, "no-cache") ||
				    !strcasecmp(*s, "private") ||
				    !strcasecmp(*s, "no-store")) {
					complete = 0;
					break;
				}
			}
			wstrvecfree(controls);
		}

		wcache_release(client->cl_co, complete);
	}
	
	client_log_request(client);
	client_close(client);
}
	
static void
client_close(client)
	struct http_client *client;
{
	WDEBUG((WLOG_DEBUG, "close client %d", client->cl_fde->fde_fd));
	assert(!client->cl_flags.f_closed);
	client->cl_flags.f_closed = 1;
	if (client->cl_wrtbuf)
		wfree(client->cl_wrtbuf);
	if (client->cl_path)
		wfree(client->cl_path);
	wnet_close(client->cl_fde->fde_fd);
	if (client->cl_backendfde)
		wnet_close(client->cl_backendfde->fde_fd);
	entity_free(&client->cl_entity);
	
	client->fe_next = freelist.fe_next;
	freelist.fe_next = client;
}

static void
client_send_error(client, errnum, errdata, status, statstr)
	struct http_client *client;
	int errnum, status;
	const char *errdata, *statstr;
{
	FILE		*errfile;
	char		 errbuf[8192];
	char		*p = errbuf, *u;
	ssize_t		 size;
	
	if (client->cl_co)
		wcache_release(client->cl_co, 0);

	if (errnum >= 0) {
		if ((errfile = fopen(error_files[errnum], "r")) == NULL) {
			client_close(client);
			return;
		}

		if ((size = fread(errbuf, 1, sizeof(errbuf) - 1, errfile)) < 0) {
			(void)fclose(errfile);
			client_close(client);
			return;
		}

		(void)fclose(errfile);
		errbuf[size] = '\0';

		if (!errdata)
			errdata = "Unknown error";
		if (!client->cl_path)
			client->cl_path = wstrdup("NONE");

		u = NULL;

		while (*p) {
			switch(*p) {
			case '%':
				switch (*++p) {
				case 'U':
					realloc_strcat(&u, client->cl_path);
					break;
				case 'D':
					realloc_strcat(&u, current_time_str);
					break;
				case 'H':
					realloc_strcat(&u, my_hostname);
					break;
				case 'E':
					realloc_strcat(&u, errdata);
					break;
				case 'V':
					realloc_strcat(&u, my_version);
					break;
				case 'C': {
					char *s = wmalloc(4);
					sprintf(s, "%d", status);
					realloc_strcat(&u, s);
					wfree(s);
					break;
				}
				case 'S':
					realloc_strcat(&u, statstr);
					break;
				default:
					break;
				}
				p++;
				continue;
			default:
				realloc_addchar(&u, *p);
				break;
			}
			++p;
		}


		client->cl_wrtbuf = u;
	}

	header_free(&client->cl_entity.he_headers);
	bzero(&client->cl_entity.he_headers, sizeof(client->cl_entity.he_headers));
	header_add(&client->cl_entity.he_headers, wstrdup("Date"), wstrdup(current_time_str));
	header_add(&client->cl_entity.he_headers, wstrdup("Expires"), wstrdup(current_time_str));
	header_add(&client->cl_entity.he_headers, wstrdup("Server"), wstrdup(my_version));
	header_add(&client->cl_entity.he_headers, wstrdup("Connection"), wstrdup("close"));

	entity_set_response(&client->cl_entity, 1);
	client->cl_entity.he_rdata.response.status = status;
	client->cl_entity.he_rdata.response.status_str = statstr;
	if (errnum >= 0) {
		header_add(&client->cl_entity.he_headers, wstrdup("Content-Type"), wstrdup("text/html"));
		client->cl_entity.he_source_type = ENT_SOURCE_BUFFER;
		client->cl_entity.he_source.buffer.addr = client->cl_wrtbuf;
		client->cl_entity.he_source.buffer.len = strlen(client->cl_wrtbuf);
	} else {
		client->cl_entity.he_source_type = ENT_SOURCE_NONE;
	}

	client->cl_entity.he_flags.cachable = 0;
	entity_send(client->cl_fde, &client->cl_entity, client_response_done, client, 0);
}

static void
client_log_request(client)
	struct http_client *client;
{
	int	i;
	
	if (!config.access_log)
		return;

	i = fprintf(alf, "[%s] %s %s \"%s\" %d %s %s\n",
			current_time_short, client->cl_fde->fde_straddr,
			request_string[client->cl_reqtype],
			client->cl_path, client->cl_entity.he_rdata.response.status,
			client->cl_backend ? client->cl_backend->be_name : "-",
			client->cl_flags.f_cached ? "HIT" : "MISS");
	if (i < 0) {
		wlog(WLOG_ERROR, "writing logfile: %s", strerror(errno));
		exit(8);
	}
	
	if (fflush(alf) == EOF) {
		wlog(WLOG_ERROR, "flushing logfile: %s", strerror(errno));
		exit(8);
	}
}

static void
do_cache_write(buf, len, data)
	const char *buf;
	size_t len;
	void *data;
{
struct	http_client	*client = data;

	if (write(client->cl_cfd, buf, len) < 0) {
		/*EMPTY*/
		WDEBUG((WLOG_WARNING, "writing cached data: %s", strerror(errno)));
	}
	client->cl_co->co_size += len;
}

static int
removable_header(hdr)
	const char *hdr;
{
static const char *removable_headers[] = {
	"Connection",
	"Keep-Alive",
	"Proxy-Authenticate",
	"Proxy-Authorization",
	"TE",
	"Trailers",
	"Transfer-Encoding",
	"Upgrade",
	NULL,
};
	const char **s;
	for (s = &removable_headers[0]; *s; s++)
		if (!strcasecmp(*s, hdr))
			return 1;
	return 0;
}


