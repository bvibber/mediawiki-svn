/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp: HTTP implementation.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

/*
 * The logic of whttp is explained in whttp_entity.c
 */

#ifndef _GNU_SOURCE
# define _GNU_SOURCE	/* glibc strptime */
#endif

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

struct http_client : freelist_allocator<http_client> {
		http_client(fde *e) : cl_fde(e) {
			cl_entity = new http_entity;
		}
		~http_client() {
			if (cl_wrtbuf)
				wfree(cl_wrtbuf);
			if (cl_path)
				wfree(cl_path);
			delete cl_entity;
			wnet_close(cl_fde->fde_fd);
			if (cl_backendfde)
				wnet_close(cl_backendfde->fde_fd);
		}

		
struct	fde		*cl_fde;	/* backref to fd			*/
	int		 cl_reqtype;	/* request type or 0			*/
	char		*cl_path;	/* path they want			*/
	char		*cl_wrtbuf;	/* write buf (either to client or be)	*/
struct	backend		*cl_backend;	/* backend servicing this client	*/
struct	fde		*cl_backendfde;	/* fde for backend			*/
struct	http_entity	*cl_entity;	/* reply to send back			*/

	/* Cache-related data */
	int		 cl_cfd;	/* FD of cache file for writing, or 0	*/
struct	cache_object	*cl_co;		/* Cache object				*/
	struct {
		unsigned int	f_cached:1;
		unsigned int	f_closed:1;
		unsigned int	f_http11:1;	/* Client understands HTTP/1.1		*/
	}		 cl_flags;
	size_t		 cl_dsize;	/* Object size				*/
enum	encoding	 cl_enc;
struct	http_client	*fe_next;	/* freelist 				*/
};

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

static int const default_udplog_port = 4445;
static int udplog_sock;
static int udplog_count;
static bool do_udplog;

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
	snprintf(via_hdr, sizeof(via_hdr), "1.1 %s (%s)", my_hostname, my_version);

	hsize = sizeof("MISS from ") + strlen(my_hostname);
	cache_hit_hdr = (char *)wmalloc(hsize + 1);
	cache_miss_hdr = (char *)wmalloc(hsize + 1);
	
	if (cache_hit_hdr == NULL || cache_miss_hdr == NULL)
		outofmemory();
	
	snprintf(cache_hit_hdr, hsize, "HIT from %s", my_hostname);
	snprintf(cache_miss_hdr, hsize, "MISS from %s", my_hostname);
}

void
whttp_reconfigure(void)
{
	/* file logging */
	if (config.access_log.size()) {
		if ((alf = fopen(config.access_log.c_str(), "a")) == NULL) {
			wlog(WLOG_WARNING, "opening %s: %s", config.access_log.c_str(), strerror(errno));
		}
	}

	/* UDP logging */
	if (config.udp_log) {
	struct addrinfo	*res, *r, hints;
	int	i;
		if (config.udplog_port == 0)
			config.udplog_port = default_udplog_port;
		memset(&hints, 0, sizeof(hints));
		hints.ai_socktype = SOCK_DGRAM;
		if ((i = getaddrinfo(config.udplog_host.c_str(),
				lexical_cast<string>(config.udplog_port).c_str(),
				&hints, &r)) != 0) {
			wlog(WLOG_WARNING, "resolving UDP log host %s: %s; disabling UDP logging",
				config.udplog_host.c_str(),
				gai_strerror(i));
			return;
		}

		for (res = r; res; res = res->ai_next) {
			if ((udplog_sock = socket(res->ai_family, res->ai_socktype, res->ai_protocol)) == -1) {
				wlog(WLOG_WARNING, "%s[%s]:%d: %s",
					config.udplog_host.c_str(),
					wnet::straddr(res->ai_addr, res->ai_addrlen).c_str(),
					config.udplog_port, strerror(errno));
				continue;
			}
			if (connect(udplog_sock, res->ai_addr, res->ai_addrlen) == -1) {
				wlog(WLOG_WARNING, "%s[%s]:%d: %s",
					config.udplog_host.c_str(),
					wnet::straddr(res->ai_addr, res->ai_addrlen).c_str(),
					config.udplog_port, strerror(errno));
				close(udplog_sock);
				udplog_sock = -1;
				continue;
			}
			break;
		}
		if (udplog_sock == -1) {
			wlog(WLOG_WARNING, "could not connect to UDP log host; disabling UDP logging");
			return;
		}
		do_udplog = true;
		wlog(WLOG_NOTICE, "UDP logging to %s[%s]:%d, sample rate 1/%d",
			config.udplog_host.c_str(),
			wnet::straddr(res->ai_addr, res->ai_addrlen).c_str(),
			config.udplog_port, config.udplog_sample);
		freeaddrinfo(r);
	}

}

void
whttp_shutdown(void)
{
	wfree(cache_hit_hdr);
	wfree(cache_miss_hdr);
}

/*
 * Called by wnet_accept to regiister a new client.  Reads the request headers
 * from the client.
 */
void
http_new(fde *e)
{
struct	http_client	*cl;

	cl = new http_client(e);
	cl->cl_entity->he_source_type = ENT_SOURCE_FDE;
	cl->cl_entity->he_source.fde.fde = e;
	cl->cl_entity->he_rdata.request.contlen = -1;
		
	WDEBUG((WLOG_DEBUG, "http_new: starting header read for %d", cl->cl_fde->fde_fd));
	entity_read_headers(cl->cl_entity, client_read_done, cl);
}

/*
 * Called when the initial request has been read.  Checks if the object is
 * cached, and starts a backend request if not.  If it it, sends the cached
 * object to the client.
 */
/*ARGSUSED*/
static void
client_read_done(http_entity *entity, void *data, int res)
{
struct	http_client	*client = (http_client *)data;
	char		*pragma, *cache_control, *ifmod;
	vector<qvalue>	 acceptenc;
	qvalue		 val;
	int		 cacheable = 1;

	WDEBUG((WLOG_DEBUG, "client_read_done: called, res=%d", res));

	if (res < -1) {
		client_send_error(client, ERR_BADREQUEST, ent_errors[-res], 400, "Bad request (#10.4.1)");
		return;
	} else if (res == -1) {
		delete client;
		return;
	} else if (res == 1) {
		delete client;
		return;
	}
	
	if (client->cl_entity->he_rdata.request.httpmaj >= 1 &&
	    client->cl_entity->he_rdata.request.httpmin >= 1)
		client->cl_flags.f_http11 = 1;

	if (client->cl_entity->he_rdata.request.host == NULL)
		client->cl_path = wstrdup(client->cl_entity->he_rdata.request.path);
	else {
		int	len;
		
		len = strlen(client->cl_entity->he_rdata.request.host) +
			strlen(client->cl_entity->he_rdata.request.path) + 7;
		
		client->cl_path = (char *)wmalloc(len + 1);
		if (client->cl_path == NULL)
			outofmemory();
		snprintf(client->cl_path, len + 1, "http://%s%s",
				client->cl_entity->he_rdata.request.host,
				client->cl_entity->he_rdata.request.path);
	}
	
	client->cl_reqtype = client->cl_entity->he_rdata.request.reqtype;

	pragma = entity->he_h_pragma;
	cache_control = entity->he_h_cache_control;

	if (pragma) {
		char **pragmas = wstrvec(pragma, ",", 0);
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
		char **cache_controls = wstrvec(cache_control, ",", 0);
		char **s;
		for (s = cache_controls; *s; ++s) {
			if (!strcasecmp(*s, "no-cache")) {
				cacheable = 0;
				break;
			}
		}
		wstrvecfree(cache_controls);
	}

	while (qvalue_remove_best(entity->he_rdata.request.accept_encoding, val)) {
		WDEBUG((WLOG_DEBUG, "client offers [%s] q=%f", val.name, (double) val.val));
		if ((client->cl_enc = accept_encoding(val.name)) != E_NONE)
			break;
	}

	/*
	 * Check for cached object.
	 */
	if (config.ncaches && client->cl_reqtype == REQTYPE_GET) {
		if (cacheable)
			client->cl_co = wcache_find_object(client->cl_path, &client->cl_cfd,
				WCACHE_RDWR);
		else
			client->cl_co = wcache_find_object(client->cl_path, &client->cl_cfd,
				WCACHE_WRONLY);

		if (cacheable && client->cl_co && client->cl_co->co_time && 
		    (ifmod = entity->he_h_if_modified_since)) {
			char *s;
			time_t t;
			struct tm m;
			s = strptime(ifmod, "%a, %d %b %Y %H:%M:%S", &m);
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
	if (get_backend(client->cl_path, proxy_start_backend, client, 0) == -1) {
		client_send_error(client, ERR_GENERAL, 
			"No backends were available to service your request", 503, 
			"Service unavailable (#10.5.4)");
		return;
	}
}

/*
 * Called when backend is ready.  backend==NULL if none was found.
 */
static void
proxy_start_backend(backend *backend, fde *e, void *data)
{
struct	http_client	*client = (http_client *)data;
struct	header		*hdr;
vector<header *>::iterator	it, end;
	int		 error = 0;
	socklen_t	 len = sizeof(error);
	
	WDEBUG((WLOG_DEBUG, "proxy_start_backend: called; for client=%d", client->cl_fde->fde_fd));
	
	if (backend == NULL) {
		client_send_error(client, ERR_GENERAL, 
			"No backends were available to service your request", 
			503, "Service unavailable (#10.5.4)");
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
	for (const char **s = removable_headers; *s; ++s) {
		client->cl_entity->he_headers.remove(*s);
	}

	evbuffer_add_printf(client->cl_entity->he_extraheaders, "X-Forwarded-For: %s\r\n",
		client->cl_fde->fde_straddr);
	client->cl_entity->he_headers.add("Connection", "close");
	/*
	 * POST requests require Content-Length.
	 */
	if (client->cl_reqtype == REQTYPE_POST) {
		if (client->cl_entity->he_rdata.request.contlen == -1) {
			client_send_error(client, ERR_BADREQUEST, "POST request without Content-Length",
						411, "Length required (#10.4.12)");
			return;
		}
		
		WDEBUG((WLOG_DEBUG, "client content-length=%d", client->cl_entity->he_rdata.request.contlen));
		client->cl_entity->he_source_type = ENT_SOURCE_FDE;
		client->cl_entity->he_source.fde.fde = client->cl_fde;
		client->cl_entity->he_source.fde.len = client->cl_entity->he_rdata.request.contlen;
	} else
		client->cl_entity->he_source_type = ENT_SOURCE_NONE;
	
	entity_send(e, client->cl_entity, backend_headers_done, client, 0);
}

/*
 * Called when clients request was written to the backend.
 */
/*ARGSUSED*/
static void
backend_headers_done(http_entity *entity, void *data, int res)
{
struct	http_client	*client = (http_client *)data;
	
	WDEBUG((WLOG_DEBUG, "backend_headers_done: called"));
	if (res == -1) {
		client_send_error(client, ERR_GENERAL, strerror(errno), 503,
			"Service unavailable (#10.5.4)");
		return;
	}
	
	delete client->cl_entity;
	client->cl_entity = new http_entity;
	client->cl_entity->he_source_type = ENT_SOURCE_FDE;
	client->cl_entity->he_source.fde.fde = client->cl_backendfde;
	client->cl_entity->he_source.fde.len = -1;
	
	entity_set_response(client->cl_entity, 1);

	/*
	 * This should probably be handled somewhere inside
	 * whttp_entity.c ...
	 */
	entity_read_headers(client->cl_entity, client_headers_done, client);
}

/*
 * Called when backend's headers are finished reading.
 */
static void
client_headers_done(http_entity *entity, void *data, int res)
{
struct	http_client	*client = (http_client *)data;
	
	WDEBUG((WLOG_DEBUG, "client_headers_done: called"));
	
	if (res == -1) {
		delete client;
		return;
	} else if (res < -1 || res == 1) {
		client_send_error(client, ERR_GENERAL, 
			res == 1 ? "Server response contained no data." : ent_errors[-res], 503,
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
		char *lastmod;

		entity->he_cache_callback = do_cache_write;
		entity->he_cache_callback_data = client;
		client->cl_entity->he_headers.dump(client->cl_cfd);

		/*
		 * Look for last-modified
		 */
		if ((lastmod = entity->he_h_last_modified) != NULL) {
			struct tm tim;
			char *lm;
			lm = strptime(lastmod, "%a, %d %b %Y %H:%M:%S", &tim);
			if (lm) {
				WDEBUG((WLOG_DEBUG, "last-modified: %d", mktime(&tim)));
				client->cl_co->co_time = mktime(&tim);
			}
		}
	}
	
	client->cl_entity->he_headers.add("Via", via_hdr);
	client->cl_entity->he_headers.add("X-Cache", cache_miss_hdr);
	client->cl_entity->he_source.fde.len = -1;
	if (config.compress)
		client->cl_entity->he_encoding = client->cl_enc;

	if (!HAS_BODY(client->cl_entity->he_rdata.response.status))
		client->cl_entity->he_source_type = ENT_SOURCE_NONE;

	entity_send(client->cl_fde, client->cl_entity, client_response_done, client,
			client->cl_flags.f_http11 ? ENT_CHUNKED_OKAY : 0);
}

/*
 * Write a cached object to the client.
 */
static void
client_write_cached(http_client *client)
{
	size_t	 plen;
	char	*cache_path;
struct	stat	 sb;
	char	 size[64];

	plen = strlen(config.caches[0].dir) + client->cl_co->co_plen + 12 + 2;
	if ((cache_path = (char *)wcalloc(1, plen + 1)) == NULL)
		outofmemory();

	if (fstat(client->cl_cfd, &sb) < 0) {
		wlog(WLOG_WARNING, "stat(%s): %s", cache_path, strerror(errno));
		client_send_error(client, ERR_CACHE_IO, strerror(errno),
			500, "Internal server error (#10.5.1)");
		wfree(cache_path);
		return;
	}
		
	wfree(cache_path);
	
	delete client->cl_entity;
	client->cl_entity = new http_entity;
	client->cl_entity->he_headers.undump(client->cl_cfd, &client->cl_entity->he_source.fd.off);
	client->cl_entity->he_headers.add("Via", via_hdr);
	client->cl_entity->he_headers.add("X-Cache", cache_hit_hdr);
	if (!client->cl_enc && !client->cl_entity->he_headers.find("Content-Length")) {
		evbuffer_add_printf(client->cl_entity->he_extraheaders, "Content-Length: %lu\r\n",
			(unsigned long) client->cl_co->co_size);
	}

	entity_set_response(client->cl_entity, 1);
	client->cl_entity->he_rdata.response.status = 200;
	client->cl_entity->he_rdata.response.status_str = "OK";
			
	client->cl_entity->he_source.fd.fd = client->cl_cfd;
	client->cl_entity->he_source.fd.size = sb.st_size;
	if (config.compress)
		client->cl_entity->he_encoding = client->cl_enc;

	client->cl_entity->he_source_type = ENT_SOURCE_FILE;

	client->cl_flags.f_cached = 1;
	entity_send(client->cl_fde, client->cl_entity, client_response_done, client, 0);
}

/*
 * Called when response was finished writing to the client.
 */
/*ARGSUSED*/
static void
client_response_done(http_entity *entity, void *data, int res)
{
struct	http_client	*client = (http_client *)data;

	WDEBUG((WLOG_DEBUG, "client_response_done: called, res=%d", res));

	if (client->cl_cfd) {
		if (close(client->cl_cfd) < 0) {
			wlog(WLOG_WARNING, "closing cache file: %s", strerror(errno));
		}
	}
	
	if (client->cl_co) {
	int		 complete = (res != -1);
	struct header	*hdr;

		/*
		 * The server may have indicated that we shouldn't cache this document.
		 * If so, release it in an incomplete state so it gets evicted.
		 */

		/*
		 * HTTP/1.0 Pragma
		 */
		hdr = client->cl_entity->he_headers.find("Pragma");
		if (hdr) {
			char **pragmas = wstrvec(hdr->hr_value, ",", 0);
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
		hdr = client->cl_entity->he_headers.find("Cache-Control");
		if (hdr) {
			char **controls = wstrvec(hdr->hr_value, ",", 0);
			char **s;
			for (s = controls; *s; ++s) {
				/*
				 * According to the standard, we can still cache no-cache
				 * documents, but we have to revalidate them on every request.
				 */
				if (!strcasecmp(*s, "no-cache") ||
				    (!config.cache_private && !strcasecmp(*s, "private")) ||
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
	delete client;
}

static void
client_send_error(http_client *client, int errnum, const char * errdata, int status, const char *statstr)
{
	FILE		*errfile;
	char		 errbuf[8192];
	char		*p = errbuf, *u;
	ssize_t		 size;
	
	if (client->cl_co)
		wcache_release(client->cl_co, 0);

	if (errnum >= 0) {
		if ((errfile = fopen(error_files[errnum], "r")) == NULL) {
			delete client;
			return;
		}

		if ((size = fread(errbuf, 1, sizeof(errbuf) - 1, errfile)) < 0) {
			(void)fclose(errfile);
			delete client;
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
					char *s = (char *)wmalloc(4);
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

	delete client->cl_entity;
	client->cl_entity = new http_entity;

	client->cl_entity->he_headers.add("Date", current_time_str);
	client->cl_entity->he_headers.add("Expires", current_time_str);
	client->cl_entity->he_headers.add("Server", my_version);
	client->cl_entity->he_headers.add("Connection", "close");

	entity_set_response(client->cl_entity, 1);
	client->cl_entity->he_rdata.response.status = status;
	client->cl_entity->he_rdata.response.status_str = statstr;
	if (errnum >= 0) {
		client->cl_entity->he_headers.add("Content-Type", "text/html");
		client->cl_entity->he_source_type = ENT_SOURCE_BUFFER;
		client->cl_entity->he_source.buffer.addr = client->cl_wrtbuf;
		client->cl_entity->he_source.buffer.len = strlen(client->cl_wrtbuf);
	} else {
		client->cl_entity->he_source_type = ENT_SOURCE_NONE;
	}

	client->cl_entity->he_flags.cachable = 0;
	entity_send(client->cl_fde, client->cl_entity, client_response_done, client, 0);
}

static void
client_log_request(http_client *client)
{
	int	i;
	
	if (alf) {
		i = fprintf(alf, "[%s] %s %s \"%s\" %d %s %s\n",
				current_time_short, client->cl_fde->fde_straddr,
				request_string[client->cl_reqtype],
				client->cl_path, client->cl_entity->he_rdata.response.status,
				client->cl_backend ? client->cl_backend->be_name.c_str() : "-",
				client->cl_flags.f_cached ? "HIT" : "MISS");
		if (i < 0) {
			wlog(WLOG_ERROR, "writing access log: %s; log will be closed", strerror(errno));
			fclose(alf);
			alf = NULL;
		}
	}

	if (config.udp_log) {
	char	 buf[65535];
	char	*bufp = buf, *endp = buf + sizeof(buf);
		if (++udplog_count != config.udplog_sample)
			return;
		udplog_count = 0;
		/*
		 * The log format is a packed binary strucure laid out like this:
		 *
		 *    <curtime><addrlen><straddr><reqtype><pathlen><reqpath><status>
		 *    <belen><bestr><cached><docsize>
		 *
		 * curtime is a 32-bit Unix timestamp.  *len are the length in bytes
		 * of the next element.  straddr is the ASCII IP address of the client.
		 * reqtype is an 8-bit integer:
		 *   0 - GET
		 *   1 - POST
		 *   2 - HEAD
		 *   3 - TRACE
		 *   4 - OPTIONS
		 * reqpath is the request path, including "http://" and the host.
		 * status is a 16-bit HTTP status code for the response.
		 * bestr is the ASCII IP address of the backend.  cached is an 
		 * 8-bit value, 1 if the request was served from the cache and 0 if not.
		 * docsize is the size of the response object, excluding headers.
		 */
#define HAS_SPACE(b,l) (((b) + (l)) < endp)
#define ADD_UINT32(b,i)	if (HAS_SPACE(b,4)) { *(uint32_t*)b = i; b += 4; }
#define ADD_UINT16(b,i)	if (HAS_SPACE(b,2)) { *(uint16_t*)b = i; b += 2; }
#define ADD_UINT8(b,i)	if (HAS_SPACE(b,1)) { *(uint8_t*)b = i; b += 1; }
#define ADD_STRING(b,s) do {	uint32_t len = strlen(s);		\
				if (HAS_SPACE(b,4 + len)) {		\
					ADD_UINT32(b,len);		\
					memcpy(b, s, len);		\
					b += len;			\
				}					\
			} while (0)
		ADD_UINT32(bufp, (uint32_t)time(NULL));
		ADD_STRING(bufp, client->cl_fde->fde_straddr);
		ADD_UINT8(bufp, client->cl_reqtype);
		ADD_STRING(bufp, client->cl_path);
		ADD_UINT16(bufp, client->cl_entity->he_rdata.response.status);
		ADD_STRING(bufp, client->cl_backend ? client->cl_backend->be_name.c_str() : "-");
		ADD_UINT8(bufp, client->cl_flags.f_cached ? 1 : 0);
		ADD_UINT32(bufp, client->cl_entity->he_size);
		write(udplog_sock, buf, bufp - buf);
#if 0
		if (write(udplog_sock, buf, bufp - buf) < 0) {
			wlog(WLOG_ERROR, "writing to UDP log host: %s", strerror(errno));
			close(udplog_sock);
			udplog_sock = 0;
			config.udp_log = false;
		}
#endif
	}
}

static void
do_cache_write(const char *buf, size_t len, void *data)
{
struct	http_client	*client = (http_client *)data;

	if (write(client->cl_cfd, buf, len) < 0) {
		/*EMPTY*/
		WDEBUG((WLOG_WARNING, "writing cached data: %s", strerror(errno)));
	}
	client->cl_co->co_size += len;
}
