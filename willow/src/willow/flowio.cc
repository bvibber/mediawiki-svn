/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * flowio: stream-based i/o system.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif
#include <sys/stat.h>
#include <sys/types.h>

#include <iostream>
#include <cerrno>
using std::streamsize;

#include "flowio.h"
#include "wnet.h"

namespace io {

sink_result
spigot::_sp_data_ready(char *b, size_t s, ssize_t &discard) {
	return _sp_sink->data_ready(b, s, discard);
}

sink_result
spigot::_sp_data_empty(void) {
	return _sp_sink->data_empty();
}

void
spigot::sp_connect(sink *s) {
	_sp_sink = s;
	s->_sink_spigot = this;
}

void
spigot::sp_disconnect(void) {
	if (_sp_sink)
		_sp_sink->_sink_spigot = NULL;
	_sp_sink = NULL;
}

void
fde_spigot::_fdecall(fde *e, int) {
ssize_t	 read;
sink_result	res;
	WDEBUG((WLOG_DEBUG, "fde_spigot::_fdecall_impl, fd=%d saved=%d, _off=%d [%.*s]", 
		e->fde_fd, _saved, _off, _saved - _off, _savebuf + _off));

	if (_off) {
		memmove(_savebuf, _savebuf + _off, _saved - _off);
		_saved -= _off;
		_off = 0;
	}
	WDEBUG((WLOG_DEBUG, "now saved=%d, _off=%d [%.*s]", _saved, _off, _saved - _off, _savebuf + _off));

	if (_saved) {
		switch (this->_sp_data_ready(_savebuf, _saved, _off)) {
		case sink_result_blocked:
			sp_cork();
			return;
		case sink_result_okay:
			break;
		case sink_result_error:
			_sp_error_callee();
			return;
		case sink_result_finished:
			_sp_completed_callee();
			return;
		}
	}

	if (_off >= _saved)
		_off = _saved = 0;

	read = ::read(e->fde_fd, _savebuf, sizeof(_savebuf));
	WDEBUG((WLOG_DEBUG, "read %d", read));
	if (read == 0) {
		sp_cork();
		switch (this->_sp_data_empty()) {
		case sink_result_blocked:
			sp_cork();
			return;
		case sink_result_okay:
			return;
		case sink_result_error:
			_sp_error_callee();
			return;
		case sink_result_finished:
			_sp_completed_callee();
			return;
		}
	}

	if (read == -1 && errno == EAGAIN) {
		WDEBUG((WLOG_DEBUG, "fde_spigot read -1, EAGAIN"));
		ioloop->readback(_fde->fde_fd, polycaller<fde *, int>(*this, &fde_spigot::_fdecall), 0);
		return;
	}

	if (read == -1) {
		WDEBUG((WLOG_DEBUG, "fde_spigot read -1; error = %s", strerror(errno)));
		_sp_error_callee();
		return;
	}

	_saved = read;
	switch (this->_sp_data_ready(_savebuf, _saved, _off)) {
	case sink_result_blocked:
	case sink_result_okay:
		break;
	case sink_result_error:
		_sp_error_callee();
		return;
	case sink_result_finished:
		_sp_completed_callee();
		return;
	}
	ioloop->readback(_fde->fde_fd, polycaller<fde *, int>(*this, &fde_spigot::_fdecall), 0);
	WDEBUG((WLOG_DEBUG, "fde_spigot::_fdecall_impl: saving %d", _saved));
	return;
}

sink_result
fde_sink::data_ready(char const *buf, size_t len, ssize_t &discard)
{
ssize_t	wrote;
	switch (wrote = write(_fde->fde_fd, buf, len)) {
	case -1:
		if (errno == EAGAIN) {
			WDEBUG((WLOG_DEBUG, "fde_sink::data_ready: socket blocked"));
			_sink_spigot->sp_cork();
			if (!_reg) {
				ioloop->writeback(_fde->fde_fd, polycaller<fde *, int>(*this, &fde_sink::_fdecall), 0);
				_reg = true;
			}
			return sink_result_blocked;
		}
		_sink_spigot->sp_cork();
		return sink_result_error;
		break;
	}
	WDEBUG((WLOG_DEBUG, "fde_sink::data_ready: got %lu, wrote %lu", len, wrote));
	discard += wrote;
	return sink_result_okay;
}

tss<file_spigot::cache_map> file_spigot::_cache;

file_spigot::file_spigot(void)
	: _corked(false)
	, _cached(false)
	, _cached_size(0)
{
}

file_spigot *
file_spigot::from_path(string const &path, bool cache)
{
file_spigot	*s = new file_spigot;
	if (!s->open(path.c_str(), cache)) {
		delete s;
		return NULL;
	}
	return s;
}

file_spigot *
file_spigot::from_path(char const *path, bool cache)
{
file_spigot	*s = new file_spigot;
	if (!s->open(path, cache)) {
		delete s;
		return NULL;
	}
	return s;
}

bool
file_spigot::open(char const *file, bool cache)
{
cache_map::iterator	it;
cache_item		item;
struct stat		sb;

	if (_cache == NULL)
		_cache = new cache_map;

	if (cache) {
		if (stat(file, &sb) == -1) {
			wlog(WLOG_WARNING, "cannot open %s: %s", file, strerror(errno));
			return false;
		}

		if ((it = _cache->find(file)) != _cache->end()) {
			if (sb.st_mtime == it->second.mtime) {
				_cached = true;
				_cdata = it->second.data;
				_cached_size = it->second.len;
				return true;
			}
			delete[] it->second.data;
			_cache->erase(it);
		}


		_file.open(file);
		if (!_file.is_open()) {
			wlog(WLOG_WARNING, "cannot open %s: %s", file, strerror(errno));
			return false;
		}

		item.mtime = sb.st_mtime;
		item.data = new char[sb.st_size];
		item.len = sb.st_size;
		if (_file.readsome(item.data, sb.st_size) != sb.st_size) {
			delete[] item.data;
			_file.seekg(0);
			return true;
		}
		(*_cache)[file] = item;
		_cached = true;
		_cached_size = sb.st_size;
		_cdata = item.data;
		return true;
	}

	_file.open(file);
	if (!_file.is_open())
		wlog(WLOG_WARNING, "cannot open %s: %s", file, strerror(errno));
	return _file.is_open();
}

bool
file_spigot::bs_get_data(void)
{
streamsize	size;
	if (_cached) {
		WDEBUG((WLOG_DEBUG, "file_spigot: cached=%d", _cached_size));

		if (!_cached_size) {
			_sp_completed_callee();
			return false;
		}
		WDEBUG((WLOG_DEBUG, "file_spigot: %d of cached data", _cached_size));

		_buf.add(_cdata, _cached_size, false);
		_cached_size = 0;
		return true;
	}

	size = _file.readsome(_fbuf, 16384);
	WDEBUG((WLOG_DEBUG, "file_spigot: read %d from file", (int)size));
	if (size == 0) {
		_sp_completed_callee();
		return false;
	} else if (_file.fail()) {
		_sp_error_callee();
		return false;
	}
	_buf.add(_fbuf, size, false);
	return true;
}

} // namespace io
