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
#include <pthread.h>

#include <utility>
#include <deque>
using std::deque;
using std::min;

#include "willow.h"
#include "whttp.h"
#include "wnet.h"
#include "wbackend.h"
#include "wconfig.h"
#include "wlogwriter.h"
#include "whttp_entity.h"
#include "wlog.h"
#include "wcache.h"
#include "radix.h"
#include "chunking.h"
#include "flowio.h"

using namespace wnet;

#ifndef MAXHOSTNAMELEN
# define MAXHOSTNAMELEN HOST_NAME_MAX /* SysV / BSD disagreement */
#endif

/*
 * Error handling.
 */
#define ERR_GENERAL	0	/* unspecified error			*/
#define ERR_BADREQUEST	1	/* client request invalid		*/
#define ERR_BADRESPONSE	2	/* backend response invalid		*/
#define ERR_CACHE_IO	3	/* i/o failure reading cache		*/
#define ERR_BLOCKED	4	/* client denied by configuration	*/

static const char *error_files[] = {
	/* ERR_GENERAL		*/	DATADIR "/errors/ERR_GENERAL",
	/* ERR_BADREQUEST	*/	DATADIR "/errors/ERR_BADREQUEST",
	/* ERR_BADRESPONSE	*/	DATADIR "/errors/ERR_BADRESPONSE",
	/* ERR_CACHE_IO		*/	DATADIR "/errors/ERR_CACHE_IO",
	/* ERR_BLOCKED		*/	DATADIR "/errors/ERR_BLOCKED",
};

struct http_client : freelist_allocator<http_client> {
		http_client(fde *e) : cl_fde(e) {
			cl_entity = new http_entity;
		}
		~http_client() {
			if (cl_wrtbuf)
				wfree(cl_wrtbuf);
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
		unsigned int	f_blocked:1;
	}		 cl_flags;
	size_t		 cl_dsize;	/* Object size				*/
enum	encoding	 cl_enc;
struct	http_client	*fe_next;	/* freelist 				*/
};

#if 0
static void proxy_start_backend(struct backend *, struct fde *, void *);
static void client_read_done(struct http_entity *, void *, int);
static void client_response_done(struct http_entity *, void *, int);
static void backend_headers_done(struct http_entity *, void *, int);
static void client_headers_done(struct http_entity *, void *, int);
static void client_write_cached(struct http_client *);
#endif
static int removable_header(const char *);

#if 0
static void client_send_error(struct http_client *, int errcode, const char *error,
				int status, const char *statusstr);
#endif
static void client_log_request(struct http_client *);

static void do_cache_write(const char *, size_t, void *);

static void *client_thread(void *);
static void stats_merge(int, short, void *);

static char via_hdr[1024];
static char *cache_hit_hdr;
static char *cache_miss_hdr;

tss<event> merge_ev;

char my_hostname[MAXHOSTNAMELEN + 1];
static char my_version[64];
static int logwr_pipe[2];
static FILE *alf;
lockable alf_lock;

static int const default_udplog_port = 4445;
static int udplog_sock;
static int udplog_count;
static bool do_udplog;
lockable udp_lock;

struct error_transform_filter : io::buffering_filter, freelist_allocator<error_transform_filter>
{
	string const	_url;
	string const	_errdata;
	string const	_statstr;
	int		 _status;

	error_transform_filter(
		string const &url, 
		string const &errdata, 
		string const &statstr,
		int status);

	io::sink_result	bf_transform(char const *, size_t, ssize_t &);
};		

struct httpcllr {
	/* Accept a new client and start processing it. */
	httpcllr(fde *);
	~httpcllr();

		/* reading request from client */
	void header_read_complete		(void);
	void header_read_error			(void);
		/* sending request to backend */
	void backend_ready			(backend *, fde *, int);
	void backend_write_done			(void);
	void backend_write_error		(void);
		/* reading request from backend */
	void backend_read_headers_done		(void);
	void backend_read_headers_error 	(void);
		/* sending request to client */
	void send_headers_to_client_done	(void);
	void send_headers_to_client_error	(void);
	void send_body_to_client_done		(void);
	void send_body_to_client_error		(void);
		/* sending errors to the client */
	void send_error_to_client		(void);
	void error_send_headers_done		(void);
	void error_send_done			(void);

	void send_error(int, char const *, int, char const *);

	fde		*_client_fde;
	backend		*_backend;
	fde		*_backend_fde;

	io::fde_spigot		*_client_spigot;
	io::fde_spigot		*_backend_spigot;
	io::fde_sink		*_backend_sink,
				*_client_sink;
	header_parser		 _header_parser,
				 _backend_headers;
	dechunking_filter	*_dechunking_filter;
	header_spigot		*_error_headers;
	io::file_spigot		*_error_body;
	error_transform_filter	*_error_filter;
	chunking_filter		*_chunking_filter;
};

httpcllr::httpcllr(fde *e)
	: _client_fde(e)
	, _backend(NULL)
	, _backend_fde(NULL)
	, _client_spigot(new io::fde_spigot(e))
	, _backend_spigot(NULL)
	, _backend_sink(NULL)
	, _client_sink(NULL)
	, _dechunking_filter(NULL)
	, _error_headers(NULL)
	, _error_body(NULL)
	, _error_filter(NULL)
	, _chunking_filter(NULL)
{
	/*
	 * Start by reading headers.
	 */
	_client_spigot->completed_callee(this, &httpcllr::header_read_complete);
	_client_spigot->error_callee(this, &httpcllr::header_read_error);
	_client_spigot->sp_connect(&_header_parser);
	_client_spigot->sp_uncork();
}

httpcllr::~httpcllr(void)
{
	delete _client_spigot;
	delete _backend_spigot;
	delete _backend_sink;
	delete _client_sink;
	delete _dechunking_filter;
	delete _error_headers;
	delete _error_filter;
	delete _error_body;
	delete _chunking_filter;
	if (_backend_fde)
		wnet_close(_backend_fde->fde_fd);
	wnet_close(_client_fde->fde_fd);
}

static const char *removable_headers[] = {
	"Connection",
	"Keep-Alive",
	"Proxy-Authenticate",
	"Proxy-Authorization",
	"Proxy-Connection",
	"TE",
	"Trailers",
	"Upgrade",
	"If-Modified-Since",
	"Last-Modified",
	NULL,
};

void
httpcllr::header_read_complete(void)
{
	WDEBUG((WLOG_DEBUG, "header_read_complete()"));
	for (const char **s = removable_headers; *s; ++s)
		_header_parser._headers.remove(*s);
	_header_parser._headers.add("Connection", "close");


	if (_header_parser._http_reqtype == REQTYPE_POST && _header_parser._content_length == -1) {
		send_error(ERR_BADREQUEST, "POST request without content length",
			400, "Bad request");
		return;
	}

	/*
	 * Now parse the client's headers and decide what to do with
	 * the request.
	 */
	_client_spigot->sp_disconnect();
	if (gbep.get(_header_parser._http_path, 
		     polycaller<backend *, fde *, int>(*this, &httpcllr::backend_ready), 0) == -1)
		backend_ready(NULL, NULL, 0);
}

void
httpcllr::header_read_error(void)
{
	WDEBUG((WLOG_DEBUG, "header_read_error()"));
	stats.tcur->n_httpreq_fail++;
	send_error(ERR_BADREQUEST, "Could not parse client headers", 400, "Bad request");
}

void
httpcllr::backend_ready(backend *be, fde *e, int)
{
	WDEBUG((WLOG_DEBUG, "backend_ready"));
	if (be == NULL) {
		stats.tcur->n_httpreq_fail++;
		send_error(ERR_GENERAL, "No backends were available to serve your request", 
			503, "Internal server error");
		return;
	}

	/*
	 * Create the backend fde_sink, connect the header parser to it
	 * and start sending headers.
	 */
	_backend_fde = e;
	_backend = be;
	_backend_sink = new io::fde_sink(e);
	_header_parser.completed_callee(this, &httpcllr::backend_write_done);
	_header_parser.error_callee(this, &httpcllr::backend_write_error);
	_header_parser.sp_connect(_backend_sink);
	_header_parser.sp_uncork();
}

void
httpcllr::backend_write_error(void)
{
	WDEBUG((WLOG_DEBUG, "backend_write_error"));
	stats.tcur->n_httpreq_fail++;
	send_error(ERR_GENERAL, "Could not write request to backend", 503, "Internal server error");
}

void
httpcllr::backend_write_done(void)
{
	WDEBUG((WLOG_DEBUG, "backend_write_done"));
	/*
	 * Detach the backend sink and create a spigot to read the reply.
	 */
	_header_parser.sp_disconnect();

	_backend_headers.set_response();

	_backend_spigot = new io::fde_spigot(_backend_fde);
	_backend_spigot->completed_callee(this, &httpcllr::backend_read_headers_done);
	_backend_spigot->error_callee(this, &httpcllr::backend_read_headers_error);
	_backend_spigot->sp_connect(&_backend_headers);
	_backend_spigot->sp_uncork();
}

void
httpcllr::backend_read_headers_done(void)
{
	WDEBUG((WLOG_DEBUG, "backend_read_headers_done"));
	for (const char **s = removable_headers; *s; ++s)
		_backend_headers._headers.remove(*s);
	_backend_headers._headers.add("Connection", "close");

	if (_backend_headers._content_length == -1 && !_backend_headers._flags.f_chunked
		   && _header_parser._http_vers == http11 && !(config.msie_hack && _header_parser._is_msie))
		/* we will chunk the request later */
		_backend_headers._headers.add("Transfer-Encoding", "chunked");
	else if (_backend_headers._flags.f_chunked && _header_parser._http_vers == http10)
		_backend_headers._headers.remove("Transfer-Encoding");
	else if (_backend_headers._flags.f_chunked && config.msie_hack && _header_parser._is_msie)
		_backend_headers._headers.remove("Transfer-Encoding");

	/*
	 * Send the headers to the client.
	 */
	_backend_spigot->sp_disconnect();

	_client_sink = new io::fde_sink(_client_fde);
	_backend_headers.completed_callee(this, &httpcllr::send_headers_to_client_done);
	_backend_headers.error_callee(this, &httpcllr::send_headers_to_client_error);

	_backend_headers.sp_connect(_client_sink);
	_backend_headers.sp_uncork();
}

void
httpcllr::backend_read_headers_error(void)
{
	WDEBUG((WLOG_DEBUG, "backend_read_headers_error"));
	stats.tcur->n_httpreq_fail++;
	send_error(ERR_BADRESPONSE, "Could not parse backend response", 503, "Internal server error");
}

void
httpcllr::send_headers_to_client_done(void)
{
	WDEBUG((WLOG_DEBUG, "send_headers_to_client_done"));
	/*
	 * Now connect the backend directly to the client.
	 */ 
	_backend_spigot->error_callee(this, &httpcllr::send_body_to_client_error);
	_backend_spigot->completed_callee(this, &httpcllr::send_body_to_client_done);

	/*
	 * If the server is sending chunked data and the client is
	 * HTTP 1.0, insert a dechunking filter.
	 */
	_backend_spigot->sp_disconnect();
	if (_backend_headers._flags.f_chunked && _header_parser._http_vers == http10) {
		_dechunking_filter = new dechunking_filter;
		_backend_spigot->sp_connect(_dechunking_filter);
		_dechunking_filter->sp_connect(_client_sink);
	} else if (_backend_headers._content_length == -1 && !_backend_headers._flags.f_chunked
		   && _header_parser._http_vers == http11 && !(config.msie_hack && _header_parser._is_msie)) {
		/*
		 * Unchunked request without Content-Length.  Insert a chunking filter
		 * between the backend and the client so the client at least knows if we
		 * didn't send enough data.
		 */
		_chunking_filter = new chunking_filter;
		_backend_spigot->sp_connect(_chunking_filter);
		_chunking_filter->sp_connect(_client_sink);
	} else if (_backend_headers._flags.f_chunked && config.msie_hack && _header_parser._is_msie) {
		WDEBUG((WLOG_DEBUG, "MSIE client, inserting dechunking_filter"));
		_dechunking_filter = new dechunking_filter;
		_backend_spigot->sp_connect(_dechunking_filter);
		_dechunking_filter->sp_connect(_client_sink);
	} else {
		_backend_spigot->sp_connect(_client_sink);
	}
	_backend_spigot->sp_uncork();
}


void
httpcllr::send_body_to_client_done(void)
{
	WDEBUG((WLOG_DEBUG, "send_body_to_client_done"));
	stats.tcur->n_httpreq_ok++;
	delete this;
}

void
httpcllr::send_body_to_client_error(void)
{
	WDEBUG((WLOG_DEBUG, "send_body_to_client_error"));
	stats.tcur->n_httpreq_fail++;
	delete this;
}

void
httpcllr::send_headers_to_client_error(void)
{
	WDEBUG((WLOG_DEBUG, "send_headers_to_client_error"));
	stats.tcur->n_httpreq_fail++;
	delete this;
}

/*
 * Initialize whttp, start loggers.
 */
struct http_thread {
	pthread_t	thr;
	int		sv[2];

	void	execute		(void);
	void	accept_wakeup	(fde *, int);
};
vector<http_thread *> threads;

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

	wlog(WLOG_NOTICE, "whttp: starting %d worker threads", config.nthreads);
	for (int i = 0; i < config.nthreads; ++i) {
	http_thread	*t = new http_thread;
		wnet_socketpair(AF_UNIX, SOCK_DGRAM, 0, t->sv);
		wnet_add_accept_wakeup(t->sv[0]);
		threads.push_back(t);
		pthread_create(&t->thr, NULL, client_thread, t);
	}
}

void
http_thread::accept_wakeup(fde *e, int)
{
int	nfd, afd = sv[1];
	if (read(afd, &nfd, sizeof(nfd)) < sizeof(nfd)) {
		wlog(WLOG_ERROR, "accept_wakeup: reading fd: %s", strerror(errno));
		exit(1);
	}
	WDEBUG((WLOG_DEBUG, "accept_wakeup, nfd=%d", nfd));
	new httpcllr(&fde_table[nfd]);
}

static
void merge_sched(void)
{
timeval	 tv;
	tv.tv_usec = 250000;
	tv.tv_sec = 0;
	evtimer_set(merge_ev, stats_merge, NULL);
	event_base_set(evb, merge_ev);
	event_add(merge_ev, &tv);
}

static void *
client_thread(void *arg)
{
http_thread	*t = (http_thread *)arg;
	t->execute();
	return NULL;
}

void
http_thread::execute(void)
{
	make_event_base();
	stats.tcur = new stats_stru::abs_t;
	merge_ev = new event;
	memset(merge_ev, 0, sizeof(*merge_ev));
	merge_sched();
	ioloop->readback(sv[1], polycaller<fde *, int>(*this, &http_thread::accept_wakeup), 0);
	event_base_loop(evb, 0);
	wlog(WLOG_ERROR, "event_base_loop: %s", strerror(errno));
	exit(1);
}

static void
stats_merge(int, short, void *)
{
	{	HOLDING(stats.cur_lock);
		stats.cur.n_httpreq_ok += stats.tcur->n_httpreq_ok;
		stats.tcur->n_httpreq_ok = 0;
		stats.cur.n_httpreq_fail += stats.tcur->n_httpreq_fail;
		stats.tcur->n_httpreq_fail = 0;
		stats.cur.n_httpresp_ok += stats.tcur->n_httpresp_ok;
		stats.tcur->n_httpreq_ok = 0;
		stats.cur.n_httpresp_fail += stats.tcur->n_httpresp_fail;
		stats.tcur->n_httpresp_fail = 0;
	}
	merge_sched();
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

static string
errsafe(string const &s)
{
string::const_iterator	it = s.begin(), end = s.end();
string	res;
	res.reserve((long) (s.size() * 1.2));
	for (; it != end; ++it)
		switch (*it) {
		case '<':
			res += "&lt;";
			break;
		case '>':
			res += "&gt;";
			break;
		case '"':
			res += "&quot;";
			break;
		case '\'':
			res += "&apos;";
			break;
		default:
			res += *it;
		}
	return res;
}

error_transform_filter::error_transform_filter(
		string const &url, 
		string const &errdata, 
		string const &statstr,
		int status)
	: _url(url)
	, _errdata(errdata)
	, _statstr(statstr) 
	, _status(status) {
}

io::sink_result
error_transform_filter::bf_transform(char const *buf, size_t len, ssize_t &discard) 
{
string		 errtxt;
char const	*p = buf;
	errtxt.reserve((int) (len * 1.2));
	while (p < buf + len) {
		switch(*p) {
		case '%':
			if (p + 1 < buf + len) {
				switch (*++p) {
				case 'A':
					errtxt += errsafe(config.admin);
					break;
				case 'U':
					errtxt += _url;
					break;
				case 'D':
					errtxt += current_time_str;
					break;
				case 'H':
					errtxt += my_hostname;
					break;
				case 'E':
					errtxt += errsafe(_errdata);
					break;
				case 'V':
					errtxt += my_version;
					break;
				case 'C': {
				char	s[4];
					sprintf(s, "%d", _status);
					errtxt += s;
					break;
				}
				case 'S':
					errtxt += errsafe(_statstr);
					break;
				default:
					errtxt += *p;
					break;
				}
				p++;
				continue;
			}
			break;
		default:
			errtxt += *p;
			break;
		}
		++p;
	}
char	*r;
	r = new char[errtxt.size()];
	memcpy(r, errtxt.data(), errtxt.size());
	_buf.add(r, errtxt.size(), true);
	discard += len;
	return io::sink_result_okay;
}
		

void
httpcllr::send_error(int errnum, char const *errdata, int status, char const *statstr)
{
string	url = "NONE";
	WDEBUG((WLOG_DEBUG, "send_error; url=[%s]", _header_parser._http_path.c_str()));

	if (_header_parser._http_path.size())
		url = errsafe(_header_parser._http_path);

	_error_headers = new header_spigot(status, statstr);
	if (!_client_sink)
		_client_sink = new io::fde_sink(_client_fde);

	_error_headers->add("Date", current_time_str);
	_error_headers->add("Expires", current_time_str);
	_error_headers->add("Server", my_version);
	_error_headers->add("Connection", "close");
	_error_headers->add("Content-Type", "text/html;charset=UTF-8");

	_error_body = io::file_spigot::from_path(error_files[errnum], true);
	if (_error_body == NULL) {
		delete this;
		return;
	}
	_error_filter = new error_transform_filter(url, errdata, statstr, status);

	_error_headers->completed_callee(this, &httpcllr::error_send_headers_done);
	_error_headers->error_callee(this, &httpcllr::error_send_done);

	_error_headers->sp_connect(_client_sink);
	_error_headers->sp_uncork();
}

void
httpcllr::error_send_headers_done(void)
{
	_error_headers->sp_disconnect();
	_error_body->completed_callee(this, &httpcllr::error_send_done);
	_error_body->error_callee(this, &httpcllr::error_send_done);

	_error_body->sp_connect(_error_filter);
	_error_filter->sp_connect(_client_sink);
	_error_body->sp_uncork();
}

void
httpcllr::error_send_done(void)
{
	WDEBUG((WLOG_DEBUG, "error_send_done"));
	delete this;
}
#if 0
void
httpcllr::log_request(void)
{
int	i;

	if (alf) {
		HOLDING(alf_lock);
		i = fprintf(alf, "[%s] %s %s \"%s\" %lu %d %s %s\n",
				current_time_short, _client_fde->fde_straddr,
				request_string[client->cl_reqtype],
				client->cl_path, (unsigned long) client->cl_entity->he_size,
				client->cl_entity->he_rdata.response.status,
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
		ADD_UINT32(bufp, (uint32_t)time(NULL), endp);
		ADD_STRING(bufp, client->cl_fde->fde_straddr, endp);
		ADD_UINT8(bufp, client->cl_reqtype, endp);
		ADD_STRING(bufp, client->cl_path, endp);
		ADD_UINT16(bufp, client->cl_entity->he_rdata.response.status, endp);
		ADD_STRING(bufp, client->cl_backend ? client->cl_backend->be_name.c_str() : "-", endp);
		ADD_UINT8(bufp, client->cl_flags.f_cached ? 1 : 0, endp);
		ADD_UINT32(bufp, client->cl_entity->he_size, endp);
		HOLDING(udp_lock);
		write(udplog_sock, buf, bufp - buf);
	}
}
#endif
