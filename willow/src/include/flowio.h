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
using std::ifstream;
using std::string;

#include "wnet.h"

namespace io {

struct sink;
struct spigot;

enum sink_result {
	sink_result_done,
	sink_result_error,
	sink_result_later
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
		if (_sink_spigot)
			_sink_spigot->sp_disconnect();
	}

	virtual sink_result	data_ready (char const*, size_t, ssize_t &) = 0;
	virtual sink_result	data_empty (void) = 0;

protected:
	friend class spigot;

	spigot		*_sink_spigot;

private:
};

/*
 * A sink that sends data to an FDE.
 */
struct fde_sink : freelist_allocator<fde_sink>, sink {
	fde_sink(fde *e)
		: _fde(e)
		, _reg(false) {
	}
	~fde_sink() {
		if (_reg)
			ioloop->clear_writeback(_fde->fde_fd);
	}

	void _fdecall(fde *e, int) {
		_spigot->sp_uncork();
	}

	sink_result data_ready(char const *buf, size_t len, ssize_t &discard);
	sink_result data_empty(void) {
		WDEBUG((WLOG_DEBUG, "fde_sink::data_empty"));
		ioloop->clear_writeback(_fde->fde_fd);
		_reg = false;
		return sink_result_done;
	}

	fde		*_fde;
	spigot		*_spigot;
	bool		 _reg;
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
		case sink_result_later:
			return;
		case sink_result_error:
			_sp_error_callee();
			return;
		case sink_result_done:
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
 * A spigot that reads from an fde.
 */
struct fde_spigot : freelist_allocator<fde_spigot>, spigot {
	/*
	 * Create a new corked fde_spigot.
	 * fde should not have any existing callbacks or odd
	 * things will happen.
	 */ 
	fde_spigot(fde *e)
		: _fde(e) 
		, _saved(0)
		, _off(0)
		, _corked(true) {
	}

	~fde_spigot() {
		if (!_corked)
			ioloop->clear_readback(_fde->fde_fd);
	}
	
	virtual void sp_cork(void) {
		if (!_corked)
			ioloop->clear_readback(_fde->fde_fd);
		_corked = true;
	}
	virtual void sp_uncork(void) {
		if (_corked) {
			_corked = false;
			ioloop->readback(_fde->fde_fd, polycaller<fde *, int>(*this, &fde_spigot::_fdecall), 0);
			_fdecall(_fde, 0);
		}
	}

private:
	void _fdecall(fde *e, int);

	static const int bufsz = 65535;
	fde	*_fde;
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
		return sink_result_done;
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
		res = bf_eof();
		if (res != sink_result_done)
			return res;
		return _sp_sink->data_empty();
	}
	
	void sp_uncork(void) {
		_corked = false;
		_bf_push_data();
		_sink_spigot->sp_uncork();
	}

	void sp_cork(void) {
		_sink_spigot->sp_cork();
	}

	sink_result _bf_push_data(void) {
		while (!_corked && _buf.items.size()) {
		wnet::buffer_item	&b = *_buf.items.begin();
		ssize_t			 discard;
		sink_result		 res;
			res = _sp_sink->data_ready(b.buf + b.off, b.len, discard);
			if ((size_t)discard == b.len) {
				_buf.items.pop_front();
			} else {
				b.len -= discard;
				b.off += discard;
			}
			if (res != sink_result_later)
				return res;
		}
		return sink_result_later;
	}

	bool		_corked;
	wnet::buffer	_buf;
};

/*
 * A file_spigot reads data from a file.
 */
struct file_spigot : spigot, freelist_allocator<file_spigot>
{
	void	sp_cork(void);
	void	sp_uncork(void);

	static file_spigot	*from_path(string const &);
	static file_spigot	*from_path(char const *);

	bool		_corked;
	ifstream	_file;
	char		_buf[16384];
	ssize_t		_off;
	ssize_t		_size;

private:
		file_spigot	(void);
	bool	open		(char const *);
};

} // namespace io

#endif
