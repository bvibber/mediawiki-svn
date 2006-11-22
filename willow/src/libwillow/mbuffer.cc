/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * mbuffer: helper buffer for data marshalling.
 */

#if defined __SUNPRO_CC || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

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
