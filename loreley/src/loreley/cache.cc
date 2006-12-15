/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* cache: HTTP entity caching.						*/
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

using std::make_pair;
using std::ostream;

#include "cache.h"
#include "config.h"

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

shared_ptr<cachedentity>
httpcache::find_cached(imstring const &url, bool createnew, bool &wasnew)
{
map<imstring, shared_ptr<cachedentity> >::iterator it;
	if (!config.cache_memory)
		return shared_ptr<cachedentity>();

	HOLDING(_lock);
	it = _entities.find(url);

	if (it != _entities.end()) {
	shared_ptr<cachedentity> ret(it->second);
		/* entity was cached */
		WDEBUG(format("[%s] cached, complete=%d") 
			% url % ret->_complete);
		wasnew = false;
		_lru.erase(it);
		it->second->reused();
		_lru.insert(it);
		ret->ref();
		return ret;
	}

	/*
	 * Maybe it's in the disk cache
	 */
	if (_db) {
	cachedentity		 *e;
		e = _db->get(url);
		if (e != NULL) {
		shared_ptr<cachedentity>  ret(e);
			WDEBUG(format("found [%s] in disk cache complete %d void %d") 
					% url % e->complete() % e->isvoid());
			_lru.insert(_entities.insert(make_pair(url, ret)).first);
			wasnew = false;
			ret->ref();
			return ret;
		}
		if (_db->error() && _db->error() != DB_NOTFOUND) {
			wlog.warn(format("fetching cached data: %s")
				% _db->strerror());
		}
	}

	WDEBUG(format("[%s] not cached") % url);
	if (!createnew)
		return shared_ptr<cachedentity>();

	/* need to create new entity */
cachedentity		 *e;
	e = new cachedentity(url);
shared_ptr<cachedentity> ret(e);
	wasnew = true;
	_lru.insert(_entities.insert(make_pair(url, ret)).first);
	ret->ref();
	return ret;
}

bool
httpcache::cached(imstring const &url)
{
bool		 wasnew;
shared_ptr<cachedentity>	ent = find_cached(url, false, wasnew);
	if (ent) {
		release(ent);
		return true;
	}
	return false;
}

void
httpcache::release(shared_ptr<cachedentity> ent)
{
	HOLDING(_lock);
	if (ent->isvoid())
		_remove_unlocked(ent);
}

void
httpcache::_remove_unlocked(shared_ptr<cachedentity> ent)
{
map<imstring, shared_ptr<cachedentity> >::iterator it;
	if ((it = _entities.find(ent->url())) != _entities.end()) {
		cache_mem_reduce(it->second->_data.size());
		_lru.erase(it);
		_entities.erase(it);
	}
	ent->deref();
}

void
httpcache::_remove(shared_ptr<cachedentity> ent)
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
	shared_ptr<cachedentity> ent;
		{	HOLDING(_lock);
		lruset::iterator	 it = _lru.begin();
			for (; it != _lru.end(); ++it) {
				ent = (*it)->second;
				if (ent.get() == self)
					continue;
				_remove_unlocked(ent);
				break;
			}
			if (ent.get() == self)
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
	WDEBUG(format("swapping out %s") % ent->url());
	HOLDING(_lock);
	if (!_db)
		return;
	_db->put(ent->url(), *ent);
	if (_db->error()) {
		wlog.warn(format("storing cached data: %s")
			% _db->strerror());
	}
}

bool
httpcache::purge(imstring const &url)
{
map<imstring, shared_ptr<cachedentity> >::iterator it;
	{	HOLDING(_lock);
		it = _entities.find(url);

		if (it == _entities.end()) {
			return false;
		}

		if (_db)
			_db->del(url);
		_remove_unlocked(it->second);
	}


	return true;
}

void
httpcache::purge(shared_ptr<cachedentity> ent)
{
	HOLDING(_lock);
	_remove_unlocked(ent);
	if (_db)
		_db->del(ent->url());
}

bool
httpcache::open(void)
{
	if (config.cache_master.empty() || config.cachedirs.empty())
		return true;

	_store = new cachedir_data_store;
	
	_env = db::environment::open(config.cache_master);
	if (_env->error()) {
		wlog.error(format("cannot open cache master environment \"%s\": %s")
			% config.cache_master % _env->strerror());
		delete _env;
		_env = NULL;
		return false;
	}

	_db = _env->open_database<imstring, cachedentity,
		cachedir_data_store>("objects", _store);
	if (_db->error()) {
		wlog.error(format("cannot open cache master database \"%s\": %s")
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
		wlog.error("no cache master to create");
		return false;
	}

	_store = new cachedir_data_store;
	
	if (mkdir(config.cache_master.c_str(), 0700) < 0) {
		wlog.error(format("cannot create cache master \"%s\": %s")
			% config.cache_master % strerror(errno));
		return false;
	}
		
	_env = db::environment::create(config.cache_master);
	if (_env->error()) {
		wlog.error(format(
			"cannot create cache master environmet \"%s\": %s")
			% config.cache_master % _env->strerror());
		delete _env;
		_env = NULL;
		return false;
	}

	_db = _env->create_database<imstring, cachedentity,
		cachedir_data_store>("objects", _store);
	if (_db->error()) {
		wlog.error(format(
			"cannot create cache master database \"%s\": %s")
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

