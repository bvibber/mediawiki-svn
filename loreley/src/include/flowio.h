/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* flowio: stream-based i/o system.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef FLOWIO_H
#define FLOWIO_H

#include <sys/mman.h>

#include <fstream>
#include <string>
#include <algorithm>
#include <map>
using std::ifstream;
using std::map;
using std::min;

#include "loreley.h"
#include "net.h"
using namespace net;

namespace io {

struct sink;
struct spigot;

enum sink_result {
	sink_result_finished,
	sink_result_error,
	sink_result_okay,
	sink_result_blocked
};

/*
 * A spigot is an object which produces a bytestream.
 */
struct spigot : noncopyable {
	spigot() : _sp_sink(NULL) {}

	virtual ~spigot() {
		sp_disconnect();
	}

	/*
	 * Connect this spigot to a sink.
	 */
	void sp_connect(sink *s);
	void sp_disconnect(void);

	template<typename T>
	T &operator>> (T &s) {
		sp_connect(&s);
		return s;
	}

	/*
	 * Stop providing new data.
	 */
	virtual void sp_cork(void) = 0;

	/*
	 * Start providing new data again.
	 */
	virtual void sp_uncork(void) = 0;

	template<typename T>
	void completed_callee(T cb) {
		_sp_completed_callee = cb;
	}

	template<typename T>
	void error_callee(T cb) {
		_sp_error_callee = cb;
	}

protected:
	sink_result 	 _sp_data_ready (char *b, size_t s, ssize_t &discard);
	sink_result 	 _sp_dio_ready (int fd, off_t off, size_t s, ssize_t &discard);
	sink_result	 _sp_data_empty (void);

	function<void ()>	 _sp_completed_callee;
	function<void ()>	 _sp_error_callee;
	sink			*_sp_sink;
};

enum dio_source {
	dio_source_fd,
	dio_source_socket
};

/*
 * A sink is an object which receives bytes from a spigot.
 */
struct sink : noncopyable {
	sink() : _sink_spigot(NULL) {}

	virtual ~sink() {
		if (_sink_spigot) {
			_sink_disconnected();
			_sink_spigot->sp_disconnect();
		}
	}

	virtual sink_result	data_ready (char const*, size_t, ssize_t &) = 0;
	virtual sink_result	data_empty (void) = 0;

	/*
	 * Direct I/O.  This is used for mechanisms like sendfile()
	 * which operate on an fd instead of a buffer.  If dio_supported
	 * returns true, a spigot supporting direct i/o will call dio_ready
	 * instead of data_ready.  The sink should then read data from
	 * fd, beginning at offset.  discard and size are used as normal.
	 */
	virtual bool		dio_supported	(dio_source) const { return false; }
	virtual sink_result	dio_ready	(int, off_t, size_t, ssize_t&) { 
		abort();
		return io::sink_result_error;
	}

protected:
	friend class spigot;

	virtual void _sink_disconnected(void) {}

	spigot		*_sink_spigot;

private:
};

/*
 * A sink that sends data to a socket.
 */
struct socket_sink : freelist_allocator<socket_sink>, sink {
	socket_sink(wsocket *s)
		: _socket(s)
		, _reg(false)
		, _counter(false) {
	}
	~socket_sink() {
		_socket->clearbacks();
	}

	void _socketcall(wsocket *, int) {
		_reg = false;
		_sink_spigot->sp_uncork();
	}
	virtual void _sink_disconnected(void) {
		_socket->clearbacks();
	}

#ifdef HAVE_SYS_SENDFILE_H
	virtual bool dio_supported(dio_source s) const {
		return s == dio_source_fd;
	}
	virtual sink_result dio_ready(int, off_t, size_t, ssize_t&);
#endif

	sink_result data_ready(char const *buf, size_t len, ssize_t &discard);
	sink_result data_empty(void) {
		WDEBUG("socket_sink::data_empty");
		_reg = false;
		return sink_result_finished;
	}

	wsocket		*_socket;
	bool		 _reg;
	size_t		 _counter;
};

/*
 * A spigot that reads from a string.
 */
struct string_spigot : freelist_allocator<string_spigot>, spigot {
	string_spigot(string const &s_) 
		: _str(s_)
		, _pos(0)
		, _len(_str.size())
		, _corked(false) {
		_data = new char[_len];
		memcpy(_data, _str.data(), _len); 
	}

	~string_spigot() {
		delete[] _data;
	}

	void sp_cork() {
		_corked = true;
	}

	void sp_uncork() {
		_corked = false;
		_send_data();
	}

	void _send_data(void) {
		switch (this->_sp_data_ready(_data, _len - _pos, _pos)) {
		case sink_result_blocked:
			sp_cork();
			return;
		case sink_result_error:
			_sp_error_callee();
			return;
		case sink_result_okay:
		case sink_result_finished:
			_sp_completed_callee();
			return;
		}
	}

	string const		&_str;
	char 			*_data;
	ssize_t			 _pos;
	string::size_type	 _len;
	bool			 _corked;
};

#define DIOBUFSZ_LARGE 65535
#define DIOBUFSZ_SMALL 2048
struct diocache : freelist_allocator<diocache> {
	int	 fd;
	char	*addr;
	struct diocache *next;
};

/*
 * A spigot that reads from a socket.
 */
struct socket_spigot : freelist_allocator<socket_spigot>, spigot {
	/*
	 * Create a new corked socket_spigot.
	 * socket should not have any existing callbacks or odd
	 * things will happen.
	 */ 
	socket_spigot(wsocket *s, bool smallbuf = false)
		: _socket(s)
		, _savebuf(NULL) 
		, _saved(0)
		, _off(0)
		, _corked(true)
		, _diofd(-1)
		, _smallbuf(smallbuf) {
		_savebuf = _get_dio_buf(smallbuf);
	}

	~socket_spigot() {
		_socket->clearbacks();
		if (_diofd > -1) {
		diocache	*d = new diocache;
		int		 n = _smallbuf;
			d->fd = _diofd;
			d->addr = _savebuf;
			d->next = _diocache[n];
			_diocache[n] = d;	
		} else {
			delete[] _savebuf;
		}
	}
	
	virtual void sp_cork(void) {
		_corked = true;
	}
	virtual void sp_uncork(void) {
		_dio = (_diofd > -1) && _sp_sink->dio_supported(dio_source_fd);

		if (_corked) {
			_corked = false;
			_socketcall(_socket, 0);
		}
	}

private:
	sink_result _maybe_dio_send(off_t off, char *bf, size_t sz, ssize_t &disc) {
		if (_dio)
			return _sp_dio_ready(_diofd, off, sz, disc);
		else
			return _sp_data_ready(bf + off, sz, disc);
	}

	void _socketcall(wsocket *e, int);
	char *_get_dio_buf(bool);

	wsocket	*_socket;
	char	*_savebuf;
	size_t	 _saved;
	ssize_t	 _off;
	bool	 _corked;
	bool	 _dio;
	int	 _diofd;
	bool	 _smallbuf;
	static tss<diocache> _diocache[2];
};

/*
 * Base class to make implementing efficient filters easier.
 */
struct buffering_filter : sink, spigot {
	buffering_filter() 
		: _corked(false) {
	}

	virtual sink_result bf_transform(char const *, size_t, ssize_t &discard) = 0;
	virtual sink_result bf_eof(void) {
		return sink_result_finished;
	}

	sink_result data_ready(char const *buf, size_t len, ssize_t &discard) {
	sink_result	result;
	ssize_t		d = discard;
		while ((size_t)(discard - d) < len) {
			result = bf_transform(buf + (discard - d), len - (discard - d), discard);
			if (result != sink_result_okay &&
			    result != sink_result_finished)
				return result;
			_bf_push_data();
			if (result == sink_result_finished)
				return _sp_sink->data_empty();
		}
		return result;
	}

	sink_result data_empty () {
	sink_result	res;
		WDEBUG("buffering_filter: data_empty");
		res = bf_eof();
		if (res != sink_result_finished)
			return res;
		res = _bf_push_data();
		if (res != sink_result_okay && res != sink_result_finished)
			return res;
		return _sp_sink->data_empty();
	}
	
	void sp_uncork(void) {
		_corked = false;
		if (_bf_push_data() != sink_result_okay)
			return;
		_sink_spigot->sp_uncork();
	}

	void sp_cork(void) {
		_sink_spigot->sp_cork();
	}

	sink_result _bf_push_data(void) {
		while (!_corked && _buf.items.size()) {
		net::buffer_item	&b = *_buf.items.begin();
		ssize_t			 discard = 0;
		sink_result		 res;
			res = _sp_sink->data_ready(b.buf + b.off, b.len, discard);
			if ((size_t)discard == b.len) {
				_buf.items.pop_front();
			} else {
				b.len -= discard;
				b.off += discard;
			}
			switch (res) {
			case sink_result_blocked:
				sp_cork();
				return res;
			case sink_result_okay:
				continue;
			case sink_result_finished:
				return res;
			case sink_result_error:
				return res;
			}
		}
		return sink_result_okay;
	}

	bool		_corked;
	net::buffer	_buf;
};

struct buffering_spigot : spigot
{
	buffering_spigot() 
		: _corked(true) {
	}

	virtual bool bs_get_data(void) = 0;

	void sp_uncork(void) {
		_corked = false;
		while (bs_get_data()) {
			switch(_bs_push_data()) {
			case sink_result_finished:
				_sp_completed_callee();
				return;
			case sink_result_okay:
				continue;
			case sink_result_error:
				_sp_error_callee();
				return;
			case sink_result_blocked:
				return;
			}
		}
	}

	void sp_cork(void) {
		_corked = true;
	}

	sink_result _bs_push_data(void) {
		while (!_corked && _buf.items.size()) {
		net::buffer_item	&b = *_buf.items.begin();
		ssize_t			 discard = 0;
		sink_result		 res;
			res = _sp_sink->data_ready(b.buf + b.off, b.len, discard);
			if ((size_t)discard == b.len) {
				_buf.items.pop_front();
			} else {
				b.len -= discard;
				b.off += discard;
			}
			if (res != sink_result_okay)
				return res;
		}
		return sink_result_okay;
	}

	bool		_corked;
	net::buffer	_buf;
};

/*
 * A file_spigot reads data from a file.
 */
struct file_spigot : buffering_spigot, freelist_allocator<file_spigot>
{
	bool	bs_get_data(void);

	static file_spigot	*from_path(string const &, bool = false);
	static file_spigot	*from_path(char const *, bool = false);

private:
	struct cache_item {
		time_t	 mtime;
		char	*data;
		size_t	 len;
	};
	typedef map<string, cache_item> cache_map;

	bool		 _corked;
	ifstream	 _file;
	bool		 _cached;
	size_t		 _cached_size;
	char		*_cdata;
	char		 _fbuf[16384];

	static tss<cache_map> _cache;

		file_spigot	(void);
	bool	open		(char const *, bool = false);
};

/*
 * Claims to be finished after reading a certain amount of data.
 */
struct size_limiting_filter : sink, spigot, freelist_allocator<size_limiting_filter>
{
	size_limiting_filter(size_t sz) : _left(sz) {}

	void sp_uncork(void) {
		_sink_spigot->sp_uncork();
	}

	void sp_cork(void) {
		_sink_spigot->sp_cork();
	}

	bool dio_supported (dio_source s) const {
		return _sp_sink->dio_supported(s);
	}
	
	sink_result dio_ready(int fd, off_t off, size_t len, ssize_t &discard) {
	sink_result	res;
	ssize_t		sent = 0, send = min(len, _left);
		res = _sp_sink->dio_ready(fd, off, send, sent);
		_left -= sent;
		discard += sent;
		if (res == sink_result_error)
			return res;

		if (_left == 0)
			return _sp_sink->data_empty();
		return res;		
	}

	sink_result data_ready(char const *buf, size_t len, ssize_t &discard) {
	sink_result	res;
	ssize_t		sent = 0, send = min(len, _left);
		res = _sp_sink->data_ready(buf, send, sent);
		_left -= sent;
		discard += sent;
		if (res == sink_result_error)
			return res;

		if (_left == 0)
			return _sp_sink->data_empty();
		return res;		
	}

	sink_result data_empty(void) {
		return _sp_sink->data_empty();
	}
private:
	size_t	_left;
};

} // namespace io

#endif
