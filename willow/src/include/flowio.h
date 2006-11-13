/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * flowio: stream-based i/o system.
 */

#ifndef FLOWIO_H
#define FLOWIO_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <fstream>
#include <string>
#include <algorithm>
#include <map>
using std::ifstream;
using std::map;
using std::min;

#include "willow.h"
#include "wnet.h"
using namespace wnet;

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

	/*
	 * Stop providing new data.
	 */
	virtual void sp_cork(void) = 0;

	/*
	 * Start providing new data again.
	 */
	virtual void sp_uncork(void) = 0;

	template<typename T>
	void completed_callee(T *o, void (T::*f)(void)) {
		_sp_completed_callee.assign(*o, f);
	}

	template<typename T>
	void error_callee(T *o, void (T::*f)(void)) {
		_sp_error_callee.assign(*o, f);
	}

protected:
	sink_result 	 _sp_data_ready (char *b, size_t s, ssize_t &discard);
	sink_result	 _sp_data_empty (void);

	polycaller<>	 _sp_completed_callee;
	polycaller<>	 _sp_error_callee;
	sink		*_sp_sink;
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

	sink_result data_ready(char const *buf, size_t len, ssize_t &discard);
	sink_result data_empty(void) {
		WDEBUG((WLOG_DEBUG, "socket_sink::data_empty"));
		_socket->uncork();
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

/*
 * A spigot that reads from a socket.
 */
struct socket_spigot : freelist_allocator<socket_spigot>, spigot {
	/*
	 * Create a new corked socket_spigot.
	 * socket should not have any existing callbacks or odd
	 * things will happen.
	 */ 
	socket_spigot(wsocket *s)
		: _socket(s) 
		, _saved(0)
		, _off(0)
		, _corked(true) {
	}

	~socket_spigot() {
		_socket->clearbacks();
	}
	
	virtual void sp_cork(void) {
		_corked = true;
	}
	virtual void sp_uncork(void) {
		if (_corked) {
			_corked = false;
			_socketcall(_socket, 0);
		}
	}

private:
	void _socketcall(wsocket *e, int);

	static const int bufsz = 65535;
	wsocket	*_socket;
	char	 _savebuf[bufsz];
	size_t	 _saved;
	ssize_t	 _off;
	bool	 _corked;
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
		return sink_result_okay;
	}

	sink_result data_ready(char const *buf, size_t len, ssize_t &discard) {
	sink_result	result;
		result = bf_transform(buf, len, discard);
		if (discard > 0)
			_bf_push_data();
		return result;
	}

	sink_result data_empty () {
	sink_result	res;
		WDEBUG((WLOG_DEBUG, "buffering_filter: data_empty"));
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
		wnet::buffer_item	&b = *_buf.items.begin();
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
	wnet::buffer	_buf;
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
		wnet::buffer_item	&b = *_buf.items.begin();
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
	wnet::buffer	_buf;
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

	sink_result data_ready(char const *buf, size_t len, ssize_t &discard) {
	sink_result	res;
	ssize_t		sent = 0, send = min(len, _left);
		res = _sp_sink->data_ready(buf, send, sent);
		_left -= sent;
		discard += sent;
		if (res == sink_result_error)
			return res;

		if (_left == 0) {
			WDEBUG((WLOG_DEBUG, "size limiter: finished"));
			return sink_result_finished;
		}
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
