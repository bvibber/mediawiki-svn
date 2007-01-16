/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* cachedentity: a single cached document.				*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
#ifdef __INTEL_COMPILER
# pragma hdrstop
#endif

using std::ostream;

#include "cache.h"
#include "mbuffer.h"
#include "config.h"

#ifndef NO_BDB

cachedentity::cachedentity(imstring const &url, size_t hint)
	: _url(url)
	, _data(hint ? hint : 4096)
	, _refs(0)
	, _complete(false)
	, _builthdrs(NULL)
	, _builtsz(0)
	, _void(false)
	, _lastuse(time(0))
	, _expires(0)
	, _modified(0)
{
	WDEBUG(format("CACHE: creating cached entity with URL %s") % url);
	assert(_url.size());
}

cachedentity::~cachedentity()
{
	delete[] _builthdrs;
}

void
cachedentity::_append(char const *data, size_t size)
{
	if (_void)
		return;

	if ((long) (_data.size() + size) > config.max_entity_size) {
		_void = true;
		return;
	}

	if (!entitycache.cache_mem_increase(size, this)) {
		WDEBUG("object is too large, voiding cache");
		_void = true;
		return;
	}
	_data.append(data, size);
}

time_t
cachedentity::parse_date(char const *date)
{
struct tm	tm;
	memset(&tm, 0, sizeof(tm));
	if (strptime(date, "%a, %d %b %Y %H:%M:%S GMT", &tm) == NULL)
		return (time_t) -1;
	return mktime(&tm);
}

void
cachedentity::set_complete(void)
{
header	*h;
	WDEBUG(format("set_complete: void=%d") % _void);
	if (_void)
		return;
	_headers.remove("transfer-encoding");
	if (!_headers.find("content-length")) {
	char	lenstr[64];
		snprintf(lenstr, sizeof lenstr, "%lu", 
			(unsigned long) _data.size());
		_headers.add("Content-Length", lenstr);
	}

	if ((h = _headers.find("Expires")) != NULL) {
		if ((_expires = parse_date(h->value())) == -1) {
			_expires = time(0);
		}
	} else {
		_expires = 0;
	}

	if ((h = _headers.find("Last-Modified")) != NULL) {
		if ((_modified = parse_date(h->value())) == -1) {
			_modified = time(0);
		}
	} else {
		if ((h = _headers.find("Date")) != NULL) {
			if ((_modified = parse_date(h->value())) == -1) {
				_modified = time(0);
			}
		} else {
			_modified = time(0);
		}
	}

	/*
	 * lifetime of 0 means the object shouldn't be cached.
	 */
	_lifetime = (time_t) ((time(0) - _modified) * 1.25);
	if (_expires == _modified || _lifetime <= 0) {
		_void = true;
		return;
	}

	WDEBUG(format("CACHE: object lifetime=%d sec.") % _lifetime);
	revalidated();
	_builthdrs = _headers.build();
	_builtsz = _headers.length();
	_data.finished();
	_complete = true;
	entitycache._swap_out(this);
}

pair<char const *, uint32_t>
cachedentity::marshall(void) const
{
marshalling_buffer	buf;
	buf.reserve(
		sizeof(size_t) + _url.size() +
		sizeof(size_t) + _status.size() +
		sizeof(size_t) + _builtsz +
		sizeof(time_t) * 5 +
		sizeof(int) +
		sizeof(uint64_t));
	buf.append<imstring>(_url);
	buf.append<imstring>(_status);
	buf.append<size_t>(_builtsz);
	buf.append_bytes(_builthdrs, _builtsz);
	buf.append<time_t>(_lastuse);
	buf.append<time_t>(_expires);
	buf.append<time_t>(_modified);
	buf.append<time_t>(_lifetime);
	buf.append<time_t>(_revalidate_at);
	buf.append<int>(_cachedir);
	buf.append<uint64_t>(_cachefile);
	buf.append<uint16_t>(_statuscode);
	return make_pair(buf.buffer(), buf.size());
}

cachedentity *
cachedentity::unmarshall(char const *d, uint32_t s)
{
cachedentity		*ret;
marshalling_buffer	 buf(d, s);
imstring		 url;
char			*hdrbuf;
size_t			 bufsz;
	if (!buf.extract<imstring>(url))
		return NULL;
	WDEBUG(format("unmarshall: URL [%s], len %d") % url % url.size());
	ret = new cachedentity(url);
	if (!buf.extract<imstring>(ret->_status)) {
		delete ret;
		return NULL;
	}

	if (!buf.extract<size_t>(bufsz)) {
		delete ret;
		return NULL;
	}

	ret->_builthdrs = new char[bufsz + 1];
	ret->_builtsz = bufsz;
	if (!buf.extract_bytes(ret->_builthdrs, bufsz)) {
		delete ret;
		return NULL;
	}

	if (!buf.extract<time_t>(ret->_lastuse)) {
		delete ret;
		return NULL;
	}

	if (!buf.extract<time_t>(ret->_expires)) {
		delete ret;
		return NULL;
	}

	if (!buf.extract<time_t>(ret->_modified)) {
		delete ret;
		return NULL;
	}

	if (!buf.extract<time_t>(ret->_lifetime)) {
		delete ret;
		return NULL;
	}

	if (!buf.extract<time_t>(ret->_revalidate_at)) {
		delete ret;
		return NULL;
	}
	
	if (!buf.extract<int>(ret->_cachedir)) {
		delete ret;
		return NULL;
	}
	
	if (!buf.extract<uint64_t>(ret->_cachefile)) {
		delete ret;
		return NULL;
	}
	
	if (!buf.extract<uint16_t>(ret->_statuscode)) {
		delete ret;
		return NULL;
	}

	/*
	 * An incomplete or void entity will never be written to the disk cache.
	 */
	ret->_complete = true;
	ret->_void = false;
	return ret;
}

bool
cachedentity::loadcachefile(void)
{
cachefile	*f;
struct stat	 sb;
	if ((f = entitycache.get_cachefile(_cachedir, _cachefile)) == NULL)
		return false;
	return _data.loadfile(f->file(), f->size());
}

bool
cachedentity::savecachefile(cachefile *f)
{
ostream	&sm = f->file();
	assert(f);
	if (!f->okay())
		return false;
	WDEBUG(format("CACHE: writing cached data to %s") % f->filename());
	if (!sm.write(_data.ptr(), _data.size())) {
		wlog.warn(format("writing cached data to %s: %s")
				% f->filename() % strerror(errno));
		return false;
	}
	_cachedir = f->dirnum();
	_cachefile = f->filenum();
	return true;
}

#endif	/* NO_BDB */
