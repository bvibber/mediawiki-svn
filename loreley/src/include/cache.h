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

#ifndef CACHE_H
#define CACHE_H

#include <sys/types.h>
#include <sys/stat.h>

#include <map>
#include <set>
#include <fstream>
using std::map;
using std::multiset;
using std::fstream;

#include "loreley.h"
#include "thread.h"
#include "flowio.h"
#include "http_header.h"
#include "dbwrap.h"

struct caching_filter;
struct cached_spigot;
struct cachefile;

namespace db {
	template<>
	struct marshaller<imstring> {
		pair<char const *, uint32_t> marshall(imstring const &s) {
		char	*b = new char[s.size()];
			memcpy(b, s.data(), s.size());
			return pair<char const *, uint32_t>(b, s.size());
		}

		imstring *unmarhall(pair<char const *, uint32_t> const &d) {
			return new imstring(d.first, d.second);
		}
	};
};

struct cachedir_data_store;
struct a_cachedir;

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

	time_t modified(void) const {
		return _modified;
	}

	time_t expires(void) const {
		return _expires;
	}

	bool expired(void) {
		HOLDING(_lock);

		/*
		 * Does the entity have an Expires: header?
		 */
		if (_expires)
			return _expires < time(0);

		/*
		 * Assume it's valid if the time it was last validated is
		 * less than 25% greater than its age.
		 */
		WDEBUG((format("expired: now=%d, revalidating at %d")
				% time(0) % _revalidate_at));
		if (_revalidate_at <= time(0)) {
			_complete = false;
			return true;
		}
		return false;
	}

	void revalidated(void) {
		/*
		 * If the object is still valid, its lifetime can increase.
		 */
		HOLDING(_lock);
		_lifetime = (time_t) ((time(0) - _modified) * 1.25);
		if (_lifetime < 0)
			_lifetime = 0;
		_revalidate_at = time(0) + _lifetime;
	}

	void set_complete(void);
	void store_status(imstring const &nstatus, int code) {
		_status = nstatus;
		_statuscode = code;
	}

	void store_headers(header_list const &h) {
		_headers = h;
		_headers.add("X-Cache", cache_hit_hdr);
		_headers.add("Via", via_hdr);
	}

	imstring const &status(void) const {
		return _status;
	}

	uint16_t status_code(void) const {
		return _statuscode;
	}

	time_t lastuse(void) const {
		return _lastuse;
	}

	static time_t parse_date(char const *date);

	pair<char const *, uint32_t> marshall(void) const;
	static cachedentity *unmarshall(char const *, uint32_t);

	void cachedfile(imstring const &file) {
		_cachedfile = file;
	}
	
	imstring const &cachedfile(void) const {
		return _cachedfile;
	}
	
	bool loadcachefile();
	bool savecachefile(cachefile *);

	int cachedir(void) const {
		return _cachedir;
	}

	int refs(void) {
		return _refs;
	}

private:
	friend struct httpcache;
	friend struct caching_filter;
	friend struct cached_spigot;
	friend struct cachedir_data_store;

	cachedentity(imstring const &url, size_t hint = 0);
	void _append(char const *data, size_t size);

	void ref(void) {
		++_refs;
	}

	void deref(void) {
		--_refs;
	}

	/*
	 * Remember to add marshallers when adding new data here.
	 */
	lockable	 _lock;
	imstring	 _url;
	imstring	 _status;
	uint16_t	 _statuscode;
	imstring	 _cachedfile;
	diobuf		 _data;
	atomic<int>	 _refs;
	atomic<bool>	 _complete;
	header_list	 _headers;
	char		*_builthdrs;
	int		 _builtsz;
	bool		 _void;
	time_t		 _lastuse;
	time_t		 _expires, _modified, _lifetime, _revalidate_at;
	int		 _cachedir;
	uint64_t	 _cachefile;
};

/*
 * Represents a single cached file in the filesystem.
 */
struct cachefile : noncopyable {
	/*
	 * True if the file was opened successfully.
	 */
	bool	okay(void) {
		return _file.is_open();
	}
	
	/*
	 * Remove this cached file from disk.
	 */
	bool	remove(void) {
		return std::remove(_path.c_str()) == 0;
	}
	
	/*
	 * Return the path of this file on disk.
	 */
	imstring	filename(void) const {
		return _path;
	}
	
	/*
	 * Return the size of this cached file.
	 */
	size_t size(void) const {
		return _size;
	}
	
	/*
	 * Write data to this cachefile.
	 */
	void	write(char const *buf, size_t len);
	
	/*
	 * Return the on-disk file.
	 */
	fstream &file(void) {
		return _file;
	}
	
	fstream const &file(void) const {
		return _file;
	}
	
	~cachefile(void) {
	}
	
	uint64_t filenum(void) const {
		return _num;
	}
	
	int dirnum(void) const {
		return _dnum;
	}
	
private:
	friend struct cachedir_data_store;
	friend struct a_cachedir;	
	cachefile(imstring path, int dnum, uint64_t num, bool create)
	: _path(path)
	, _size(0)
	, _num(num)
	, _dnum(dnum)
	{
	struct stat	sb;
		if (create) {
			_file.open(path.c_str(), ios::out | ios::binary | ios::trunc);
			if (!_file)
				wlog.warn(format("creating cache file %s: %s")
						% path % strerror(errno));
		}else {
			if (stat(path.c_str(), &sb) == -1) {
				wlog.warn(format("cache file %s: %s")
						% path % strerror(errno));
					return;
			}
			_size = sb.st_size;
			_file.open(path.c_str(), ios::in | ios::binary);
		}
	}
	
	fstream		_file;
	imstring	_path;
	size_t		_size;
	uint64_t	_num;
	int		_dnum;
};

/*
 * A single cache directory.
 */
struct a_cachedir : noncopyable {
	a_cachedir(imstring const &, int);
	
	/*
	 * Return a cachefile opened for output, referring to a new file, or
	 * NULL if opening failed.
	 */
	cachefile	*nextfile(void);
		
	/*
	 * Locate an existing file and return it opened for reading, or NULL
	 * if opening failed.
	 */
	cachefile	*open(uint64_t num);
	
private:
	imstring		_path;		/* root of this cachedir on disk	*/
	atomic<uint64_t>	_curfnum;	/* next file number to use		*/
	int			_dnum;
};

/*
 * Represents an overview of all cache dirs, and abstracts the creation
 * of new cached files.
 */
struct cachedir_data_store : noncopyable {
	cachedir_data_store();
	
	/*
	 * Store an entity's contents in the cachedir and return the serialised
	 * entity.
	 */
	pair<char const *, uint32_t> store(cachedentity &o);
	
	/*
	 * Delete a cached entity from disk.
	 */
	void unstore(cachedentity const &o) {}
	
	/*
	 * Retrieve the cached data from the cachedir and return an entity
	 * referring to it.
	 */
	cachedentity *retrieve(pair<char const *, uint32_t> const &d);

	/*
	 * Create a new file in the next available cache directory.
	 */
	cachefile *nextfile(void);
	
	/*
	 * Open the file given by this filenumber.
	 */
	cachefile *open(int, uint64_t);
	
private:
	uint64_t		 _curdir;
	vector<a_cachedir *>	 _cachedirs;
	lockable		 _lock;
};

struct httpcache {
	httpcache();
	~httpcache();

	bool open(void);
	void close(void);
	bool create(void);

	shared_ptr<cachedentity>
			 find_cached(imstring const &url, bool create, bool& wasnew);
	void		 release(shared_ptr<cachedentity>);
	bool		 purge(imstring const &url);
	void		 purge(shared_ptr<cachedentity>);
	bool		 cached(imstring const &url);

	/*
	 * Return the on-disk cached file represented by this file number.
	 */
	cachefile	 *get_cachefile(int dnum, uint64_t fnum);

private:
	friend struct cachedentity;
	friend struct caching_filter;

	typedef map<imstring, shared_ptr<cachedentity> > entmap;
	struct lru_comparator {
		bool operator() (entmap::iterator a,
				 entmap::iterator b) const {
			return a->second->lastuse() < b->second->lastuse();
		}
	};

	typedef multiset<entmap::iterator, lru_comparator> lruset;

	void		 _remove(shared_ptr<cachedentity> ent);
	void		 _remove_unlocked(shared_ptr<cachedentity> ent);
	void		 _swap_out(cachedentity *);
	shared_ptr<cachedentity>	
			 _swap_in(imstring const &url);

	entmap			 _entities;
	lruset			 _lru;
	lockable		 _lock, _memlock;
	size_t			 _cache_mem;
	cachedir_data_store	*_store;
	
	db::environment	*_env;
	db::database<imstring, cachedentity, cachedir_data_store> *_db;

	void	cache_mem_reduce(size_t);
	bool	cache_mem_increase(size_t, cachedentity *);
};

struct caching_filter : io::sink, io::spigot {
	caching_filter(shared_ptr<cachedentity> ent)
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
	shared_ptr<cachedentity>	_entity;
};

struct cached_spigot : io::spigot {
	cached_spigot(shared_ptr<cachedentity> ent)
		: _ent(ent)
		, _done(false)
		, _keepalive(false)
		, _doneheaders(false)
		, _corked(true)
		, _inited(false)
		, _off(0) {}

	~cached_spigot() {
		sp_cork();
	}

	void	keepalive(bool ke) {
		_keepalive = ke;
	}

	void sp_cork(void) {
		_corked = true;
	}

	void sp_uncork(void) {
		_dio = _sp_sink->dio_supported(io::dio_source_fd)
			&& _ent->_data.fd() != -1;

		_corked = false;
		if (!_inited) {
			push_headers();
			_inited = true;
		}

		if (!_doneheaders) {
			while (!_corked && _buf.items.size()) {
			wnet::buffer_item	&b = *_buf.items.begin();
			ssize_t			 discard = 0;
			io::sink_result		 res;
				res = _sp_sink->data_ready(b.buf + b.off, b.len, discard);
				if ((size_t)discard == b.len) {
					_buf.items.pop_front();
				} else {
					b.len -= discard;
					b.off += discard;
				}

				switch (res) {
				case io::sink_result_finished:
					_sp_completed_callee();
					return;
				case io::sink_result_okay:
					continue;
				case io::sink_result_error:
					_sp_error_callee();
					return;
				case io::sink_result_blocked:
					return;
				}
			}
			_doneheaders = true;
			_off = 0;
		}

	ssize_t		disc = 0;
	io::sink_result	res;
		for (;;) {
			WDEBUG(format("cached_spigot: %d left, off %d fd %d")
					% (_ent->_data.size() - _off) 
					% _off % _ent->_data.fd());
			if (_dio) {		
				res = _sp_dio_ready(_ent->_data.fd(), _off, 
					_ent->_data.size() - _off, disc);
			} else {
				res = _sp_data_ready(_ent->_data.ptr() + _off, 
					_ent->_data.size() - _off, disc);
			}
			_off += disc;
			switch (res) {
			case io::sink_result_finished:
				_sp_completed_callee();
				WDEBUG("all finished");
				return;
			case io::sink_result_okay:
				if (_off == _ent->_data.size()) {
					_sp_completed_callee();
					return;
				}
				WDEBUG("continuing");
				continue;
			case io::sink_result_error:
				WDEBUG(format("error %s") % strerror(errno));
				_sp_error_callee();
				return;
			case io::sink_result_blocked:
				sp_cork();
				WDEBUG("blocked");
				return;
			}
		}
	}

	void	push_headers(void) {
		_buf.add(_ent->_status.data(), _ent->_status.size(), false);
		_buf.add(_ent->_builthdrs, _ent->_builtsz, false);
	static char const ke_header[] = "Keep-Alive: 300\r\n";
		if (_keepalive)
			_buf.add(ke_header, sizeof(ke_header) - 1, false);
		_buf.add("\r\n", 2, false);
	}

private:
	shared_ptr<cachedentity>
			 _ent;
	bool		 _done;
	bool		 _keepalive;
	bool		 _doneheaders;
	bool		 _dio;
	wnet::buffer	 _buf;
	bool		 _corked;
	bool		 _inited;
	size_t		 _off;
};

namespace db {

template<>
struct marshaller<cachedentity> {
	pair<char const *, uint32_t> marshall(cachedentity const &e) {
		return e.marshall();
	}

	cachedentity *unmarshall(pair<char const *, uint32_t> const &d) {
		return cachedentity::unmarshall(d.first, d.second);
	}
};

} // namespace db

extern httpcache entitycache;

#endif
