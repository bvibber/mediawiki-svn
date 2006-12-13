/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* dechunking_filter: HTTP 1.1 dechunking filter			*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
using std::min;

#include "chunking.h"
#include "http_header.h"
#include "flowio.h"
#include "util.h"

io::sink_result
chunking_filter::bf_transform(char const *buf, size_t len, ssize_t &discard)
{
char	sstr[16], *s;
int	i;
	if (!_first) {
		i = snprintf(sstr, sizeof(sstr), "\r\n%lx\r\n", (unsigned long)len);
	} else {
		i = snprintf(sstr, sizeof(sstr), "%lx\r\n", (unsigned long) len);
		_first = false;
	}
	s = new char[strlen(sstr)];
	memcpy(s, sstr, i); 
	_buf.add(s, i, true);
	_buf.add(buf, len, false);
	discard += len;
	_counter += len;
	return io::sink_result_okay;
}

io::sink_result
chunking_filter::bf_eof(void)
{
	WDEBUG("chunking_filter: EOF");
	_buf.add("\r\n0\r\n\r\n", 7, false);
	return io::sink_result_finished;
}

dechunking_filter::dechunking_filter()
	: _state(s_start)
	, _current_chunk_size(0)
	, _counter(0)
	, _atend(false)
	, _first(true)
{
	_cbuf[0] = '\0';
}

io::sink_result
dechunking_filter::bf_transform(char const *buf, size_t len, ssize_t &discard)
{
char const	*rn;
int		 i;
ssize_t		 s;
	while (len) {
		switch (_state) {
		case s_start:	/* expect a chunk, <digits>\r\n */
			if ((rn = find_rn(buf, buf + len)) == NULL) {
				/* no \r\n yet, need more data */
				if (len + strlen(_cbuf) > 15)
					return io::sink_result_error;
				strncat(_cbuf, buf, len);
				discard += len;
				return io::sink_result_okay;
			}
			i = (rn - buf);
			if (i + strlen(_cbuf) > 15)
				return io::sink_result_error;
			strncat(_cbuf, buf, i);
			_current_chunk_size = strNtoint<16>(_cbuf, strlen(_cbuf));

			if (_current_chunk_size == -1)
				return io::sink_result_error;

			discard += i + 2;
			len -= i + 2;
			buf += i + 2;
			_cbuf[0] = '\0';
			if (_current_chunk_size == 0) {
				_state = s_trailers;
				break;;
			}
			_state = s_data;
			break;;
		case s_data:	/* expect to read _current_chunk_size bytes */
			s = min((ssize_t)len, _current_chunk_size);			
			_current_chunk_size -= s;
			_buf.add(buf, s, false);
			discard += s;
			len -= s;
			buf += s;
			if (_current_chunk_size == 0)
				_state = s_end_chunk;
			break;
		case s_end_chunk:
			if (*buf != '\r')
				return io::sink_result_error;
			_state = s_end_chunk_n;
			len--;
			buf++;
			discard++;
			break;
		case s_end_chunk_n:
			if (*buf != '\n')
				return io::sink_result_error;
			len--;
			buf++;
			discard++;
			_state = s_start;
			break;
		case s_trailers:
			if (*buf != '\r')
				return io::sink_result_error;
			_state = s_trailers_n;
			len--;
			buf++;
			discard++;
			break;
		case s_trailers_n:
			if (*buf != '\n' || len > 1)
				return io::sink_result_error;
			len--;
			buf++;
			discard++;
			return io::sink_result_finished;
		}
	}
	return io::sink_result_okay;
}
