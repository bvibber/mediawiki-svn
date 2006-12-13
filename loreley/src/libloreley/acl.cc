/* Loreley: Lightweight HTTP reverse-proxy.			*/
/* acl: ACL definitions.					*/
/* Copyright (C) 2001, 2002 Pim van Pelt <pim@ipng.nl>		*/
/* Copyright 2006 River Tarnell <river@attenuate.org>		*/
/*
 * This is from DAPd package (http://dapd.sourceforge.net).  It was
 * released under the GPL and relicensed with permission from 
 * Pim van Pelt.
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *
 *  * Redistributions in binary form must reproduce the above 
 *    copyright notice, this list of conditions and the following 
 *    disclaimer in the documentation and/or other materials provided
 *    with the distribution.
 *
 *  * Neither the name of the authors nor the names of contributors 
 *    may be used to endorse or promote products derived from this 
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */
/* From: Id: acl.c,v 1.10 2004/04/15 07:29:57 pim Exp */
/* $Id$ */

#include "stdinc.h"
using std::memcpy;

#include "acl.h"

static uint8_t maskbit[] = {0x00, 0x80, 0xc0, 0xe0, 0xf0,
			    0xf8, 0xfc, 0xfe, 0xff};
#define PNBBY 8
#define MASKBIT(offset)  ((0xff << (PNBBY - (offset))) & 0xff)

bool
aclprefix::match (const aclprefix *p) const
{
int	offset;
int	shift;

	uint8_t const *np = (uint8_t const *)&this->u.val;
	uint8_t const *pp = (uint8_t const *)&p->u.val;

	/* If n's prefix is longer than p's one return 0. */
 	if (this->prefixlen > p->prefixlen)
		return false;
	offset = this->prefixlen / PNBBY;
	shift =  this->prefixlen % PNBBY;
	if (shift)
		if (maskbit[shift] & (np[offset] ^ pp[offset]))
			return false;
	while (offset--)
		if (np[offset] != pp[offset])
			return false;

	return true;
}

bool
aclprefix::match (const sockaddr *sa) const
{
aclprefix	a;
	switch (sa->sa_family) {
	case AF_INET: {
	sockaddr_in const	*mysin = (sockaddr_in const *) sa;
		a.family = AF_INET;
		a.prefixlen = 32;
		memcpy (&a.u.val, &mysin->sin_addr, sizeof(in_addr));
		break;
	}
#ifdef AF_INET6
	case AF_INET6: {
	sockaddr_in6 const	*mysin6 = (sockaddr_in6 const *) sa;
		a.family = AF_INET6;
		a.prefixlen = 128;
		memcpy (&a.u.val, &mysin6->sin6_addr, sizeof(in6_addr));
		break;
	}
#endif
	default:
		return false;
	}
	if (sa->sa_family != this->family)
		return false;
	return this->match(&a);
}

acl::acl (int family_, string const &name_) 
	: _family(family_)
	, _name(name_)
{
}

const aclnode *
acl::match (const sockaddr *sa)
{
aclnode *an;
vector<aclnode>::const_iterator	it, end;
	if (acllist.empty())
		return NULL;
	if (_family != sa->sa_family)
		return NULL;

	for (it = acllist.begin(), end = acllist.end(); it != end; ++it) {
		if (it->prefix.match(sa))
			return &*it;
	}
	return NULL;
}

bool
acl::add (const char *prefix, const uint8_t prefixlen, const uint32_t action, const uint32_t flags)
{
aclnode an;

	an.prefix.family = _family;
	an.prefix.prefixlen = prefixlen;
	if (inet_pton(an.prefix.family, prefix, (void *)&an.prefix.u.val) <= 0) {
		return false;
	}
	an.action = action;
	an.flags = flags;
	acllist.push_back(an);
	return true;
}

void
acl::name (string const &name)
{
	_name = name;
}

string const &
acl::name (void) const
{
	return _name;
}

int
acl::family(void) const
{
	return _family;
}
