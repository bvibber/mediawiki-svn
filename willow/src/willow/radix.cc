/* 
 * This file implements a 128 bit deep Patricia Trie, to facilitate very
 * fast best-match route lookup. Parts from this (and its accompanying C
 * file) were taken from the Merit MRTd project:
	This product includes software developed by the University of Michigan,
	Merit Network, Inc., and their contributors. 
 * 
 * See the radix.h header for more information.
 * Copyright (c) 2005, IPng, Pim van Pelt <pim@ipng.nl>
 * Copyright 2006 River Tarnell.
 */
/* From: Id: radix.c,v 1.1.1.1 2005/11/07 20:17:44 pim Exp */
/* $Id$ */

#include <stdio.h>
#include <cstring>
#include <cstdlib>
#include <cassert>
#include <utility>
using std::pair;
using std::make_pair;

#include "willow.h"
#include "radix.h"

#define RADIX_WALK(Xhead, Xnode) \
    do { \
        struct radix_node *Xstack[RADIX_MAXBITS+1]; \
        struct radix_node **Xsp = Xstack; \
        struct radix_node *Xrn = (Xhead); \
        while ((Xnode = Xrn)) { \
            if (Xnode->prefix)

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

string
prefix::tostring (void)
{
static char	ret[100];
char		ipstr[100];
	inet_ntop(_family, (void*)&add, ipstr, sizeof(ipstr));
	snprintf(ret, 100, "%s/%d", ipstr, prefixlen);
	return ret;
}

int
prefix::family(void) const
{
	return _family;
}

static int 
comp_with_mask (void const *addr, void const *dest, uint32_t mask)
{
	if ( /* mask/8 == 0 || */ memcmp (addr, dest, mask / 8) == 0) {
		int n = mask / 8;
		int m = ((-1) << (8 - (mask % 8)));
		if (mask % 8 == 0 || (((uint8_t const*)addr)[n] & m) == (((uint8_t const*)dest)[n] & m))
			return (1);
	}
	return (0);
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


prefix::prefix(void)
	: _family(0)
	, prefixlen(0)
	, ref_count(0)
{
	memset(&add, 0, sizeof(add));
}

prefix::prefix(char const *s)
{
	_from(s);
}

prefix::prefix(string const &s)
{
	_from(s.c_str());
}

void
prefix::_from(char const *string)
{
char const *cp;
char prefixstr[64];

	if (strchr(string, ':')) {
		prefixlen = 128;
		_family = AF_INET6;
	} else if (strchr(string, '.')) {
		_family = AF_INET;
		prefixlen = 32;
	} else {
		throw invalid_prefix("cannot parse IP address");
	}

	if ((cp = std::strchr(string, '/')) != NULL) {
		prefixlen = atol (cp+1);
		memcpy(prefixstr, string, cp-string);
		prefixstr[cp-string] = '\0';
	} else {
		strlcpy (prefixstr, string, sizeof(prefixstr));
	}

	if (inet_pton(_family, prefixstr, &add) != 1)
		throw invalid_prefix("IP address is invalid");
}

prefix::prefix(sockaddr const *addr)
{
sockaddr_in	*in;
sockaddr_in6	*in6;
	_family = addr->sa_family;
	switch (_family) {
	case AF_INET:
		prefixlen = 32;
		in = (sockaddr_in *)addr;
		memcpy(&add.sin4, &in->sin_addr, sizeof(in->sin_addr));
		break;
	case AF_INET6:
		prefixlen = 128;
		in6 = (sockaddr_in6 *)addr;
		memcpy(&add.sin6, &in6->sin6_addr, sizeof(in6->sin6_addr));
		break;
	default:
		abort();
	}
}

radix::radix(void)
	: head(NULL)
	, maxbits(128)
	, num_active_node(0)
	, dtor(NULL)
{
}

radix::~radix(void)
{
	clear(dtor);
}

radix_node *
radix::search (string const &s) const
{
	return search(s.c_str());
}

radix_node *
radix::search (const char *prefixstr) const
{
prefix	pfx(prefixstr);
	return search(&pfx);
}

radix_node *
radix::search (const sockaddr *addr) const
{
prefix	prefix(addr);
	return search(&prefix);
}

radix_node *
radix::search (prefix const *prefix) const
{
int		 inclusive = 1;
radix_node	*node;
radix_node	*stack[RADIX_MAXBITS + 1];
uint8_t	const	*addr;
int		 cnt = 0;

	if (prefix->prefixlen > maxbits)
		return NULL;

	if (head == NULL)
		return NULL;

	node = head;
	addr = prefix->tochar();

	while (node->bit < prefix->prefixlen) {
		if (node->prefix) {
			stack[cnt++] = node;
		}

		if (BIT_TEST (addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			node = node->r;
		} else {
			node = node->l;
		}

		if (node == NULL)
			break;
	}

	if (inclusive && node && node->prefix)
		stack[cnt++] = node;

	if (cnt <= 0) {
		return (NULL);
	}

	while (--cnt >= 0) {
		node = stack[cnt];
		if (comp_with_mask(node->prefix->tochar(), prefix->tochar(), node->prefix->prefixlen)) { 
			return node;
		}
	}
	return NULL;
}

void
radix::clear (void_fn_t func)
{
	if (head) {
	radix_node *Xstack[RADIX_MAXBITS+1];
	radix_node **Xsp = Xstack;
	radix_node *Xrn = head;

		while (Xrn) {
		radix_node *l = Xrn->l;
		radix_node *r = Xrn->r;

			if (Xrn->prefix) {
				delete Xrn->prefix;
				if (Xrn->data && func)
					((void (*)(void *))func) (Xrn->data);
			} else {
				assert (NULL == Xrn->data);
			}
			Xrn->prefix = NULL;
			delete Xrn;
			num_active_node--;
			if (l) {
				if (r) {
					*Xsp++ = r;
				}
				Xrn = l;
			} else if (r) {
				Xrn = r;
			} else if (Xsp != Xstack) {
				Xrn = *(--Xsp);
			} else {
				Xrn = NULL;
			}
		}
	}
	assert(num_active_node == 0);
	return;
}

void
radix::doall(void_fn_t func)
{
radix_node *node;

	if (!func)
		return;

	RADIX_WALK (head, node) {
		((void (*)(prefix *, void *)) func) (node->prefix, node->data);
	} RADIX_WALK_END;
}

radix_node *
radix::search_exact (string const &s) const
{
	return search_exact(s.c_str());
}

radix_node *
radix::search_exact (const char *prefixstr) const
{
prefix	pfx(prefixstr);
	return search_exact(&pfx);
}

radix_node *
radix::search_exact (const sockaddr *addr) const
{
prefix	pfx(addr);
	return search_exact(&pfx);
}

radix_node *
radix::search_exact (prefix const *pfx) const
{
radix_node	*node;
uint8_t const	*addr;

	assert(pfx->prefixlen <= maxbits);

	if (!head)
		return NULL;

	node = head;
	addr = pfx->tochar();

	while (node->bit < pfx->prefixlen) {
		if (BIT_TEST (addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			node = node->r;
		} else {
			node = node->l;
		}

		if (node == NULL)
			return NULL;
	}
	if (node->bit > pfx->prefixlen || node->prefix == NULL)
		return NULL;
	assert(node->bit == pfx->prefixlen);
	assert(node->bit == node->prefix->prefixlen);
	if (comp_with_mask(node->prefix->tochar(), pfx->tochar(), pfx->prefixlen))
		return node;
	
	return NULL;
}

radix_node *
radix::add (string const &s)
{
	return add(s.c_str());
}

radix_node *
radix::add (const char *prefixstr)
{
	return add(new prefix(prefixstr));
}

radix_node *
radix::add(prefix *pfx)
{
radix_node	*node, *new_node, *parent, *glue;
uint8_t		*addr, *test_addr;
uint32_t	 prefixlen, check_bit, differ_bit;
int		 i, j, r;

	if ((node = search_exact(pfx)) != NULL) {
		delete pfx;
		return node;
	}

	if (head == NULL) {
		node = new radix_node;
		node->bit = pfx->prefixlen;
		node->prefix = pfx;
		node->parent = NULL;
		node->l = node->r = NULL;
		node->data = NULL;
		head = node;
		num_active_node++;
		return node;
	}

	addr = pfx->tochar();
	prefixlen = pfx->prefixlen;
	node = head;

	while (node->bit < prefixlen || node->prefix == NULL) {
		if (node->bit < maxbits && BIT_TEST(addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			if (node->r == NULL)
				break;
			node = node->r;
		} else {
			if (node->l == NULL)
				break;
			node = node->l;
		}
		assert (node);
	}
	assert(node->prefix);

	test_addr = node->prefix->tochar();
	/* find the first bit different */
	check_bit = (node->bit < prefixlen)? node->bit: prefixlen;
	differ_bit = 0;
	for (i = 0; i*8 < (int) check_bit; i++) {
		if ((r = (addr[i] ^ test_addr[i])) == 0) {
			differ_bit = (i + 1) * 8;
			continue;
		}
		/* I know the better way, but for now */
		for (j = 0; j < 8; j++) {
			if (BIT_TEST(r, (0x80 >> j)))
				break;
		}
		/* must be found */
		assert(j < 8);
		differ_bit = i * 8 + j;
		break;
	}

	if (differ_bit > check_bit)
		differ_bit = check_bit;

	parent = node->parent;
	while (parent && parent->bit >= differ_bit) {
		node = parent;
		parent = node->parent;
	}

	if (differ_bit == prefixlen && node->bit == prefixlen) {
		if (node->prefix) {
			return node;
		}
		node->prefix = pfx;
		assert(node->data == NULL);
		return node;
	}

	new_node = new radix_node;
	new_node->bit = pfx->prefixlen;
	new_node->prefix = pfx;
	new_node->parent = NULL;
	new_node->l = new_node->r = NULL;
	new_node->data = NULL;
	num_active_node++;

	if (node->bit == differ_bit) {
		new_node->parent = node;
		if (node->bit < maxbits && BIT_TEST(addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			assert(node->r == NULL);
			node->r = new_node;
		} else {
			assert(node->l == NULL);
			node->l = new_node;
		}
		return new_node;
	}

	if (prefixlen == differ_bit) {
		if (prefixlen < maxbits && BIT_TEST (test_addr[prefixlen >> 3], 0x80 >> (prefixlen & 0x07))) {
			new_node->r = node;
		} else {
			new_node->l = node;
		}
		new_node->parent = node->parent;
		if (node->parent == NULL) {
			assert(head == node);
			head = new_node;
		} else if (node->parent->r == node) {
			node->parent->r = new_node;
		} else {
			node->parent->l = new_node;
		}
		node->parent = new_node;
	} else {
		glue = new radix_node;
		glue->bit = differ_bit;
		glue->prefix = NULL;
		glue->parent = node->parent;
		glue->data = NULL;
		num_active_node++;
		if (differ_bit < maxbits && BIT_TEST(addr[differ_bit >> 3], 0x80 >> (differ_bit & 0x07))) {
			glue->r = new_node;
			glue->l = node;
		} else {
			glue->r = node;
			glue->l = new_node;
		}
		new_node->parent = glue;
		if (node->parent == NULL) {
			assert(head == node);
			head = glue;
		} else if (node->parent->r == node) { 
			node->parent->r = glue;
		} else { 
			node->parent->l = glue;
		}
		node->parent = glue; 
	}
	return new_node;
}

void
radix::remove (struct radix_node *node)
{
radix_node *parent, *child;

	assert (node);

	if (node->r && node->l) {
		delete node->prefix;
		node->prefix = NULL;
		node->data = NULL;
		return;
	}

	if (node->r == NULL && node->l == NULL) {
		parent = node->parent;
		delete node->prefix;
		node->prefix = NULL;
		delete node;
		num_active_node--;
		if (parent == NULL) {
			assert(head == node);
			head = NULL;
			return;
		}

		if (parent->r == node) {
			parent->r = NULL;
			child = parent->l;
		} else {
			assert(parent->l == node);
			parent->l = NULL;
			child = parent->r;
		}

		if (parent->prefix)
			return;

		/* we need to remove parent too */

		if (parent->parent == NULL) {
			assert(head == parent);
			head = child;
		} else if (parent->parent->r == parent) {
			parent->parent->r = child;
		} else {
			assert(parent->parent->l == parent);
			parent->parent->l = child;
		}
		child->parent = parent->parent;
		delete node;
		num_active_node--;
		return;
	}
	if (node->r) {
		child = node->r;
	} else {
		assert (node->l);
		child = node->l;
	}
	parent = node->parent;
	child->parent = parent;

	delete node->prefix;
	node->prefix = NULL;
	delete node;
	num_active_node--;

	if (parent == NULL) {
		assert(head == node);
		head = child;
		return;
	}

	if (parent->r == node) {
		parent->r = child;
	} else {
		assert(parent->l == node);
		parent->l = child;
	}

	return;
}

int
radix::del (string const &s)
{
	return del(s.c_str());
}

int
radix::del (const char *prefixstr)
{
radix_node *node;

	node = search_exact(prefixstr);
	if (!node)
		return -1;
	remove(node);
	return 0;
}

/*
 * Lower 16 bits are reserved for consumers.
 */
const int access_list::_denyflg		= 0x10000;
const int access_list::_allowflg	= 0x20000;

pair<bool,uint16_t>
access_list::allowed(char const *s) const
{
prefix	p(s);
	return allowed(&p);
}

pair<bool,uint16_t>
access_list::allowed(string const &s) const
{
	return allowed(s.c_str());
}

pair<bool,uint16_t>
access_list::allowed(sockaddr const *addr) const
{
prefix	p(addr);
	return allowed(&p);
}

pair<bool,uint16_t>
access_list::allowed(prefix const *p) const
{
	if (_empty())
		return make_pair(false, 0);

radix_node	*r;
	if ((r = _get(p)) == NULL)
		return make_pair(true, 0);

	if (r->flags & _denyflg)
		return make_pair(false, r->flags & 0xFFFF);
	else if (r->flags & _allowflg)
		return make_pair(true, r->flags & 0xFFFF);
	else
		abort();
}

radix_node *
access_list::_get(prefix const *p) const
{
	switch (p->family()) {
	case AF_INET:
		return _v4.search(p);
	case AF_INET6:
		return _v6.search(p);
	}
	abort();
}

bool
access_list::_empty(void) const
{
	return _v4.empty() && _v6.empty();
}

radix_node *
access_list::_add(prefix *p, int flags)
{
radix_node	*r;
	switch (p->family()) {
	case AF_INET:
		r = _v4.add(p);
		break;
	case AF_INET6:
		r = _v6.add(p);
		break;
	default:
		abort();
	}
	r->flags = flags;
	return r;
}

void
access_list::allow(char const *s, uint16_t flags)
{
	_add(new prefix(s), _allowflg | (flags & 0xFFFF));
}

void
access_list::allow(string const &s, uint16_t flags)
{
	allow(s.c_str(), flags);
}

void
access_list::deny(char const *s, uint16_t flags)
{
	_add(new prefix(s), _denyflg | (flags & 0xFFFF));
}

void
access_list::deny(string const &s, uint16_t flags)
{
	deny(s.c_str(), flags);
}
