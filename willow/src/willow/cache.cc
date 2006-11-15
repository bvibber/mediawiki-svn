/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * cache: HTTP entity caching.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <utility>
using std::make_pair;

#include "cache.h"
#include "format.h"
#include "wconfig.h"

httpcache entitycache;

httpcache::httpcache(void)
	: _cache_mem(0)
{
}

httpcache::~httpcache(void)
{
}

cachedentity *
httpcache::find_cached(imstring const &url, bool create, bool &wasnew)
{
map<imstring, cachedentity *>::iterator it;
cachedentity *ret;

	if (!config.cache_memory)
		return NULL;

	HOLDING(_lock);
	it = _entities.find(url);

	if (it != _entities.end()) {
	ret = it->second;
		/* entity was cached */
		WDEBUG((WLOG_DEBUG, format("[%s] cached, complete=%d") % url % ret->_complete));
		ret->ref();
		wasnew = false;
		_lru.erase(it);
		it->second->reused();
		_lru.insert(it);
		return ret;
	}

	WDEBUG((WLOG_DEBUG, format("[%s] not cached") % url));
	if (!create)
		return NULL;

	/* need to create new entity */
	ret = new cachedentity(url);
	wasnew = true;
	_lru.insert(_entities.insert(make_pair(url, ret)).first);
	ret->_refs = 2; /* one for _entities and one for the caller */
	return ret;
}

void
httpcache::release(cachedentity *ent)
{
	if (ent->isvoid()) {
		/* don't keep void objects around */
		ent->deref();
	}

	ent->deref();
}

void
httpcache::_remove(cachedentity *ent)
{
	HOLDING(_lock);
map<imstring, cachedentity *>::iterator it;
	if ((it = _entities.find(ent->url())) != _entities.end()) {
		_lru.erase(it);
		_entities.erase(it);
	}
}

void
httpcache::cache_mem_reduce(size_t n)
{
	HOLDING(_memlock);
	_cache_mem -= n;
}

bool
httpcache::cache_mem_increase(size_t n)
{
	for (;;) {
		{	HOLDING(_memlock);
			if (_cache_mem + n <= config.cache_memory)
				break;
		}
	cachedentity		*ent;
		{	HOLDING(_lock);
		lruset::iterator	 it;
			if ((it = _lru.begin()) == _lru.end())
				return false;
			ent = (*it)->second;
		}
		ent->deref();
	}

	HOLDING(_memlock);
	_cache_mem += n;
	return true;
}

bool
httpcache::purge(imstring const &url)
{
map<imstring, cachedentity *>::iterator it;
cachedentity *ent;
	{	HOLDING(_lock);
		it = _entities.find(url);

		if (it == _entities.end()) {
			return false;
		}
		ent = it->second;
	}

	ent->deref();

	return true;
}

cachedentity::cachedentity(imstring const &url, size_t hint)
	: _url(url)
	, _refs(0)
	, _complete(false)
	, _builthdrs(NULL)
	, _builtsz(0)
	, _void(false)
	, _lastuse(time(0))
{
	if (hint)
		_data.reserve(hint);
}

cachedentity::~cachedentity()
{
	entitycache._remove(this);
	entitycache.cache_mem_reduce(_data.size());
	delete[] _builthdrs;
}

void
cachedentity::_append(char const *data, size_t size)
{
	if (_void)
		return;

	if (_data.size() + size > config.max_entity_size) {
		_void = true;
		return;
	}

	if (!entitycache.cache_mem_increase(size)) {
		WDEBUG((WLOG_DEBUG, "object is too large, voiding cache"));
		_void = true;
		return;
	}
	_data.insert(_data.end(), data, data + size);
}
