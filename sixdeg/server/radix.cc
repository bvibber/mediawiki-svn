/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* Copyright (c) 2005, IPng, Pim van Pelt <pim@ipng.nl>			*/
/* Copyright (c) 2006 River Tarnell <river@attenuate.org>		*/
/* 
 * This file implements a 128 bit deep Patricia Trie, to facilitate very
 * fast best-match route lookup. Parts from this (and its accompanying C
 * file) were taken from the Merit MRTd project:
 *
 *	This product includes software developed by the University of Michigan,
 *	Merit Network, Inc., and their contributors. 
 * 
 * See the radix.h header for more information.
 */

/* From: Id: radix.c,v 1.1.1.1 2005/11/07 20:17:44 pim Exp */
/* From: (Loreley) Id: radix.cc 18347 2006-12-15 01:59:58Z river */
/* $Id$ */

#include <stdio.h>
#include <arpa/inet.h>
#include "radix.h"

#define RADIX_WALK(Xhead, Xnode) \
    do { \
        struct radix_node *Xstack[RADIX_MAXBITS+1]; \
        struct radix_node **Xsp = Xstack; \
        struct radix_node *Xrn = (Xhead); \
        while ((Xnode = Xrn)) { \
            if (Xnode->pfx)

#define RADIX_WALK_ALL(Xhead, Xnode) \
do { \
        struct radix_node *Xstack[RADIX_MAXBITS+1]; \
        struct radix_node **Xsp = Xstack; \
        struct radix_node *Xrn = (Xhead); \
        while ((Xnode = Xrn)) { \
            if (1)

#define RADIX_WALK_BREAK { \
            if (Xsp != Xstack) { \
                Xrn = *(--Xsp); \
             } else { \
                Xrn = (struct radix_node *) 0; \
            } \
            continue; }

#define RADIX_WALK_END \
            if (Xrn->l) { \
                if (Xrn->r) { \
                    *Xsp++ = Xrn->r; \
                } \
                Xrn = Xrn->l; \
            } else if (Xrn->r) { \
                Xrn = Xrn->r; \
            } else if (Xsp != Xstack) { \
                Xrn = *(--Xsp); \
            } else { \
                Xrn = (struct radix_node *) 0; \
            } \
        } \
    } while (0)

namespace radix_detail {

int 
comp_with_mask (void const *addr, void const *dest, uint32_t mask)
{
	if ( /* mask/8 == 0 || */ std::memcmp(addr, dest, mask / 8) == 0) {
		int n = mask / 8;
		int m = ((-1) << (8 - (mask % 8)));
		if (mask % 8 == 0 || (((uint8_t const*)addr)[n] & m) == (((uint8_t const*)dest)[n] & m))
			return (1);
	}
	return (0);
}

}

std::string
prefix::tostring (void) const
{
char	ret[100];
char	ipstr[100];
	inet_ntop(_family, (void const*) &add, ipstr, sizeof(ipstr));
	snprintf(ret, 100, "%s/%d", ipstr, prefixlen);
	return ret;
}

int
prefix::family(void) const
{
	return _family;
}

uint8_t const *
prefix::tochar(void) const
{
	return ((uint8_t const *) &add.sin4);
}

uint8_t *
prefix::tochar(void)
{
	return ((uint8_t *) &add.sin4);
}


prefix::prefix(char const *s)
{
	_from(s);
}

prefix::prefix(std::string const &s)
{
	_from(s.c_str());
}

void
prefix::_from(char const *string)
{
char const *cp;
char prefixstr[64] = {};

	if (std::strchr(string, ':')) {
		prefixlen = 128;
		_family = AF_INET6;
	} else if (std::strchr(string, '.')) {
		_family = AF_INET;
		prefixlen = 32;
	} else {
		throw invalid_prefix("cannot parse IP address");
	}

	if ((cp = std::strchr(string, '/')) != NULL) {
		prefixlen = std::atol (cp+1);
		std::memcpy(prefixstr, string, cp-string);
		prefixstr[cp-string] = '\0';
	} else {
		std::strncpy (prefixstr, string, sizeof(prefixstr));
	}

	if (inet_pton(_family, prefixstr, &add) != 1)
		throw invalid_prefix("IP address is invalid");
}

prefix::prefix(sockaddr const *addr)
{
sockaddr_in const	*in;
sockaddr_in6 const	*in6;
	_family = addr->sa_family;
	switch (_family) {
	case AF_INET:
		prefixlen = 32;
		in = reinterpret_cast<sockaddr_in const *>(addr);
		std::memcpy(&add.sin4, &in->sin_addr, sizeof(in->sin_addr));
		break;
	case AF_INET6:
		prefixlen = 128;
		in6 = reinterpret_cast<sockaddr_in6 const *>(addr);
		std::memcpy(&add.sin6, &in6->sin6_addr, sizeof(in6->sin6_addr));
		break;
	default:
		std::abort();
	}
}

