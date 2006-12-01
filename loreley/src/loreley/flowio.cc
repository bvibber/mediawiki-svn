/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* flowio: stream-based i/o system.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include <sys/stat.h>
#include <sys/types.h>
#include <sys/fcntl.h>
#include <sys/mman.h>

#include <fcntl.h>
#include <limits.h>
#include <stdio.h>
#include <iostream>
#include <cerrno>
using std::streamsize;

#include "flowio.h"
#include "net.h"
#include "format.h"
#include "config.h"

namespace io {

sink_result
spigot::_sp_data_ready(char *b, size_t s, ssize_t &discard) {
	return _sp_sink->data_ready(b, s, discard);
}

sink_result
spigot::_sp_dio_ready(int fd, off_t off, size_t s, ssize_t &discard) {
	return _sp_sink->dio_ready(fd, off, s, discard);
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
	if (_sp_sink) {
		_sp_sink->_sink_disconnected();
		_sp_sink->_sink_spigot = NULL;
	}
	_sp_sink = NULL;
}

tss<diocache> socket_spigot::_diocache[2];

char *
socket_spigot::_get_dio_buf(bool smallbuf)
{
char	 path[PATH_MAX + 1];
char	*ret;
int	 sz = smallbuf ? DIOBUFSZ_SMALL : DIOBUFSZ_LARGE;
int	 n = smallbuf;

	if (!config.use_dio)
		return new char[sz];

	if (_diocache) {
	diocache	*d = _diocache[n];
		_diocache[n] = d->next;
		_diofd = d->fd;
		ret = d->addr;
		delete d;
		return ret;
	}

	snprintf(path, sizeof(path), "/dev/shm/loreley.diobuf.%d.%d.%d",
		getpid(), (int) pthread_self(), rand());
	if ((_diofd = open(path, O_CREAT | O_EXCL | O_RDWR, 0600)) == -1) {
		wlog.warn(format("opening diobuf %s: %s") 
			% path % strerror(errno));
		return new char[sz];
	}
	unlink(path);

	if (lseek(_diofd, sz, SEEK_SET) == -1) {
		wlog.warn(format("seeking diobuf %s: %s") 
			% path % strerror(errno));
		close(_diofd);
		_diofd = -1;
		return new char[sz];
	}
	if (write(_diofd, "", 1) < 1) {
		wlog.warn(format("extending diobuf %s: %s") 
			% path % strerror(errno));
		close(_diofd);
		_diofd = -1;
		return new char[sz];
	}
	ret = (char *)mmap(0, sz, PROT_READ | PROT_WRITE, MAP_SHARED, _diofd, 0);
	if (ret == MAP_FAILED) {
		wlog.warn(format("mapping diobuf %s: %s") 
			% path % strerror(errno));
		close(_diofd);
		_diofd = -1;
		return new char[sz];
	}
	return ret;
}

void
socket_spigot::_socketcall(wsocket *s, int flags) {
ssize_t		read;
int		sz = _smallbuf ? DIOBUFSZ_SMALL : DIOBUFSZ_LARGE;
	/*
	 * _off is the offset of the start of _savebuf
	 * _saved is the number of bytes past _off that are usable.
	 */

	/* _off was increased by the previous send, reduce _saved
	 * appropriately
	 */
	if (_off >= (ssize_t)_saved)
		_saved = _off = 0;
	else
		_saved -= _off;

	if (_saved) {
		switch (this->_maybe_dio_send(_off, _savebuf, _saved, _off)) {
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

	if (_off >= (ssize_t)_saved)
		_off = _saved = 0;

	read = s->read(_savebuf + _off + _saved, sz - (_off + _saved));
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
		_socket->readback(bind(&socket_spigot::_socketcall, this, _1, _2), -1);
		return;
	}

	if (read == -1) {
		_sp_error_callee();
		return;
	}

	_saved = read;
	switch (this->_maybe_dio_send(0, _savebuf, _saved, _off)) {
	case sink_result_blocked:
		sp_cork();
	case sink_result_okay:
		break;
	case sink_result_error:
		_sp_error_callee();
		return;
	case sink_result_finished:
		_sp_completed_callee();
		return;
	}
	if (!_corked)
		_socket->readback(bind(&socket_spigot::_socketcall, this, _1, _2), -1);
	return;
}

sink_result
socket_sink::data_ready(char const *buf, size_t len, ssize_t &discard)
{
ssize_t	wrote;
	if ((wrote = _socket->write(buf, len)) == -1) {
		if (errno == EAGAIN) {
			_sink_spigot->sp_cork();
			if (!_reg) {
				_socket->writeback(bind(
					&socket_sink::_socketcall, this, _1, _2), -1);
				_reg = true;
			}
			return sink_result_blocked;
		}
		_sink_spigot->sp_cork();
		return sink_result_error;
	}

	discard += wrote;
	_counter += wrote;

	if ((ssize_t)len == wrote) {
		return sink_result_okay;
	} else {
		_sink_spigot->sp_cork();
		if (!_reg) {
			_socket->writeback(bind(&socket_sink::_socketcall, this, _1, _2), -1);
			_reg = true;
		}
		return sink_result_blocked;
	}
}

sink_result
socket_sink::dio_ready(int fd, off_t off, size_t len, ssize_t &discard)
{
ssize_t	wrote;
	WDEBUG(format("dio_ready: starting off %d") % off);
	if ((wrote = _socket->sendfile(fd, &off, len)) == -1) {
		if (errno == EAGAIN) {
			_sink_spigot->sp_cork();
			if (!_reg) {
				_socket->writeback(bind(&socket_sink::_socketcall, this, _1, _2), -1);
				_reg = true;
			}
			return sink_result_blocked;
		}
		_sink_spigot->sp_cork();
		return sink_result_error;
	}
	discard += wrote;
	_counter += wrote;

	WDEBUG(format("dio_ready: len %d off %d wrote %d fileoff=%d")
		% len % off % wrote % lseek(fd, 0, SEEK_CUR));

	if ((ssize_t)len == wrote) {
		return sink_result_okay;
	} else {
		_sink_spigot->sp_cork();
		if (!_reg) {
			_socket->writeback(bind(&socket_sink::_socketcall, this, _1, _2), -1);
			_reg = true;
		}
		return sink_result_blocked;
	}
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
			wlog.warn(format("cannot open %s: %s") 
				% file % strerror(errno));
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
			wlog.warn(format("cannot open %s: %s") 
				% file % strerror(errno));
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
		wlog.warn(format("cannot open %s: %s") % file % strerror(errno));
	return _file.is_open();
}

bool
file_spigot::bs_get_data(void)
{
streamsize	size;
	if (_cached) {
		if (!_cached_size) {
			_sp_data_empty();
			_sp_completed_callee();
			return false;
		}

		_buf.add(_cdata, _cached_size, false);
		_cached_size = 0;
		return true;
	}

	size = _file.readsome(_fbuf, 16384);
	if (size == 0) {
		_sp_data_empty();
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
