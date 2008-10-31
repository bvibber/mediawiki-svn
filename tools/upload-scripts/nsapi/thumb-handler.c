/* Copyright (c) 2008 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

/*
 * This is a C implementation of the thumb-handler.php script as an NSAPI
 * module.  Running as NSAPI is faster, and more efficient (as we don't need
 * to keep a load of PHP processes around).
 *
 * To use it, first load the module in magnus.conf:
 *   Init fn="load-modules" shlib="thumb-handler.so" funcs="thumb-handler,thumb-handler-init"
 *   Init fn="thumb-handler-init" 
 * Then invoke it in obj.conf:
 *   Service method="(GET|HEAD)" type="*~magnus-internal/*" fn="thumb-handler"
 */

#include	<netdb.h>
#include	<nsapi.h>
#include	<stdio.h>
#include	<unistd.h>
#include	<string.h>
#include	<pcre.h>
#include	<curl/curl.h>
#include	<glib.h>

/* Size of the output array for pcre_exec */
#define OVECSIZE	15

typedef struct request {
	Session		*sn;
	Request		*rq;
	char const	*uri;
	char const	*uripath;
	GString		*thumburl;
	CURL		*curl;
struct	curl_slist	*headers;
#if 0
	char		*data;
	size_t		 sz;
#endif
	int		 output_started;
} request_t;

static char const *strip_hostname(char const *uri);
static int re_match(request_t *, char const *re, char const *subj, char ***output);
static int prepare_curl(request_t *);
static int add_headers(request_t *);
static char *make_xff_header(request_t *);
static GString *pblock_to_query(pblock const *);
static GString *hostname_from_site_lang(char const *site, char const *lang);
static int thumb_url_from_path(request_t *);
static size_t write_data(void *ptr, size_t, size_t, void *);
static void send_error(request_t *, int code, char const *fmt, ...);
static void free_request(request_t *);
static int start_output(request_t *, void *, size_t);

static void
free_request(req)
	request_t *req;
{
	if (req->thumburl)
		g_string_free(req->thumburl, TRUE);
	if (req->curl)
		curl_easy_cleanup(req->curl);
	if (req->headers)
		curl_slist_free_all(req->headers);
}

#ifdef __GNUC__
# define UNUSED __attribute__((unused))
#else
# define UNUSED
#endif

/*
 * cURL callback that writes data to the client
 */
static size_t
write_data(ptr, size, num, data)
	void *ptr, *data;
	size_t size, num;
{
size_t		 sz = size * num;
request_t	*req = (request_t *)data;
	if (!req->output_started) {
		if (start_output(req, ptr, sz) == -1)
			return 0;
		req->output_started = 1;
	}
	
	return net_write(req->sn->csd, ptr, sz);
}

/*
 * Handle initialisation on server startup.
 */
int thumb_handler_init(pblock *pb UNUSED, Session *sn, Request *rq)
{
	if (curl_global_init(CURL_GLOBAL_NOTHING) != 0) {
		log_error(LOG_FAILURE, "thumb-handler", sn, rq,
			   "failed to initialise cURL");
		return REQ_ABORTED;
	}
	log_error(LOG_INFORM, "thumb-handler", sn, rq, "thumb-handler $Id$ ready");
	return REQ_PROCEED;
}

static void
send_error(
	request_t *req,
	int code,
	char const *fmt,
	...)
{
va_list	 ap;
GString	*err = g_string_new(NULL);
	g_string_assign(err,
"<html>\r\n"
"<head><title>Thumbnail error</title></head>\r\n"
"<body>\r\n");
	
	va_start(ap, fmt);
#if GLIB_CHECK_VERSION(2, 14, 0)
	g_string_append_vprintf(err, fmt, ap);
#else
	/* <= 2.14.10 doesn't have g_string_append_vprintf */
	{
		int len;
		char *s;
		len = vsnprintf(NULL, 0, fmt, ap);
		s = malloc(len + 1);
		vsnprintf(s, len + 1, fmt, ap);
		g_string_append(err, s);
		free(s);
	}
#endif
	
	va_end(ap);

	g_string_append(err, "</body>\r\n</html>\r\n");
	pblock_nvinsert("content-type", "text/html;charset=UTF-8", req->rq->srvhdrs);
	pblock_nvinsert("cache-control", "no-cache", req->rq->srvhdrs);
	protocol_status(req->sn, req->rq, code, NULL);
	if (protocol_start_response(req->sn, req->rq) != REQ_NOACTION)
		net_write(req->sn->csd, err->str, err->len);
	g_string_free(err, TRUE);
}

/*
 * This function handles the actual request.
 */
int
thumb_handler(pblock *pb UNUSED, Session *sn, Request *rq)
{
char		 curlerror[CURL_ERROR_SIZE];
request_t	 req;

	memset(&req, 0, sizeof(req));
	req.sn = sn;
	req.rq = rq;
	
	/* Get the URI from the request */
	if ((req.uri = pblock_findval("uri", rq->reqpb)) == NULL) {
		/* Shouldn't happen, but just in case... */
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "received a request with a NULL URI");
		return REQ_ABORTED;
	}

	if (util_uri_is_evil(req.uri)) {
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "received a request with an evil URI: %s",
			   req.uri);
		return REQ_ABORTED;
	}
	
	/*
	 * If the URI starts with a hostname, i.e. http://example.com/...,
	 * strip it off.  Squid sends requests like this.
	 */

	if ((req.uripath = strip_hostname(req.uri)) == NULL) {
		/*
		 * A request like "GET http://example.com" will leave uripath empty.
		 */
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "received a request with invalid URI: %s",
			   req.uri);
		return REQ_ABORTED;
	}
	
	if (thumb_url_from_path(&req) == -1)
		return REQ_ABORTED;
	
	pblock_nvinsert("x-wikimedia-thumb", req.thumburl->str, rq->srvhdrs);
	
	/*
	 * Fetch the image.
	 */
	if ((req.curl = curl_easy_init()) == NULL) {
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "failed to initialise libcURL");
		send_error(&req, PROTOCOL_SERVER_ERROR, "Error initialising cURL");
		free_request(&req);
		return REQ_PROCEED;
	}

	if (prepare_curl(&req) == -1) {
		send_error(&req, PROTOCOL_SERVER_ERROR, "Error preparing cURL");
		free_request(&req);
		return REQ_PROCEED;
	}
	
	curl_easy_setopt(req.curl, CURLOPT_WRITEFUNCTION, write_data);
	curl_easy_setopt(req.curl, CURLOPT_WRITEDATA, (void *)&req);
	curl_easy_setopt(req.curl, CURLOPT_ERRORBUFFER, curlerror);
	
	if (curl_easy_perform(req.curl) != 0) {
		/*
		 * Some kind of cURL error occurred.
		 */
		send_error(&req, PROTOCOL_SERVER_ERROR, 
			    "Error retrieving thumbnail from scaling servers: %s",
			    curlerror);
		
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "cURL failed to fetch image [url=%s]: %s",
			   req.thumburl->str, curlerror);
		free_request(&req);
		return REQ_PROCEED;
	}
	
	free_request(&req);
	return REQ_PROCEED;
}

int
start_output(req, data, sz)
	request_t *req;
	void *data;
	size_t sz;
{
long	 httpcode;
char	*contenttype;

	curl_easy_getinfo(req->curl, CURLINFO_CONTENT_TYPE, &contenttype);
	curl_easy_getinfo(req->curl, CURLINFO_RESPONSE_CODE, &httpcode);

	switch (httpcode) {
	case 304:
		protocol_status(req->sn, req->rq, PROTOCOL_NOT_MODIFIED, NULL);	
		return -1;
	
	case 404:
		log_error(LOG_WARN, "thumb-handler", req->sn, req->rq,
			   "Response code was 404");
		send_error(req, PROTOCOL_NOT_FOUND, 
			   "Received 404 response from scaling server");
		return -1;
	}
	
	if (sz == 0) {
		log_error(LOG_WARN, "thumb-handler", req->sn, req->rq,
			  "Empty response from scaling server");
		send_error(req, PROTOCOL_SERVER_ERROR, 
		    "Error retrieving thumbnail from scaling servers: empty response");
		return -1;
	}
	
	if (httpcode != 200 || strncmp(contenttype, "text/html", 9) == 0 ||
	    (sz > 5 && memcmp(data, "<html", 5) == 0)) {
		protocol_status(req->sn, req->rq, PROTOCOL_SERVER_ERROR, NULL);
		pblock_nvinsert("cache-control", "nocache", req->rq->srvhdrs);
	} else {
		protocol_status(req->sn, req->rq, PROTOCOL_OK, NULL);
	}
	
	pblock_nvinsert("content-type", contenttype, req->rq->srvhdrs);
	if (protocol_start_response(req->sn, req->rq) == REQ_NOACTION)
		return -1;
	
	return 0;
}

static int
prepare_curl(req)
	request_t *req;
{
	curl_easy_setopt(req->curl, CURLOPT_NOSIGNAL, (long) 1);
	curl_easy_setopt(req->curl, CURLOPT_TIMEOUT, (long) 53);
	curl_easy_setopt(req->curl, CURLOPT_URL, req->thumburl->str);
	curl_easy_setopt(req->curl, CURLOPT_PROXY, "10.2.1.21:80");

	if (add_headers(req) == -1)
		return -1;
	
	return 0;
}

static int
add_headers(req)
	request_t *req;
{
GString			*str;
const char		**h;
char			*xff;
static char const *passthrough[] = {
	"if-modified-since",
	"referer",
	"user-agent",
	NULL
};
	if ((xff = make_xff_header(req)) == NULL)
		return -1;
	
	req->headers = curl_slist_append(req->headers, xff);
	
	str = g_string_new(NULL);
	g_string_printf(str, "X-Original-URI: %s", pblock_findval("uri", req->rq->reqpb));
	req->headers = curl_slist_append(req->headers, str->str);
	g_string_free(str, TRUE);
	
	for (h = passthrough; *h; ++h) {
	char	*s;
		if ((s = pblock_findval(*h, req->rq->headers)) != NULL) {
		GString	*hdr;
			hdr = g_string_new(NULL);
			g_string_printf(hdr, "%s: %s", *h, s);
			req->headers = curl_slist_append(req->headers, hdr->str);
			g_string_free(hdr, TRUE);
		}
	}
	
	curl_easy_setopt(req->curl, CURLOPT_HTTPHEADER, req->headers);
	return 0;
}

static char *
make_xff_header(req)
	request_t *req;
{
	/*
	 * Set an XFF header for abuse tracking.
	 */
char	*xff, *s, *r;
	if ((s = pblock_findval("x-forwarded-for", req->rq->headers)) != NULL) {
		char *raddr = pblock_findval("ip", req->sn->client);
		if (raddr == NULL) {
			log_error(LOG_FAILURE, "thumb-handler", req->sn, req->rq,
				   "could not find remote address for client");
			return NULL;
		}
		
		char *xff = CALLOC(strlen(raddr) + strlen(s) + 3);
		if (xff == NULL) {
			log_error(LOG_FAILURE, "thumb-handler", req->sn, req->rq, "out of memory");
			return NULL;
		}
		sprintf(xff, "%s, %s", s, raddr);
	} else {
		xff = pblock_findval("ip", req->sn->client);
		if (xff == NULL) {
			log_error(LOG_FAILURE, "thumb-handler", req->sn, req->rq,
				   "could not find remote address for client");
			return NULL;
		}
	}
	
	r = CALLOC(sizeof("X-Forwarded-For: ") + strlen(xff));
	if (r == NULL) {
		log_error(LOG_FAILURE, "thumb-handler", req->sn, req->rq, "out of memory");
		return NULL;
	}
	sprintf(r, "X-Forwarded-For: %s", xff);
	
	return r;
}

/*
 * Given a string like "http://example.com/a/b/c", remove
 * "http://example.com" and leave "/a/b/c".
 */
static char const *
strip_hostname(uri)
	char const *uri;
{
	if (strncmp(uri, "http://", 7) == 0)
		return strchr(uri + 7, '/');
	else
		return uri;
}

static int 
re_match(req, rex, subj, matches)
	request_t *req;
	char const *rex, *subj;
	char ***matches;
{
pcre		*re;
int		 rc, i;
int		 ovector[OVECSIZE * 3];
char const	*error;
int		 erroffset;

	re = pcre_compile(rex, 0, &error, &erroffset, NULL);
	
	if (re == NULL) {
		log_error(LOG_WARN, "thumb-handler", req->sn, req->rq,
			   "pcre_compile failed (offset=%d): %s",
			   erroffset, error);
		return -1;
	}
	
	rc = pcre_exec(re, NULL, subj, strlen(subj), 0, 0,
			ovector, OVECSIZE * 3);
	
	pcre_free(re);
	
	if (rc <= 0) {
		switch (rc) {
		case PCRE_ERROR_NOMATCH:
			return -1;
			
		default:
			log_error(LOG_WARN, "thumb-handler", req->sn, req->rq,
				   "pcre_exec failed: %d", rc);
			return rc;
		}
	}
	
	*matches = CALLOC(sizeof(char *) * rc);
	if (*matches == NULL) {
		log_error(LOG_FAILURE, "thumb-handler", req->sn, req->rq, "out of memory");
		return -1;
	}

	for (i = 0; i < rc; ++i) {
		char const *start = subj + ovector[2 * i];
		int len = ovector[2*i + 1] - ovector[2 * i];
		(*matches)[i] = CALLOC(len + 1);
		if ((*matches)[i] == NULL) {
			log_error(LOG_FAILURE, "thumb-handler", req->sn, req->rq, "out of memory");
			return -1;
		}

		memcpy((*matches)[i], start, len);
	}
	
	return rc;
}
	
static int
thumb_url_from_path(req)
	request_t *req;
{
char		*site, *lang, *width, *filename, *page;
pblock		*params;
char		**matches;
GString		*host, *paramstr;
	/*
	 * Extract the various parts from the URI.  These regexes come from
	 * thumb-handler.php.
	 */

#define THUMB_REGEXP \
	"^/([\\w-]*)/([\\w-]*)/thumb(/archive|)/\\w/\\w\\w/([^/]*)/(page(\\d*)-)*(\\d*)px-([^/]*)$"

	/*
	 * Use a pblock to store the various parameters we need to send
	 * to thumb.php.
	 */
	params = pblock_create(19);

	/* First, look for a thumbnail */
	if (re_match(req, THUMB_REGEXP, req->uripath, &matches) > 0) {
		site = matches[1];
		lang = matches[2];
		filename = matches[4];
		page = matches[6];
		width = matches[7];
	
		pblock_nvinsert("f", filename, params);
		pblock_nvinsert("width", width, params);
		if (*page)
		pblock_nvinsert("page", page, params);
		if (matches[3][0])
			pblock_nvinsert("archived", "1", params);
	
#define MEDIA_REGEXP							\
	"^/([\\w-]*)/([\\w-]*)/thumb(/archive|)/\\w/\\w\\w/"		\
	"([^/]*\\.(?:(?i)ogg|ogv|oga))/(mid|seek(?:=|%3D|%3d)(\\d+))-([^/]*)$"
	} else if (re_match(req, MEDIA_REGEXP, req->uripath, &matches) > 0) {
		site = matches[1];
		lang = matches[2];
		filename = matches[4];
		pblock_nvinsert("f", filename, params);
		if (strcmp(matches[5], "mid"))
			pblock_nvinsert("thumbtime", matches[6], params);
		if (matches[3][0])
			pblock_nvinsert("archived", "1", params);
	} else {
		pblock_nvinsert("x-debug", "No regex match", req->rq->srvhdrs);
		send_error(req, PROTOCOL_NOT_FOUND, "No regex match for filename");
		return -1;
	}

	if (strstr(filename, "%20") != NULL) {
		pblock_nvinsert("x-debug", "filename contains a space", req->rq->srvhdrs);
		send_error(req, PROTOCOL_NOT_FOUND, "Filename contains a space");
		return -1;
	}

	/*
	 * Build the URL from the params pblock.
	 */
	host = hostname_from_site_lang(site, lang);
	paramstr = pblock_to_query(params);

	req->thumburl = g_string_new(NULL);
	g_string_sprintf(req->thumburl, "http://%s/thumb.php?%s", host->str, paramstr->str);
	
	g_string_free(host, TRUE);
	g_string_free(paramstr, TRUE);

	return 0;
}

static GString *
hostname_from_site_lang(site, lang)
	char const *site, *lang;
{
GString	*host;

	host = g_string_new(NULL);
	if (strcmp(site, "wikipedia") == 0) {
		if (strcmp(lang, "meta") == 0 ||
		    strcmp(lang, "commons") == 0 ||
		    strcmp(lang, "internal") == 0 ||
		    strcmp(lang, "grants") == 0 ||
		    strcmp(lang, "wikimania2006") == 0)
		{
			g_string_printf(host, "%s.wikimedia.org", lang);
		} else if (strcmp(lang, "mediawiki") == 0) {
			g_string_assign(host, "www.mediawiki.org");
		} else if (strcmp(lang, "sources") == 0) {
			g_string_assign(host, "wikisource.org");
		} else {
			g_string_printf(host, "%s.wikipedia.org", lang);
		}
	} else {
		char *l = STRDUP(lang);
		char *p = l;
		while ((p = strchr(p, '-')) != NULL)
			*p = '.';
		g_string_printf(host, "%s.%s.org", l, site);
	}
	
	return host;
}

static GString *
pblock_to_query(pb)
	pblock const *pb;
{
GString	*query;
int	 i, first = 1;
	query = g_string_new(NULL);
	
	/* Iterate the pblock hash table */
	for (i = 0; i < pb->hsize; ++i) {
		struct pb_entry *entry;

		for (entry = pb->ht[i]; entry; entry = entry->next) {
			if (!first)
				g_string_append(query, "&");
			else
				first = 0;
			
			g_string_append_printf(query, "%s=%s",
				entry->param->name, entry->param->value);
		}
	}

	return query;
}
