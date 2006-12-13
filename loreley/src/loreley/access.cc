/* Loreley: Lightweight HTTP reverse-proxy.				*/
/* access: IP access control						*/
/* Copyright (c) 2006 River Tarnell <river@attenuate.org>.		*/
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
using std::pair;
using std::make_pair;

#include "access.h"
#include "radix.h"

struct access_list_impl {
	pair<bool,uint16_t>	allowed	(char const *pfx) const;
	pair<bool,uint16_t>	allowed	(string const &pfx) const;
	pair<bool,uint16_t>	allowed	(sockaddr const *pfx) const;
	pair<bool,uint16_t>	allowed	(prefix const &pfx) const;
	void	allow	(char const *pfx, uint16_t flags);
	void	allow	(string const &pfx, uint16_t flags);
	void	deny	(char const *pfx, uint16_t flags);
	void	deny	(string const &pfx, uint16_t flags);
	bool	empty	(void) const;

	radix<uint32_t>::iterator _add	(prefix const &, int);
	radix<uint32_t>::const_iterator _get	(prefix const &) const;
	radix<uint32_t>::const_iterator _end	(prefix const &) const;

	static const int	_denyflg;	/**< Flag value for denied pfxs */
	static const int	_allowflg;	/**< Flag value for allowed pfxs */

	radix<uint32_t>		_v4;	/**< Access list for v4 prefixes */
	radix<uint32_t>		_v6;	/**< Access list for v6 prefixes */
};

/*
 * Lower 16 bits are reserved for consumers.
 */
const int access_list_impl::_denyflg	= 0x10000;
const int access_list_impl::_allowflg	= 0x20000;

pair<bool,uint16_t>
access_list_impl::allowed(char const *s) const
{
	return allowed(prefix(s));
}

pair<bool,uint16_t>
access_list_impl::allowed(string const &s) const
{
	return allowed(prefix(s.c_str()));
}

pair<bool,uint16_t>
access_list_impl::allowed(sockaddr const *addr) const
{
	return allowed(prefix(addr));
}

pair<bool,uint16_t>
access_list_impl::allowed(prefix const &p) const
{
	if (empty())
		return make_pair(false, 0);

radix<uint32_t>::const_iterator	r;
	if ((r = _get(p)) == _end(p))
		return make_pair(false, 0);

	if (*r & _denyflg)
		return make_pair(false, *r & 0xFFFF);
	else if (*r & _allowflg)
		return make_pair(true, *r & 0xFFFF);
	else
		abort();
}

radix<uint32_t>::const_iterator
access_list_impl::_end(prefix const &p) const
{
	switch (p.family()) {
	case AF_INET:
		return _v4.end();
	case AF_INET6:
		return _v6.end();
	}
	abort();
}

radix<uint32_t>::const_iterator
access_list_impl::_get(prefix const &p) const
{
	switch (p.family()) {
	case AF_INET:
		return _v4.search(p);
	case AF_INET6:
		return _v6.search(p);
	}
	abort();
}

bool
access_list_impl::empty(void) const
{
	return _v4.empty() && _v6.empty();
}

radix<uint32_t>::iterator
access_list_impl::_add(prefix const &p, int flags)
{
radix<uint32_t>::iterator	r;
	switch (p.family()) {
	case AF_INET:
		return _v4.insert(p, flags).first;
		break;
	case AF_INET6:
		return _v6.insert(p, flags).first;
		break;
	default:
		abort();
	}
}

void
access_list_impl::allow(char const *s, uint16_t flags)
{
	_add(prefix(s), _allowflg | (flags & 0xFFFF));
}

void
access_list_impl::allow(string const &s, uint16_t flags)
{
	allow(s.c_str(), flags);
}

void
access_list_impl::deny(char const *s, uint16_t flags)
{
	WDEBUG(format("deny: flags = %d") % flags);
	_add(prefix(s), _denyflg | (flags & 0xFFFF));
}

void
access_list_impl::deny(string const &s, uint16_t flags)
{
	deny(s.c_str(), flags);
}

access_list::access_list(void)
{
	impl = new access_list_impl;
}

access_list::~access_list(void)
{
	delete impl;
}

pair<bool,uint16_t>
access_list::allowed(char const *pfx) const
{
	return impl->allowed(pfx);
}

pair<bool,uint16_t>
access_list::allowed (string const &pfx) const
{
	return impl->allowed(pfx);
}

pair<bool,uint16_t>
access_list::allowed(sockaddr const *pfx) const
{
	return impl->allowed(pfx);
}

pair<bool,uint16_t>
access_list::allowed(prefix const &pfx) const
{
	return impl->allowed(pfx);
}

void
access_list::allow(char const *pfx, uint16_t flags)
{
	impl->allow(pfx, flags);
}

void
access_list::allow(string const &pfx, uint16_t flags)
{
	impl->allow(pfx, flags);
}

void
access_list::deny(char const *pfx, uint16_t flags)
{
	impl->deny(pfx, flags);
}

void
access_list::deny(string const &pfx, uint16_t flags)
{
	impl->deny(pfx, flags);
}

bool
access_list::empty(void) const
{
	return impl->empty();
}
