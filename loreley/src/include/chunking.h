/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* chunking: HTTP 1.1 (de)chunking filters				*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef DECHUNKING_FILTER_H
#define DECHUNKING_FILTER_H

#include "loreley.h"
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

	enum {
		s_start,
		s_data,
		s_end_chunk,
		s_end_chunk_n,
		s_trailers,
		s_trailers_n
	}	_state;
	ssize_t	_current_chunk_size;
	size_t	_counter;
	bool	_atend;
	bool	_first;
	char	_cbuf[16];		/* enough to hold a chunk size */
};

#endif
