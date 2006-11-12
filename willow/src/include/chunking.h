/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * dechunking_filter: HTTP 1.1 dechunking filter
 */

#ifndef DECHUNKING_FILTER_H
#define DECHUNKING_FILTER_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include "willow.h"
#include "flowio.h"

/*
 * chunking_filter: read unchunked input and write chunked data.  Used
 * when upgrading an HTTP 1.0 response with no content-length to
 * HTTP 1.1.
 */
struct chunking_filter : freelist_allocator<chunking_filter>, io::buffering_filter
{
	chunking_filter() : _first(true), _counter(0) {}

	io::sink_result	bf_transform(char const *, size_t, ssize_t &);
	io::sink_result	bf_eof(void);

	bool	_first;
	size_t	_counter;
};


/*
 * dechunking_filter: read chunked data and emit unchunked data.
 * Used for converting HTTP 1.1 responses to HTTP 1.0.
 */
struct dechunking_filter : freelist_allocator<dechunking_filter>, io::buffering_filter
{
	dechunking_filter();
	io::sink_result bf_transform(char const *, size_t, ssize_t &);

	size_t	 _current_chunk_size;
	size_t	_counter;
};

#endif
