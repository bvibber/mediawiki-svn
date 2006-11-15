/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * cache: HTTP entity caching.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#ifndef CACHE_H
#define CACHE_H

#include <map>
#include <set>
using std::map;
using std::multiset;

#include "willow.h"
#include "wthread.h"
#include "flowio.h"
#include "whttp_header.h"
#include "format.h"

struct caching_filter;
struct cached_spigot;

struct cachedentity {
	~cachedentity(void);

	imstring url(void) const {
		return _url;
	}

	bool complete(void) const {
		return _complete;
	}

	bool isvoid(void) const {
		return _void;
	}

	void reused(void) {
		_lastuse = time(0);
	}

	void set_complete(void) {
		WDEBUG((WLOG_DEBUG, format("set_complete: void=%d") % _void));
		if (_void)
			return;
		_complete = true;
	}

	void store_status(imstring const &status) {
		_status = status;
	}

	void store_headers(header_list const &h) {
		_headers = h;
		_builthdrs = _headers.build();
		_builtsz = _headers.length();
	}

	time_t lastuse(void) const {
		return _lastuse;
	}

private:
	friend struct httpcache;
	friend struct caching_filter;
	friend struct cached_spigot;

	void ref(void) {
		++_refs;
	}

	void deref(void) {
		assert(_refs);
		if (--_refs == 0)
			delete this;
	}

	cachedentity(imstring const &url, size_t hint = 0);
	void _append(char const *data, size_t size);

	imstring	 _url;
	imstring	 _status;
	vector<char>	 _data;
	atomic<int>	 _refs;
	atomic<bool>	 _complete;
	header_list	 _headers;
	char		*_builthdrs;
	int		 _builtsz;
	bool		 _void;
	time_t		 _lastuse;
};

struct httpcache {
	httpcache();
	~httpcache();

	cachedentity *find_cached(imstring const &url, bool create, bool& wasnew);
	void release(cachedentity *);
	bool purge(imstring const &url);

private:
	friend struct cachedentity;
	friend struct caching_filter;

	typedef map<imstring, cachedentity *> entmap;
	struct lru_comparator {
		bool operator() (entmap::iterator a,
				 entmap::iterator b) const {
			return a->second->lastuse() < b->second->lastuse();
		}
	};

	typedef multiset<entmap::iterator, lru_comparator> lruset;

	void _remove(cachedentity *ent);

	entmap		 _entities;
	lruset		 _lru;
	lockable	 _lock, _memlock;
	size_t		 _cache_mem;

	void	cache_mem_reduce(size_t);
	bool	cache_mem_increase(size_t);
};

struct caching_filter : io::sink, io::spigot {
	caching_filter(cachedentity *ent)
		: _entity(ent) {
	}

	void sp_cork (void) {
		_sink_spigot->sp_cork();
	}

	void sp_uncork (void) {
		_sink_spigot->sp_uncork();
	}

	io::sink_result data_ready (char const *buf, size_t s, ssize_t &d) {
	ssize_t		old = d;
	io::sink_result	ret;
		ret = _sp_sink->data_ready(buf, s, d);
		_entity->_append(buf, d - old);
		return ret;
	}

	io::sink_result data_empty (void) {
		_entity->set_complete();
		return _sp_sink->data_empty();
	}

private:
	cachedentity	*_entity;
};

struct cached_spigot : io::buffering_spigot {
	cached_spigot(cachedentity *ent)
		: _ent(ent)
		, _done(false)
		, _keepalive(false) {}

	~cached_spigot() {
		sp_cork();
	}

	void	keepalive(bool ke) {
		_keepalive = ke;
	}

	bool	bs_get_data(void) {
		if (_done) {
			_sp_completed_callee();
			return false;
		}

		_done = true;	
		_buf.add(_ent->_status.data(), _ent->_status.size(), false);
		_buf.add(_ent->_builthdrs, _ent->_builtsz, false);
	static char const ke_header[] = "Keep-Alive: 300\r\n";
		if (_keepalive)
			_buf.add(ke_header, sizeof(ke_header) - 1, false);
		_buf.add("\r\n", 2, false);
		_buf.add(&_ent->_data[0], _ent->_data.size(), false);
		return true;
	}

private:
	cachedentity	*_ent;
	bool		 _done;
	bool		 _keepalive;
};

extern httpcache entitycache;

#endif
