/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp_header: header processing implementation.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#if 0
# define WILLOW_DEBUG
#endif

#include <vector>
#include <cstring>
#include <cerrno>
using std::strlen;
using std::vector;

#include <event.h>
#include <assert.h>

#include "config.h"
#include "whttp_entity.h"
#include "whttp_header.h"
#include "wnet.h"
#include "flowio.h"
#include "wconfig.h"

using namespace wnet;

const char *request_string[] = {
	"GET ",
	"POST ",
	"HEAD ",
	"TRACE ",
	"OPTIONS ",
};

struct request_type supported_reqtypes[] = {
	{ "GET",	3,	REQTYPE_GET	},
	{ "POST",	4,	REQTYPE_POST	},
	{ "HEAD",	4,	REQTYPE_HEAD	},
	{ "TRACE",	5,	REQTYPE_TRACE	},
	{ "OPTIONS",	7,	REQTYPE_OPTIONS	},
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

header::header(string const &n, string const &v)
	: hr_name(n)
	, hr_value(v)
	, hr_next(NULL)
{
}

header_list::header_list()
	: hl_len(0)
{
	hl_hdrs.reserve(20);
}

void
header_list::add(string const &name, string const &value)
{
	hl_hdrs.push_back(header(name, value));
	hl_last = &*hl_hdrs.rbegin();
	hl_len += name.size() + value.size() + 4;
}

void
header_list::add(char const *name, size_t namelen, char const *value, size_t vallen)
{
	hl_hdrs.push_back(header(string(name, name + namelen), string(value, value + vallen)));
	hl_last = &*hl_hdrs.rbegin();
	hl_len += namelen + vallen + 4;
}

void
header_list::add(char const *name, char const *value)
{
	add(string(name), string(value));
}

void
header_list::append_last(const char *append, size_t len)
{
char const	*tmp;
char		*n;
	assert(hl_last);
	hl_last->hr_value += ", ";
	hl_last->hr_value.append(append, append + len);
	hl_len += len + 2;
}

void
header_list::remove(const char *name)
{
vector<header>::iterator	it, end;
	for (it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it) {
		if (!httpcompare(it->hr_name, name))
			continue;
		hl_len -= it->hr_name.size() + it->hr_value.size() + 4;
		hl_hdrs.erase(it);
		return;
	}
	
}

struct header *
header_list::find(const char *name)
{
vector<header>::iterator	it, end;
	for (it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it) {
		if (!httpcompare(it->hr_name, name))
			continue;
		return &*it;
	}
	return NULL;
}

char *
header_list::build(void)
{
char	*buf, *bufp;
size_t	 bufsz;
size_t	 buflen = 0;

	bufsz = hl_len + 3;
	buf = new char[bufsz];
	
	*buf = '\0';
vector<header>::iterator	it, end;
	for (it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it) {
	int	incr;
		incr = it->hr_name.size();
		memcpy(buf + buflen, it->hr_name.data(), incr);
		buflen += incr;
		memcpy(buf + buflen, ": ", 2);
		buflen += 2;
		incr = it->hr_value.size();
		memcpy(buf + buflen, it->hr_value.data(), incr);
		buflen += incr;
		memcpy(buf + buflen, "\r\n", 2);
		buflen += 2;
	}

	memcpy(buf + buflen, "\r\n", 2);

	return buf;
}

void
header_list::dump(int fd)
{
int i = 0;
	i = hl_hdrs.size();
	write(fd, &i, sizeof(i));	

	for (header *h = hl_last; h; h = h->hr_next) {
		int j, k;
		k = h->hr_name.size();
		write(fd, &k, sizeof(k));
		j = h->hr_value.size();
		write(fd, &j, sizeof(j));
		write(fd, h->hr_name.data(), k);
		write(fd, h->hr_value.data(), j);
	}
}

int
header_list::undump(int fd, off_t *len)
{
	int		 i = 0, j = 0, sz = 0;
	ssize_t		 r;
	
	*len = 0;
	if ((r = read(fd, &sz, sizeof(sz))) < 0) {
		wlog(WLOG_WARNING, "reading cache file: %s", strerror(errno));
		return -1; /* XXX */
	}
	
	*len += r;
	WDEBUG((WLOG_DEBUG, "header_undump: %d entries", sz));

	while (sz--) {
	char	*n, *v, *s;
	int	 k;
	header	*h;
		*len += read(fd, &i, sizeof(i));	
		*len += read(fd, &j, sizeof(j));
		WDEBUG((WLOG_DEBUG, "header_undump: i=%d j=%d", i, j));
		n = (char *)malloc(i + j + 2);
		i = read(fd, n, i);
		*len += i;
		s = n + i;
		*s++ = '\0';
		v = s;
		k = read(fd, s, j);
		*len += k;
		s += k;
		*s = '\0';
		add(n, v);
		free(n);
	}
	
	return 0;
}

io::sink_result
header_parser::data_ready(char const *buf, size_t len, ssize_t &discard)
{
static char const *msie = "MSIE";
	WDEBUG((WLOG_DEBUG, "header_parser: got data [%.*s]", len, buf));
char const	*rn, *value, *name, *bufp = buf;
size_t		 vlen, nlen, rnpos;
	while ((rn = find_rn(bufp, bufp + len)) != NULL) {
		WDEBUG((WLOG_DEBUG, "after find_rn: cur: [%.*s]", rn - bufp, bufp));
		for (char const *c = bufp; c < rn; ++c)
			if (*(unsigned char *)c > 0x7f || !*c)
				return io::sink_result_error;

		if (rn == bufp) {
			_sink_spigot->sp_cork();
			discard += bufp - buf + 2;
			WDEBUG((WLOG_DEBUG, "header_parser::data_ready: discarding %d up to [%s]", discard, buf + discard));
			/* request with no request is an error */
			if (!_got_reqtype)
				return io::sink_result_error;
			else
				return io::sink_result_finished;
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
			while (*s == ' ')
				s++;
			_headers.append_last(s, rnpos - (s - name));
			goto next;
		}

		if ((value = (const char *)memchr(name, ':', rnpos)) == NULL) {
			_sink_spigot->sp_cork();
			return io::sink_result_error;
		}
		nlen = value - name;
		value++;
		while (isspace(*value) && value < rn)
			value++;
		vlen = rn - value;
		if (!strncasecmp(name, "Transfer-Encoding", nlen) && !strncasecmp(value, "chunked", vlen))
			_flags.f_chunked = 1;
		else if (!strncasecmp(name, "Content-Length", nlen))
			_content_length = str10toint(value, vlen);
		else if (config.msie_hack && !strncasecmp(name, "User-Agent", nlen) &&
			 std::search(value, value + vlen, msie, msie + 4) != value + vlen) {
			WDEBUG((WLOG_DEBUG, "client is MSIE"));
			_is_msie = true;
		}

		WDEBUG((WLOG_DEBUG, "header_parser: header [%.*s] = [%.*s]", nlen, (char *)name, vlen, (char *)value));
		_headers.add(name, nlen, value, vlen);
	next:
		len -= rn - bufp + 2;
		bufp = rn + 2;
		WDEBUG((WLOG_DEBUG, "continue with bufp=[%.*s] len=%d",
			len, bufp, len));
	}
	WDEBUG((WLOG_DEBUG, "header_parser: discarding %d", bufp - buf));
	discard += bufp - buf;
	return io::sink_result_okay;
}

int
header_parser::parse_reqtype(char const *buf, char const *endp)
{
char const	*path, *vers;
size_t		 plen, vlen;
int		 httpmaj, httpmin;
	if ((path = (char const *)memchr(buf, ' ', endp - buf)) == NULL)
		return -1;
	path++;
	if ((vers = (char const *)memchr(path, ' ', endp - path)) == NULL)
		return -1;
	plen = vers - path;
	vers++;
	vlen = endp - vers;
	WDEBUG((WLOG_DEBUG, "path: [%.*s] vers: [%.*s]", plen, path, vlen, vers));
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

	_http_path.assign(path, path + plen);
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

	_response = str10toint(errcode, codelen);

	_http_path.assign(errcode, errcode + codelen);
	_http_path += " ";
	_http_path.append(errdesc, errdesc + desclen);
	WDEBUG((WLOG_DEBUG, "vers: [%.*s] errcode: [%.*s] errdesc: [%.*s]",
		errcode - buf - 1, buf, codelen, errcode, desclen, errdesc));
	return 0;
}

/*
 * Should never run out of data when reading headers.
 */ 
io::sink_result
header_parser::data_empty(void)
{
	_sink_spigot->sp_cork();
	return io::sink_result_error;
}

void
header_parser::sp_cork(void)
{
	_corked = true;
}

void
header_parser::sp_uncork(void) 
{
char	*bptr;
int	 left = _headers.hl_len;
	WDEBUG((WLOG_DEBUG, "header_parse::uncork: %d left", left));
	if (!_built) {
	char	*s;
		if (!_is_response) {
		string	req = request_string[_http_reqtype] + _http_path + " HTTP/1.1\r\n";
			s = new char[req.size()];
			memcpy(s, req.data(), req.size());
			_buf.add(s, req.size(), true);
		} else {
		string	req = "HTTP/1.1 " + _http_path + "\r\n";
			s = new char[req.size()];
			memcpy(s, req.data(), req.size());
			_buf.add(s, req.size(), true);
		}
		s = _headers.build();
		WDEBUG((WLOG_DEBUG, "built headers: [%.*s]", _headers.hl_len, s));
		_buf.add(s, _headers.hl_len, true);
		_buf.add("\r\n", 2, false);
		_built = true;
	}

	_corked = false;
	while (!_corked && _buf.items.size()) {
	wnet::buffer_item	&b = *_buf.items.begin();
	ssize_t			 discard = 0;
	io::sink_result		 res;
		WDEBUG((WLOG_DEBUG, "header_parse::uncork: %d in current buffer %p", b.len, b.buf));
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
		WDEBUG((WLOG_DEBUG, "header_spigot: writing %d", b.len));

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
