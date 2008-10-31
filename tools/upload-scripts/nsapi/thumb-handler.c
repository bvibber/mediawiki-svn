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

struct call_context {
	Session	*sn;
	Request	*rq;
	char *data;
	size_t sz;
};

static char *strip_hostname(char *uri);
static int re_match(Session *, Request *, char const *re, char const *subj, char ***output);
static int prepare_curl(Session *, Request *, CURL *, GString *requrl, struct curl_slist **);
static int add_headers(Session *, Request *rq, CURL *curl, struct curl_slist **);
static char *make_xff_header(Session *, Request *rq);
static GString *pblock_to_query(pblock const *);
static GString *hostname_from_site_lang(char const *site, char const *lang);
static GString *thumb_url_from_path(Session *sn, Request *rq, char const *path);
static size_t write_data(void *ptr, size_t, size_t, void *);
static void send_error(Session *sn, Request *rq, int code, char const *fmt, ...);

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
size_t	sz = size * num;
struct call_context *ctx = (struct call_context *)data;
	ctx->data = realloc(ctx->data, ctx->sz + sz);
	if (ctx->data == NULL) {
		log_error(LOG_FAILURE, "thumb-handler", ctx->sn, ctx->rq, "out of memory");
		return 0;
	}
	memcpy(ctx->data + ctx->sz, ptr, sz);
	ctx->sz += sz;
	return sz;
}

/*
 * Handle initialisation on server startup.
 */
int thumb_handler_init(pblock *pb UNUSED, Session *sn, Request *rq)
{
	if (curl_global_init(CURL_GLOBAL_NOTHING) != 0)
		return REQ_ABORTED;
	log_error(LOG_INFORM, "thumb-handler", sn, rq, "thumb-handler $Id$ ready");
	return REQ_PROCEED;
}

static void
send_error(
	Session *sn,
	Request *rq,
	int code,
	char const *fmt,
	...)
{
va_list	 ap;
GString	*err = g_string_new(NULL);
	g_string_assign(err,
"<html>\n"
"<head><title>Thumbnail error</title></head>\n"
"<body>\n");
	
	va_start(ap, fmt);
	g_string_append_vprintf(err, fmt, ap);
	va_end(ap);

	g_string_append(err, "</body>\n</html>\n");
	pblock_nvinsert("content-type", "text/html", rq->srvhdrs);
	pblock_nvinsert("cache-control", "no-cache", rq->srvhdrs);
	protocol_status(sn, rq, code, NULL);
	if (protocol_start_response(sn, rq) != REQ_NOACTION)
		net_write(sn->csd, err->str, err->len);
	g_string_free(err, TRUE);
}

/*
 * This function handles the actual request.
 */
int
thumb_handler(pblock *pb UNUSED, Session *sn, Request *rq)
{
char			*uri, *uripath;
GString			*thumburl;
CURL			*curl;
struct call_context	 context;
char			 curlerror[CURL_ERROR_SIZE];
long			 httpcode;
char			*contenttype;
struct curl_slist	*headers = NULL;

	/* Get the URI from the request */
	if ((uri = pblock_findval("uri", rq->reqpb)) == NULL) {
		/* Shouldn't happen, but just in case... */
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "received a request with a NULL URI");
		return REQ_ABORTED;
	}

	if (util_uri_is_evil(uri)) {
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "received a request with an evil URI: %s",
			   uri);
		return REQ_ABORTED;
	}
	
	/*
	 * If the URI starts with a hostname, i.e. http://example.com/...,
	 * strip it off.  Squid sends requests like this.
	 */

	if ((uripath = strip_hostname(uri)) == NULL) {
		/*
		 * A request like "GET http://example.com" will leave uripath empty.
		 */
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "received a request with invalid URI: %s",
			   uri);
		return REQ_ABORTED;
	}
	
	if ((thumburl = thumb_url_from_path(sn, rq, uripath)) == NULL)
		return REQ_ABORTED;
	
	pblock_nvinsert("x-wikimedia-thumb", thumburl->str, rq->srvhdrs);
	
	/*
	 * Fetch the image.
	 */
	curl = curl_easy_init();
	if (!curl) {
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "failed to initialise libcURL");
		send_error(sn, rq, PROTOCOL_SERVER_ERROR, "Error initialising cURL");
		curl_easy_cleanup(curl);
		g_string_free(thumburl, TRUE);
		return REQ_PROCEED;
	}

	if (prepare_curl(sn, rq, curl, thumburl, &headers) == -1) {
		send_error(sn, rq, PROTOCOL_SERVER_ERROR, "Error preparing cURL");
		curl_easy_cleanup(curl);
		g_string_free(thumburl, TRUE);
		return REQ_PROCEED;
	}
	
	memset(&context, 0, sizeof(context));
	context.sn = sn;
	context.rq = rq;
	
	curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, write_data);
	curl_easy_setopt(curl, CURLOPT_WRITEDATA, (void *)&context);
	curl_easy_setopt(curl, CURLOPT_ERRORBUFFER, curlerror);
	
	if (curl_easy_perform(curl) != 0) {
		/*
		 * Some kind of cURL error occurred.
		 */
		send_error(sn, rq, PROTOCOL_SERVER_ERROR, 
			    "Error retrieving thumbnail from scaling servers: %s",
			    curlerror);
		
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "cURL failed to fetch image [url=%s]: %s",
			   thumburl->str, curlerror);
		free(context.data);
		curl_easy_cleanup(curl);
		curl_slist_free_all(headers);
		g_string_free(thumburl, TRUE);
		return REQ_PROCEED;
	}

	g_string_free(thumburl, TRUE);

	curl_easy_getinfo(curl, CURLINFO_CONTENT_TYPE, &contenttype);
	curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &httpcode);

	curl_slist_free_all(headers);
	
	switch (httpcode) {
	case 304:
		protocol_status(sn, rq, PROTOCOL_NOT_MODIFIED, NULL);
		curl_easy_cleanup(curl);
		free(context.data);
		return REQ_PROCEED;
	
	case 404:
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			   "Response code was 404");
		send_error(sn, rq, PROTOCOL_NOT_FOUND, 
			   "Received 404 response from scaling server");
		curl_easy_cleanup(curl);
		free(context.data);
		return REQ_PROCEED;
	}
	
	if (context.sz == 0) {
		log_error(LOG_WARN, "thumb-handler", sn, rq,
			  "Empty response from scaling server");
		send_error(sn, rq, PROTOCOL_SERVER_ERROR, 
		    "Error retrieving thumbnail from scaling servers: empty response");
		curl_easy_cleanup(curl);
		free(context.data);
		return REQ_PROCEED;
	}
	
	if (httpcode != 200 || strncmp(contenttype, "text/html", 9) == 0 ||
	    (context.sz > 5 && memcmp(context.data, "<html", 5) == 0)) {
		protocol_status(sn, rq, PROTOCOL_SERVER_ERROR, NULL);
		pblock_nvinsert("cache-control", "nocache", rq->srvhdrs);
	} else {
		protocol_status(sn, rq, PROTOCOL_OK, NULL);
	}
	
	pblock_nvinsert("content-type", contenttype, rq->srvhdrs);
	if (protocol_start_response(sn, rq) != REQ_NOACTION)
		net_write(sn->csd, context.data, context.sz);
	curl_easy_cleanup(curl);
	free(context.data);
	
	return REQ_PROCEED;
}

static int
prepare_curl(sn, rq, curl, requrl, headers)
	Session *sn;
	Request *rq;
	CURL *curl;
	GString *requrl;
	struct curl_slist **headers;
{
	curl_easy_setopt(curl, CURLOPT_NOSIGNAL, (long) 1);
	curl_easy_setopt(curl, CURLOPT_TIMEOUT, (long) 53);
	curl_easy_setopt(curl, CURLOPT_URL, requrl->str);
	curl_easy_setopt(curl, CURLOPT_PROXY, "10.2.1.21:80");

	if (add_headers(sn, rq, curl, headers) == -1)
		return -1;
	
	return 0;
}

static int
add_headers(sn, rq, curl, headers)
	Session *sn;
	Request *rq;
	CURL *curl;
	struct curl_slist **headers;
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
	if ((xff = make_xff_header(sn, rq)) == NULL)
		return -1;
	
	*headers = curl_slist_append(*headers, xff);
	
	str = g_string_new(NULL);
	g_string_printf(str, "X-Original-URI: %s", pblock_findval("uri", rq->reqpb));
	*headers = curl_slist_append(*headers, str->str);
	g_string_free(str, TRUE);
	
	for (h = passthrough; *h; ++h) {
	char	*s;
		if ((s = pblock_findval(*h, rq->headers)) != NULL) {
		GString	*hdr;
			hdr = g_string_new(NULL);
			g_string_printf(hdr, "%s: %s", *h, s);
			*headers = curl_slist_append(*headers, hdr->str);
			g_string_free(hdr, TRUE);
		}
	}
	
	curl_easy_setopt(curl, CURLOPT_HTTPHEADER, *headers);
	return 0;
}

static char *
make_xff_header(sn, rq)
	Session *sn;
	Request *rq;
{
	/*
	 * Set an XFF header for abuse tracking.
	 */
char	*xff, *s, *r;
	if ((s = pblock_findval("x-forwarded-for", rq->headers)) != NULL) {
		char *raddr = pblock_findval("ip", sn->client);
		if (raddr == NULL) {
			log_error(LOG_FAILURE, "thumb-handler", sn, rq,
				   "could not find remote address for client");
			return NULL;
		}
		
		char *xff = CALLOC(strlen(raddr) + strlen(s) + 3);
		if (xff == NULL) {
			log_error(LOG_FAILURE, "thumb-handler", sn, rq, "out of memory");
			return NULL;
		}
		sprintf(xff, "%s, %s", s, raddr);
	} else {
		xff = pblock_findval("ip", sn->client);
		if (xff == NULL) {
			log_error(LOG_FAILURE, "thumb-handler", sn, rq,
				   "could not find remote address for client");
			return NULL;
		}
	}
	
	r = CALLOC(sizeof("X-Forwarded-For: ") + strlen(xff));
	if (r == NULL) {
		log_error(LOG_FAILURE, "thumb-handler", sn, rq, "out of memory");
		return NULL;
	}
	sprintf(r, "X-Forwarded-For: %s", xff);
	
	return r;
}

/*
 * Given a string like "http://example.com/a/b/c", remove
 * "http://example.com" and leave "/a/b/c".
 */
static char *
strip_hostname(uri)
	char *uri;
{
	if (strncmp(uri, "http://", 7) == 0)
		return strchr(uri + 7, '/');
	else
		return uri;
}

static int 
re_match(sn, rq, rex, subj, matches)
	Session *sn;
	Request *rq;
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
		log_error(LOG_WARN, "thumb-handler", sn, rq,
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
			log_error(LOG_WARN, "thumb-handler", sn, rq,
				   "pcre_exec failed: %d", rc);
			return rc;
		}
	}
	
	*matches = CALLOC(sizeof(char *) * rc);
	if (*matches == NULL) {
		log_error(LOG_FAILURE, "thumb-handler", sn, rq, "out of memory");
		return -1;
	}

	for (i = 0; i < rc; ++i) {
		char const *start = subj + ovector[2 * i];
		int len = ovector[2*i + 1] - ovector[2 * i];
		(*matches)[i] = CALLOC(len + 1);
		if ((*matches)[i] == NULL) {
			log_error(LOG_FAILURE, "thumb-handler", sn, rq, "out of memory");
			return -1;
		}

		memcpy((*matches)[i], start, len);
	}
	
	return rc;
}
	
static GString *
thumb_url_from_path(sn, rq, path)
	Session *sn;
	Request *rq;
	char const *path;
{
char		*site, *lang, *width, *filename, *page;
pblock		*params;
char		**matches;
GString		*requrl;
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
	if (re_match(sn, rq, THUMB_REGEXP, path, &matches) > 0) {
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
	} else if (re_match(sn, rq, MEDIA_REGEXP, path, &matches) > 0) {
		site = matches[1];
		lang = matches[2];
		filename = matches[4];
		pblock_nvinsert("f", filename, params);
		if (strcmp(matches[5], "mid"))
			pblock_nvinsert("thumbtime", matches[6], params);
		if (matches[3][0])
			pblock_nvinsert("archived", "1", params);
	} else {
		pblock_nvinsert("x-debug", "No regex match", rq->srvhdrs);
		send_error(sn, rq, PROTOCOL_NOT_FOUND, "No regex match for filename");
		return NULL;
	}

	if (strstr(filename, "%20") != NULL) {
		pblock_nvinsert("x-debug", "filename contains a space", rq->srvhdrs);
		send_error(sn, rq, PROTOCOL_NOT_FOUND, "Filename contains a space");
		return NULL;
	}

	/*
	 * Build the URL from the params pblock.
	 */
	host = hostname_from_site_lang(site, lang);
	paramstr = pblock_to_query(params);

	requrl = g_string_new(NULL);
	g_string_sprintf(requrl, "http://%s/thumb.php?%s", host->str, paramstr->str);
	
	g_string_free(host, TRUE);
	g_string_free(paramstr, TRUE);

	return requrl;
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