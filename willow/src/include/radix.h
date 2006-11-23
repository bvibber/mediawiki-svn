/*
 *
 * This file implements a 128 bit deep Patricia Trie, to facilitate very
 * fast best-match route lookup. Parts from this (and its accompanying C
 * file) were taken from the Merit MRTd project:
   This product includes software developed by the University of Michigan,
   Merit Network, Inc., and their contributors. 
 * 
 * Copyright (c) 2005, IPng, Pim van Pelt <pim@ipng.nl>
 * Copyright 2006 River Tarnell.
 */
/* From: Id: radix.h,v 1.1.1.1 2005/11/07 20:17:44 pim Exp */
/* $Id$ */

#ifndef _RADIX_H
#define _RADIX_H

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#include <netdb.h>
#include <inttypes.h>

#include <stdexcept>
#include <utility>
using std::invalid_argument;
using std::pair;

#include "ptalloc.h"
#include "willow.h"

typedef void (*void_fn_t)();

#define RADIX_MAXBITS 128
#define BIT_TEST(f, b)  ((f) & (b))

class prefix;
class radix_node;
class radix;

struct invalid_prefix : public invalid_argument {
	invalid_prefix(const char *_what) : invalid_argument(_what) {}
};

class prefix {
public:
		 prefix		(void);
		 prefix		(char const *);
		 prefix		(string const &);
		 prefix		(sockaddr const *);

	string	 tostring	(void);

	int	 family		(void) const;

private:
	friend class radix_node;
	friend class radix;

	void	_from(char const *);

	const uint8_t	*tochar		(void) const;
	uint8_t		*tochar		(void);

	uint16_t _family;
	uint16_t prefixlen;
	uint32_t ref_count;
	union {
		struct in_addr 	sin4;
		struct in6_addr sin6;
	} add;
};

struct radix_node {
		radix_node() 
			: bit(0)
			, pfx(0)
			, l(0)
			, r(0)
			, parent(0)
			, data(0)
			, flags(0)
		{}
		radix_node(radix_node const &);

	uint32_t	 bit;
	prefix 		*pfx;
	radix_node 	*l, *r;
	radix_node 	*parent;
	void 		*data;
	int		 flags;
};

class radix {
public:
		radix();
		virtual ~radix();
		radix(radix const &);
		radix &operator= (radix const &);

	void		 set_dtor	(void_fn_t);
	radix_node	*add		(char const *);
	radix_node	*add		(prefix *);
	radix_node	*add		(string const &);
	int		 del		(char const *);
	int		 del		(string const &);
	void		 doall		(void_fn_t);
	void		 clear		(void_fn_t);
	void		 remove		(radix_node *);

	radix_node	*search		(char const *) const;
	radix_node	*search		(prefix const *) const;
	radix_node	*search		(string const &) const;
	radix_node	*search		(sockaddr const *) const;

	radix_node	*search_exact	(char const*) const;
	radix_node	*search_exact	(prefix const *) const;
	radix_node	*search_exact	(string const &) const;
	radix_node	*search_exact	(sockaddr const *) const;

	bool		 empty		(void) const {
		return head;
	}

private:
	radix_node 	*head;
	uint32_t	 maxbits;
	uint32_t	 num_active_node;
	void_fn_t	 dtor;
};

class access_list : radix {
public:
	pair<bool,uint16_t>	allowed	(char const *) const;
	pair<bool,uint16_t>	allowed	(string const &) const;
	pair<bool,uint16_t>	allowed	(sockaddr const *) const;
	pair<bool,uint16_t>	allowed	(prefix const *) const;

	void	allow	(char const *, uint16_t flags = 0);
	void	allow	(string const &, uint16_t flags = 0);

	void	deny	(char const *, uint16_t flags = 0);
	void	deny	(string const &, uint16_t flags = 0);

private:
	radix_node	*_add	(prefix *, int);
	radix_node	*_get	(prefix const *) const;
	bool		_empty	(void) const;

	static const int	_denyflg;
	static const int	_allowflg;

	radix			_v4;
	radix			_v6;
};

#endif /* _RADIX_H */
