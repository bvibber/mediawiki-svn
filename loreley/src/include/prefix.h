/* Copyright (c) 2005, IPng, Pim van Pelt <pim@ipng.nl>	*/
/* Copyright (c) 2006 River Tarnell. */
/*
 * This file implements a 128 bit deep Patricia Trie, to facilitate very
 * fast best-match route lookup. Parts from this (and its accompanying C
 * file) were taken from the Merit MRTd project:
 *
 *   This product includes software developed by the University of Michigan,
 *   Merit Network, Inc., and their contributors. 
 */
 
/* From: Id: radix.h,v 1.1.1.1 2005/11/07 20:17:44 pim Exp */
/* $Id$ */

#ifndef PREFIX_H
#define PREFIX_H

using std::pair;
using std::invalid_argument;

#include "ptalloc.h"

struct prefix;
template<typename> struct radix_node;
template<typename, typename> struct radix_iterator;
template<typename> struct radix;

/**
 * Exception thrown when a prefix is invalid, e.g. the IP could not be parsed.
 */
struct invalid_prefix : public invalid_argument {
	invalid_prefix(const char *_what) : invalid_argument(_what) {}
};

/**
 * Stores an IPv4 or IPv6 prefix, for use in searching radix trees.  Not
 * intended as a general-purpose IP prefix class; see socket.h for that.
 */
struct prefix {
	/**
	 * Construct a new prefix from the given string, in the form
	 * "10.0.0.0/24".  If the mask is not present, /32 is assumed.
	 *
	 * \param pfx prefix string
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	 prefix		(char const *pfx);

	/**
	 * Construct a new prefix from the given string, in the form
	 * "10.0.0.0/24".  If the mask is not present, /32 is assumed.
	 *
	 * \param pfx prefix string
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	 prefix		(string const &pfx);

	/**
	 * Construct a new prefix from the given string, in the form
	 * "10.0.0.0/24".  The mask is assumed to be /32.
	 *
	 * \param addr address to create prefix from
	 */
	 prefix		(sockaddr const *addr);

	/**
	 * Return this prefix formatted as a normalised string.
	 */
	string	 tostring	(void) const;

	/**
	 * Return the address family of this prefix: AF_INET or AF_INET6.
	 */
	int	 family		(void) const;

private:
	template<typename T>
	friend struct radix_node;
	template<typename T>
	friend struct radix;

	/**
	 * Internal function to construct a prefix from various string types.
	 *
	 * \pfx prefix string, with or without mask
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	void	_from(char const *pfx);

	/**
	 * Return this prefix as an array of octets.
	 */
	const uint8_t	*tochar		(void) const;

	/**
	 * Return this prefix as an array of octets.
	 */
	uint8_t		*tochar		(void);

	uint16_t _family;	/**< Address family; AF_INET or AF_INET6 */
	uint16_t prefixlen;	/**< Length of the prefix in bits */
	uint32_t ref_count;	/**< Number of radix nodes that refer to this
					prefix */

	/**
	 * Actual address of the prefix.
	 */
	union {
		struct in_addr 	sin4;
		struct in6_addr sin6;
	} add;
};

#endif
