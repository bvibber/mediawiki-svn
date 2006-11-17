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
#include <iostream>
using std::make_pair;
using std::ostream;

#include "cache.h"
#include "format.h"
#include "wconfig.h"

httpcache entitycache;

httpcache::httpcache(void)
	: _cache_mem(0)
	, _env(NULL)
	, _db(NULL)
{
}

httpcache::~httpcache(void)
{
	close();
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

	/*
	 * Maybe it's in the disk cache
	 */
	ret = _db->get(url);
	if (ret != NULL) {
		WDEBUG((WLOG_DEBUG, format("found [%s] in disk cache complete %d void %d") 
				% url % ret->complete() % ret->isvoid()));
		ret->ref();
		_lru.insert(_entities.insert(make_pair(url, ret)).first);
		wasnew = false;
		return ret;
	}
	if (_db->error()) {
		wlog(WLOG_WARNING, format("fetching cached data: %s")
			% _db->strerror());
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
	HOLDING(_lock);
	if (ent->isvoid()) {
		/* don't keep void objects around */
		ent->deref();
	}

	ent->deref();
}

void
httpcache::_remove_unlocked(cachedentity *ent)
{
map<imstring, cachedentity *>::iterator it;
	if ((it = _entities.find(ent->url())) != _entities.end()) {
		_lru.erase(it);
		_entities.erase(it);
	}
}

void
httpcache::_remove(cachedentity *ent)
{
	HOLDING(_lock);
	_remove_unlocked(ent);
}

void
httpcache::cache_mem_reduce(size_t n)
{
	HOLDING(_memlock);
	_cache_mem -= n;
}

bool
httpcache::cache_mem_increase(size_t n, cachedentity *self)
{
	for (;;) {
		{	HOLDING(_memlock);
			if ((long) (_cache_mem + n) <= config.cache_memory)
				break;
		}
	cachedentity		*ent;
		{	HOLDING(_lock);
		lruset::iterator	 it = _lru.begin();
			for (; it != _lru.end(); ++it) {
				ent = (*it)->second;
				if (ent == self)
					continue;
				ent->deref();
				break;
			}
			if (ent == self)
				return false;
		}
	}

	HOLDING(_memlock);
	_cache_mem += n;
	return true;
}

void
httpcache::_swap_out(cachedentity *ent)
{
	WDEBUG((WLOG_DEBUG, format("swapping out %s") % ent->url()));
	if (!_db)
		return;
	_db->put(ent->url(), *ent);
	if (_db->error()) {
		wlog(WLOG_WARNING, format("storing cached data: %s")
			% _db->strerror());
	}
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
		ent->deref();
	}


	return true;
}

bool
httpcache::open(void)
{
	if (config.cache_master.empty())
		return true;

	_store = new cachedir_data_store;
	
	_env = db::environment::open(config.cache_master);
	if (_env->error()) {
		wlog(WLOG_ERROR, 
			format("cannot open cache master environment \"%s\": %s")
			% config.cache_master % _env->strerror());
		delete _env;
		_env = NULL;
		return false;
	}

	_db = _env->open_database<imstring, cachedentity,
		cachedir_data_store>("objects", _store);
	if (_db->error()) {
		wlog(WLOG_ERROR, 
			format("cannot open cache master database \"%s\": %s")
			% config.cache_master % _db->strerror());
		_env->close();
		delete _env;
		delete _db;
		_env = NULL;
		_db = NULL;
		return false;
	}
	return true;
}

bool
httpcache::create(void)
{
	if (config.cache_master.empty()) {
		wlog(WLOG_ERROR, "no cache master to create");
		return false;
	}

	_store = new cachedir_data_store;
	
	if (mkdir(config.cache_master.c_str(), 0700) < 0) {
		wlog(WLOG_ERROR, format("cannot create cache master \"%s\": %e")
			% config.cache_master);
		return false;
	}
		
	_env = db::environment::create(config.cache_master);
	if (_env->error()) {
		wlog(WLOG_ERROR,
			format("cannot create cache master environmet \"%s\": %s")
			% config.cache_master % _env->strerror());
		delete _env;
		_env = NULL;
		return false;
	}

	_db = _env->create_database<imstring, cachedentity,
		cachedir_data_store>("objects", _store);
	if (_db->error()) {
		wlog(WLOG_ERROR,
			format("cannot create cache master database \"%s\": %s")
			% config.cache_master % _db->strerror());
		_env->close();
		delete _env;
		delete _db;
		_env = NULL;
		_db = NULL;
		return false;
	}
	return true;
}
	
void
httpcache::close(void)
{
	if (_db) {
		_db->close();
		_db = NULL;
	}

	if (_env) {
		_env->close();
		_env = NULL;
	}
}

void
cachefile::write(char const *buf, size_t n)
{
	_file.write(buf, n);
}

cachefile *
httpcache::get_cachefile(int dirn, uint64_t num)
{
	return _store->open(dirn, num);
}

