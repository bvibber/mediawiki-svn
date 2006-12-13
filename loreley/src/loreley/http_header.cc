/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* http_header: header processing implementation.			*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
using std::strlen;
using std::vector;
using std::sprintf;

#include <assert.h>

#include "autoconf.h"
#include "http_header.h"
#include "net.h"
#include "flowio.h"
#include "config.h"

using namespace net;

enum {
	H_IGNORE,
	H_TRANSFER_ENCODING,
	H_CONTENT_LENGTH,
	H_USER_AGENT,
	H_HOST,
	H_CONNECTION,
	H_LOCATION,
	H_X_LORELEY_BACKEND_GROUP,
	H_X_LORELEY_FOLLOW_REDIRECT
};

#if 0
#ifdef __GNUC__
# include <ext/hash_map>
typedef __gnu_cxx::hash_map<imstring,int> hmap_type;
namespace __gnu_cxx {

#define FNV_32_PRIME 0x01000193u

template<>
struct hash<imstring> {
	size_t operator() (imstring const &str) const {
	uint32_t	 hval = 0x811c9dc5u;
	unsigned char 	*q = (unsigned char *)str.c_str() + str.size() - 1,
			*s = q;	/* unsigned string */

		while (*s && (q - s < 4)) {
#ifndef __GNUC__
			hval *= FNV_32_PRIME;
#else
			hval += (hval<<1) + (hval<<4) + (hval<<7) + (hval<<8) + (hval<<24);
#endif
			hval ^= (uint32_t)std::tolower(*s--);
    		}
		return hval;
	}
};

}
#else
typedef map<imstring, int> hmap_type;
#endif

hmap_type htypemap;
#endif
vector<pair<char const *, int> > htypemap;

static struct htypent {
	char const 	*name;
	int		 n;
	size_t		 len;
} list[] = {
	{ "transfer-encoding",		H_TRANSFER_ENCODING, 0 },
	{ "content-length",		H_CONTENT_LENGTH, 0 },
	{ "user-agent",			H_USER_AGENT, 0 },
	{ "host",			H_HOST, 0 },
	{ "connection",			H_CONNECTION, 0 },
	{ "location",			H_LOCATION, 0 },
	{ "x-loreley-backend-group",	H_X_LORELEY_BACKEND_GROUP, 0 },
	{ "x-loreley-follow-redirect",	H_X_LORELEY_FOLLOW_REDIRECT, 0 },
	{ "keep-alive",			H_IGNORE, 0 },
	{ "te",				H_IGNORE, 0 },
	{ "trailers",			H_IGNORE, 0 },
	{ "upgrade",			H_IGNORE, 0 },
	{ "proxy-authenticate",		H_IGNORE, 0 },
	{ "proxy-connection",		H_IGNORE, 0 },
	{ 0, 0, 0 }
};
void
whttp_header_init(void)
{
	for (htypent *tit = list; tit->name; ++tit)
		tit->len = strlen(tit->name);
}

static inline int
find_htype(char const *s, size_t slen)
{
	for (htypent *tit = list; tit->name; ++tit)
		if (tit->len == slen && !strncasecmp(s, tit->name, slen))
			return tit->n;
	return -1;
}

#if 0
int
find_htype(char const *s, size_t slen)
{
hmap_type::iterator	it;
	if (slen >= 24)
		return -1;
	it = htypemap.find(imstring(s, slen));
	if (it == htypemap.end())
		return -1;
	return it->second;
}
#endif

const char *request_string[] = {
	"GET ",
	"POST ",
	"HEAD ",
	"TRACE ",
	"OPTIONS ",
	"TRACE ",
};

struct request_type supported_reqtypes[] = {
	{ "GET",	3,	REQTYPE_GET	},
	{ "POST",	4,	REQTYPE_POST	},
	{ "HEAD",	4,	REQTYPE_HEAD	},
	{ "TRACE",	5,	REQTYPE_TRACE	},
	{ "OPTIONS",	7,	REQTYPE_OPTIONS	},
	{ "PURGE",	5,	REQTYPE_PURGE	},
	{ NULL,		0,	REQTYPE_INVALID }
};

static int
find_reqtype(char const *str, int len)
{
	for (request_type *r = supported_reqtypes; r->name; r++)
		if (r->len == len && !memcmp(r->name, str, len))
			return r->type;
	return REQTYPE_INVALID;
}

pt_allocator<char> header::alloc;

header::header(char const *n, size_t nlen, char const *v, size_t vlen)
	: hr_allocd(0)
{
	assign(n, nlen, v, vlen);
}

header::header(header const &other)
	: hr_allocd(0)
{
	assign(other.hr_name, strlen(other.hr_name),
		other.hr_value, strlen(other.hr_value));
}

void
header::assign(char const *n, size_t nlen, char const *v, size_t vlen)
{
char	*buf = hr_buffer;
	if (hr_allocd) {
		alloc.deallocate(hr_name, hr_allocd);
		hr_allocd = false;
	}

	if ((nlen + vlen + 2) >= HDR_BUFSZ) {
		buf = alloc.allocate(nlen + vlen + 2);
		hr_allocd = nlen + vlen + 2;
	}

	hr_name = buf;
	hr_value = buf + nlen + 1;

	memcpy(hr_name, n, nlen);
	memcpy(hr_value, v, vlen);
	hr_name[nlen] = hr_value[vlen] = '\0';
}

header&
header::operator= (header const &other)
{
	assign(other.hr_name, strlen(other.hr_name),
		other.hr_value, strlen(other.hr_value));
	return *this;
}

header::~header(void)
{
	if (hr_allocd)
		alloc.deallocate(hr_name, hr_allocd);
}

void
header::move(header &other)
{
	/*
	 * The other header is static, just copy the string.
	 */
	if (!other.hr_allocd) {
		if (hr_allocd) {
			alloc.deallocate(hr_name, hr_allocd);
			hr_allocd = 0;
		}
	
		hr_name = hr_buffer;
		strcpy(hr_name, other.hr_name);
		hr_value = hr_buffer + strlen(hr_name) + 1;
		strcpy(hr_value, other.hr_value);
		return;
	}

	/*
	 * The other header is allocd, steal its buffer.
	 */
	hr_allocd = other.hr_allocd;
	hr_name = other.hr_name;
	hr_value = other.hr_value;
	other.hr_allocd = 0;
}

header_list::header_list()
	: hl_len(0)
{
	hl_hdrs.reserve(20);
}

header_list::~header_list()
{
	for (vector<header *, pt_allocator<header *> >::iterator
		it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it)
		delete *it;
}

void
header_list::add(char const *name, size_t namelen, char const *value, size_t vallen)
{
header	*h = new header(name, namelen, value, vallen);

	hl_hdrs.push_back(h);
	hl_last = h;
	hl_len += namelen + vallen + 4;
}

void
header_list::add(char const *name, char const *value)
{
	add(name, strlen(name), value, strlen(value));
}

void
header_list::append_last(const char *append, size_t len)
{
int		 curnlen, curvlen;
	curnlen = strlen(hl_last->hr_name);
	curvlen = strlen(hl_last->hr_value);

	hl_len += len + 2;

size_t	nbufsz = curnlen + curvlen + 4 + len;
	/*
	 * Simple case: not allocated, and new header fits in static buf.
	 */
	if (!hl_last->hr_allocd && nbufsz < HDR_BUFSZ) {
		strcat(hl_last->hr_value, ", ");
		strncat(hl_last->hr_value, append, len);
		return;
	}

	/*
	 * New header is too long.
	 */
	if ((!hl_last->hr_allocd && nbufsz >= HDR_BUFSZ) ||
	     (hl_last->hr_allocd && nbufsz >= hl_last->hr_allocd)) {
	char	*nbuf = hl_last->alloc.allocate(nbufsz);
		strcpy(nbuf, hl_last->hr_name);
		sprintf(nbuf + curnlen + 1, "%s, %.*s",
			hl_last->hr_value, (int) len, append);

		if (hl_last->hr_allocd)
			hl_last->alloc.deallocate(hl_last->hr_name, hl_last->hr_allocd);

		hl_last->hr_name = nbuf;
		hl_last->hr_value = nbuf + curnlen + 1;
		hl_last->hr_allocd = nbufsz;
		return;
	}

	/* should not get here */
	abort();
}

void
header_list::remove(const char *name)
{
vector<header *, pt_allocator<header *> >::iterator	it, end;
	for (it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it) {
		if (strcasecmp((*it)->hr_name, name))
			continue;
		hl_len -= strlen((*it)->hr_name) + strlen((*it)->hr_value) + 4;
		(*it)->move(**hl_hdrs.rbegin());
		hl_hdrs.pop_back();
		return;
	}
	
}

struct header *
header_list::find(const char *name)
{
vector<header *, pt_allocator<header *> >::iterator	it, end;
	for (it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it) {
		if (strcasecmp((*it)->hr_name, name))
			continue;
		return *it;
	}
	return NULL;
}

header_list&
header_list::operator= (header_list const &other)
{
	for (vector<header *, pt_allocator<header *> >::iterator
		it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it)
		delete *it;
	hl_hdrs.clear();
	for (vector<header *, pt_allocator<header *> >::const_iterator
		it = other.hl_hdrs.begin(), end = other.hl_hdrs.end(); it != end; ++it)
		hl_hdrs.push_back(new header(**it));
	hl_len = other.hl_len;
	hl_last = *hl_hdrs.rbegin();
	return *this;
}
	
char *
header_list::build(void)
{
char	*buf;
size_t	 bufsz;
size_t	 buflen = 0;

	bufsz = hl_len + 3;
	buf = new char[bufsz];
	
	*buf = '\0';
vector<header *, pt_allocator<header *> >::iterator	it, end;
	for (it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it) {
	int	incr;
		incr = strlen((*it)->hr_name);
		memcpy(buf + buflen, (*it)->hr_name, incr);
		buflen += incr;
		memcpy(buf + buflen, ": ", 2);
		buflen += 2;
		incr = strlen((*it)->hr_value);
		memcpy(buf + buflen, (*it)->hr_value, incr);
		buflen += incr;
		memcpy(buf + buflen, "\r\n", 2);
		buflen += 2;
	}

	memcpy(buf + buflen, "\r\n", 2);

	return buf;
}

io::sink_result
header_parser::data_ready(char const *buf, size_t len, ssize_t &discard)
{
static char const *msie = "MSIE";
char const	*rn, *value, *name, *bufp = buf;
size_t		 vlen, nlen, rnpos;
int		 htype;

	WDEBUG(format("header parser: got [%s]") % string(buf, buf + len));
	while ((rn = find_rn(bufp, bufp + len)) != NULL) {
		WDEBUG(format("processing: [%s]") % string(bufp, rn));
		for (char const *c = bufp; c < rn; ++c)
			if (*reinterpret_cast<unsigned char const *>(c) > 0x7f || !*c)
				return io::sink_result_error;
		WDEBUG("chars all okay");

		if (rn == bufp) {
			_sink_spigot->sp_cork();
			discard += bufp - buf + 2;
			/* request with no request is an error */
			if (!_got_reqtype)
				return io::sink_result_error;
			else {
				if (!_is_response && _http_host.empty()) {
					if (_http_vers == http11)
						return io::sink_result_error;
					else if (!config.default_host.empty()) {
						_headers.add("Host", config.default_host.c_str());
						_http_host = config.default_host;
					}
				}
				return io::sink_result_finished;
			}
		}
		rnpos = rn - bufp;
		name = bufp;

		if (!_got_reqtype) {
			if ((!_is_response && parse_reqtype(bufp, rn) == -1)
			    || (_is_response && parse_response(bufp, rn) == -1)) {
				_sink_spigot->sp_cork();
				return io::sink_result_error;
			}
			_got_reqtype = true;
			goto next;
		}

		if (*name == ' ') {
		const char	*s = name;
			/* continuation of last header */
			if (!_headers.hl_len)
				return io::sink_result_error;
			while (*s == ' ' && s < rn)
				s++;
			if (s < rn)
				_headers.append_last(s, rnpos - (s - name));
			goto next;
		}

		if ((value = (const char *)memchr(name, ':', rnpos)) == NULL) {
			_sink_spigot->sp_cork();
			return io::sink_result_error;
		}
		nlen = value - name;

		htype = find_htype(name, nlen);
		if (htype == H_IGNORE)
			goto next;

		value++;
		while (isspace(*value) && value < rn)
			value++;
		vlen = rn - value;

		switch (htype) {
		case H_TRANSFER_ENCODING:
			if (!strncasecmp(value, "chunked", vlen))
				_flags.f_chunked = 1;
			break;

		case H_CONTENT_LENGTH:
			if ((_content_length = strNtoint<10>(value, vlen)) == -1) {
				_sink_spigot->sp_cork();
				return io::sink_result_error;
			}
			break;

		case H_USER_AGENT:
			if (config.msie_hack &&
			    std::search(value, value + vlen, msie, msie + 4) != value + vlen)
				_is_msie = true;
			break;

		case H_HOST:
			_http_host.assign(value, value + vlen);
			break;

		case H_CONNECTION:
			if (!strncasecmp(value, "close", vlen))
				_no_keepalive = true;
			else if (!strncasecmp(value, "keep-alive", vlen))
				_force_keepalive = true;
			goto next;

		case H_LOCATION:
			_location.assign(value, value + vlen);
			break;

		case H_X_LORELEY_BACKEND_GROUP:
			_http_backend.assign(value, value + vlen);
			break;

		case H_X_LORELEY_FOLLOW_REDIRECT:
			_follow_redirect = true;
			break;
		}

		_headers.add(name, nlen, value, vlen);
	next:
		len -= rn - bufp + 2;
		bufp = rn + 2;
	}
	discard += bufp - buf;
	return io::sink_result_okay;
}

int
header_parser::parse_reqtype(char const *buf, char const *endp)
{
char const	*path, *vers;
size_t		 plen, vlen;
	if ((path = (char const *)memchr(buf, ' ', endp - buf)) == NULL)
		return -1;
	path++;
	if ((vers = (char const *)memchr(path, ' ', endp - path)) == NULL)
		return -1;
	plen = vers - path;
	vers++;
	vlen = endp - vers;
	if (vlen != 8)
		return -1;
	if (strncmp(vers, "HTTP/", 5))
		return -1;
	if (vers[5] != '1' || vers[6] != '.')
		return -1;
	if (vers[7] == '0')
		_http_vers = http10;
	else if (vers[7] == '1')
		_http_vers = http11;
	else	return -1;
	if ((_http_reqtype = find_reqtype(buf, path - buf - 1)) == REQTYPE_INVALID)
		return -1;

	if (plen > 7 && !strncasecmp(path, "http://", 7)) {
	char const	*host, *slash;
		host = path + 7;
		slash = (char const *)memchr(path + 7, '/', plen - 7);
		WDEBUG(format("slash: [%s]") % string(slash, plen - (slash - path)));
		if (slash == NULL)
			_http_path.assign("/");
		else
			_http_path.assign(slash, plen - (slash - path));
		_http_host.assign(host, slash - host);
	} else {
		_http_path.assign(path, path + plen);
	}
	return 0;
}

int
header_parser::parse_response(char const *buf, char const *endp)
{
char const	*errcode, *errdesc;
int		 codelen, desclen;
	if ((errcode = (char const *)memchr(buf, ' ', endp - buf)) == NULL)
		return -1;
	if (errcode - buf != 8)
		return -1;
	errcode++;
	if ((errdesc = (char const *)memchr(errcode, ' ', endp - errcode)) == NULL)
		return -1;
	codelen = errdesc - errcode;
	errdesc++;
	desclen = endp - errdesc;
	if (strncmp(buf, "HTTP/", 5))
		return -1;
	if (buf[5] != '1' || buf[6] != '.')
		return -1;
	if (buf[7] == '0')
		_http_vers = http10;
	else if (buf[7] == '1')
		_http_vers = http11;
	else	return -1;

	WDEBUG(format("parse_response: codelen=%d [%s] desclen=%d [%s]")
		% codelen % string(errcode, errcode + codelen)
		% desclen % string(errdesc, errdesc + desclen));

	if ((_response = strNtoint<10>(errcode, codelen)) == -1)
		return -1;

	_http_path.reserve(codelen + desclen + 1);
	_http_path.assign(errcode, errcode + codelen);
	_http_path.append(" ", 1);
	_http_path.append(errdesc, errdesc + desclen);
	return 0;
}

/*
 * Should never run out of data when reading headers.
 */ 
io::sink_result
header_parser::data_empty(void)
{
	_sink_spigot->sp_cork();
	if (!_got_reqtype)
		_eof = true;
	return io::sink_result_error;
}

void
header_parser::sp_cork(void)
{
	_corked = true;
}

void
header_parser::sending_restart(void)
{
	_buf.reset();
	_built = false;
}

void
header_parser::sp_uncork(void) 
{
	if (!_built) {
	char	*s;
		if (!_is_response) {
		string	req;
			req.reserve(strlen(request_string[_http_reqtype]) +
				_http_path.size() + 11);

			req = request_string[_http_reqtype];
			req += _http_path.c_str();
			req += " HTTP/1.";
			if (_http_host.size())
				req += "1\r\n";
			else	req += "0\r\n";
			s = new char[req.size()];
			memcpy(s, req.data(), req.size());
			_buf.add(s, req.size(), true);
		} else {
		string	req;
			req.reserve(_http_path.size() + 11);
			req = "HTTP/1.1 ";
			req += _http_path.c_str();
			req += "\r\n";
			s = new char[req.size()];
			memcpy(s, req.data(), req.size());
			_buf.add(s, req.size(), true);
		}
		s = _headers.build();
		_buf.add(s, _headers.hl_len, true);
		_buf.add("\r\n", 2, false);
		_built = true;
	}

	_corked = false;
	while (!_corked && _buf.items.size()) {
	net::buffer_item	&b = *_buf.items.begin();
	ssize_t			 discard = 0;
	io::sink_result		 res;
		res = _sp_sink->data_ready(b.buf + b.off, b.len - b.off, discard);
		if (discard == 0)
			return;
		if ((size_t)discard == b.len) {
			_buf.items.pop_front();
		} else {
			b.len -= discard;
			b.off += discard;
		}
		switch (res) {
		case io::sink_result_finished:
			_sp_completed_callee();
			break;
		case io::sink_result_error:
			_sp_error_callee();
			return;
		case io::sink_result_blocked:
			sp_cork();
			return;
		case io::sink_result_okay:
			continue;
		}
	}
	this->_sp_data_empty();
	_sp_completed_callee();
}
 
void
header_parser::set_response(void)
{
	_is_response = true;
}

header_spigot::header_spigot(int errcode, char const *msg)
	: _built(false)
	, _corked(true)
{
char	cstr[4];
	sprintf(cstr, "%d", errcode);
	_first = "HTTP/1.1 ";
	_first += cstr;
	_first += " ";
	_first += msg;
	_first += "\r\n";
}

void
header_spigot::add(char const *h, char const *v)
{
	_headers.add(h, v);
}

void
header_spigot::body(string const &body)
{
	_body = body;
}

void
header_spigot::body(string &body)
{
	_body.swap(body);
}

void
header_spigot::sp_uncork(void)
{
	_corked = false;

	if (!_built) {
		_buf.add(_first.data(), _first.size(), false);
		_buf.add(_headers.build(), _headers.hl_len, true);
		_buf.add("\r\n", 2, false);
		_buf.add(_body.data(), _body.size(), false);
	}

	while (!_corked && _buf.items.size()) {
	buffer_item	&b = *_buf.items.begin();
	ssize_t		 discard = 0;
	io::sink_result	 res;

		res = _sp_sink->data_ready(b.buf + b.off, b.len, discard);
		if ((size_t)discard == b.len) {
			_buf.items.pop_front();
		} else {
			b.len -= discard;
			b.off += discard;
		}
		switch (res) {
		case io::sink_result_error:
			_sp_error_callee();
			return;
		case io::sink_result_finished:
			_sp_completed_callee();
			return;
		case io::sink_result_okay:
			continue;
		case io::sink_result_blocked:
			sp_cork();
			return;
		}
	}
	if (!_corked) {
		sp_cork();
		_sp_completed_callee();
	}
}

void
header_spigot::sp_cork(void)
{
	_corked = true;
}
