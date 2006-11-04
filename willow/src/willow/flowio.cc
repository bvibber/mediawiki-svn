/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * flowio: stream-based i/o system.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <cerrno>
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

	read = ::read(e->fde_fd, _savebuf, sizeof(_savebuf));
	WDEBUG((WLOG_DEBUG, "read %d", read));
	if (read == 0) {
		sp_cork();
		switch (this->_sp_data_empty()) {
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
	case sink_result_later:
		return;
	case sink_result_error:
		_sp_error_callee();
		return;
	case sink_result_done:
		_sp_completed_callee();
		return;
	}
	assert(_off <= read);
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
			return sink_result_later;
		}
		_sink_spigot->sp_cork();
		return sink_result_error;
		break;
	}
	WDEBUG((WLOG_DEBUG, "fde_sink::data_ready: got %lu, wrote %lu", len, wrote));
	discard = wrote;
	return sink_result_later;
}

file_spigot::file_spigot(void)
	: _corked(false)
	, _off(-1)
	, _size(0)
{
}

file_spigot *
file_spigot::from_path(string const &path)
{
file_spigot	*s = new file_spigot;
	if (!s->open(path.c_str())) {
		delete s;
		return NULL;
	}
	return s;
}

file_spigot *
file_spigot::from_path(char const *path)
{
file_spigot	*s = new file_spigot;
	if (!s->open(path)) {
		delete s;
		return NULL;
	}
	return s;
}

bool
file_spigot::open(char const *file)
{
	_file.open(file);
	return _file.is_open();
}

void
file_spigot::sp_cork(void)
{
	_corked = true;
}

void
file_spigot::sp_uncork(void)
{
	_corked = false;
	while (!_corked) {
	char		*buf;
	size_t		 sz;
		if (_off >= 0 && _off < _size) {
			WDEBUG((WLOG_DEBUG, "file_spigot: _off=%d, _size=%d",
				(int) _off, (int) _size));
			buf = _buf + _off;
			sz = _size - _off;
		} else {
			_size = _file.readsome(_buf, 16384);
			if (_size == 0) {
				_sp_completed_callee();
				return;
			} else if (_file.fail()) {
				_sp_error_callee();
				return;
			}
			WDEBUG((WLOG_DEBUG, "file_spigot: read %d from file", (int)_size));
			_off = 0;
			buf = _buf;
			sz = _size;
		}

		switch (_sp_data_ready(buf, sz, _off)) {
		case sink_result_error:
			_sp_error_callee();
			return;
		case sink_result_later:
			continue;
		case sink_result_done:
			sp_cork();
			_sp_completed_callee();
			return;
		}
	}
}

} // namespace io
