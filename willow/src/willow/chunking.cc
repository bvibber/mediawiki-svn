/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * dechunking_filter: HTTP 1.1 dechunking filter
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <algorithm>
using std::min;

#include "chunking.h"
#include "whttp_header.h"
#include "flowio.h"

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
	WDEBUG((WLOG_DEBUG, "chunking_filter: EOF"));
	_buf.add("\r\n0\r\n", 5, false);
	return io::sink_result_finished;
}

dechunking_filter::dechunking_filter()
	: _current_chunk_size(0)
	, _counter(0)
{
}

io::sink_result
dechunking_filter::bf_transform(char const *buf, size_t len, ssize_t &discard)
{
	if (_current_chunk_size) {
	size_t	sent = min(len, _current_chunk_size);
		_buf.add(buf, sent, false);
		_current_chunk_size -= sent;
		discard += sent;
		_counter += sent;
	} else {
		/* this data is the chunk size */
	char const	*rn;
		if ((rn = header_parser::find_rn(buf, buf + len)) == NULL) {
			return io::sink_result_okay;	/* need more data */
		}
		_current_chunk_size = str16toint(buf, rn - buf);
		discard += rn - buf + 2;
	}
	return io::sink_result_okay;
}
