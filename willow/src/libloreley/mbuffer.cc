/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* mbuffer: helper buffer for data marshalling.				*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "mbuffer.h"

template<>
void
marshalling_buffer::append<imstring>(imstring const &o) {
	append<size_t>(o.size());
	append_bytes(o.data(), o.size());
}

template<>
bool
marshalling_buffer::extract<imstring>(imstring &s)
{
size_t	sz = 0;
	WDEBUG("DB: extracting an imstring");
	if (!extract<size_t>(sz))
		return false;
	if (_size + sz > _bufsz)
		return false;
	s.reserve(sz);
	memcpy(s.data(), _buf + _size, sz);
	*(s.data() + sz) = '\0';
	_size += sz;
	return true;
}
