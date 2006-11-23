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

/**
 * The dtor callback type, used to free resources when deleting a radix tree.
 */
typedef void (*void_fn_t)();

#define RADIX_MAXBITS 128
#define BIT_TEST(f, b)  ((f) & (b))

class prefix;
class radix_node;
class radix;

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
	friend class radix_node;
	friend class radix;

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

/**
 * A node in the radix tree.  The radix tree contains many of these; end nodes
 * have a non-null data provided by the user.
 */
struct radix_node {
	/**
	 * Construct a new, empty radix node.
	 */
	radix_node() 
		: bit(0)
		, pfx(0)
		, l(0)
		, r(0)
		, parent(0)
		, data(0)
		, flags(0)
	{}

	/**
	 * Copy an existing radix node.  This also does a deep copy of all
	 * children.
	 * \param other node to copy from
	 */
	radix_node(radix_node const &other);

	uint32_t	 bit;
	prefix 		*pfx;		/**< Prefix stored in this node. */
	radix_node 	*l, *r;		/**< Left/right nodes */
	radix_node 	*parent;	/**< Parent node */
	void 		*data;		/**< User data */
	int		 flags;		/**< User flags */
};

/**
 * A radix tree.  Radix trees (also called patricria tries) are a data
 * structure allowing very fast matching of bitstring prefixes.  This
 * implementation is designed to store IP addresses, for use in access
 * control.
 *
 * A radix tree is initially empty.  It should be populated with prefixes
 * using add().  The radix_node returned by add() has two fields that can
 * be filled in by the user: radix_node#data and radix_node#flags.  This
 * allows you to associate information with a prefix for later retrieval.
 *
 * After prefixes have been added to the tree, use search() or search_exact()
 * to query the list.  Both functions return the matching node, or NULL
 * if the node was not found.  The difference between the two is that
 * search_exact required the prefix length to match, but search() will return
 * the longest matching prefix shorter than or equal to the argument.
 *
 * For example, if the tree contains "10.0.1.0/24", search("10.0.1.10/32")
 * will return that entry; but search_exact() for the same IP will return
 * NULL.  To find the entry for 10.0.1.0/24 using search_exact, 
 * search_exact("10.0.1.0/24") would be required.
 */
struct radix {
	/**
	 * Construct a new, empty radix tree.
	 */
	radix();

	/**
	 * Dtor; frees the tree and all its nodes.
	 */
	virtual ~radix();

	/**
	 * Create a radix tree from an existing tree.
	 * \param other tree to copy from
	 */
	radix(radix const &other);

	/**
	 * Replace the contents of this tree with another.
	 * \param other tree to copy from
	 */
	radix &operator= (radix const &other);

	/**
	 * Set the function to be called when a node is removed from the tree.
	 */
	void		 set_dtor	(void_fn_t);

	/**
	 * Add a new prefix to the tree and return the node.  If the node
	 * already exists, the existing node is returned.
	 *
	 * \param pfx prefix to add, with or without /mask
	 * \returns the new node if one was added, otherwise the existing node
	 * matching this prefix.
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	radix_node	*add		(char const *pfx);

	/**
	 * Add a new prefix to the tree, and return the node.  If the node
	 * already exists, the existing node is returned.
	 *
	 * \param pfx prefix to add
	 * \returns the new node if one was added, otherwise the existing node
	 * matching this prefix.
	 */
	radix_node	*add		(prefix *pfx);

	/**
	 * Add a new prefix to the tree, and return the node.  If the node
	 * already exists, the existing node is returned.
	 *
	 * \param pfx prefix to add, with or without /mask
	 * \returns the new node if one was added, otherwise the existing node
	 * matching this prefix.
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	radix_node	*add		(string const &pfx);

	/**
	 * Delete the specified node from the tree.
	 *
	 * \param pfx prefix to delete
	 * \returns 0 if the node was removed, or -1 if the node was not found.
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	int		 del		(char const *pfx);

	/**
	 * Delete the specified node from the tree.
	 *
	 * \param pfx prefix to delete
	 * \returns 0 if the node was removed, or -1 if the node was not found.
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	int		 del		(string const &pfx);

	/**
	 * Apply the given function to all nodes of the tree.
	 */
	void		 doall		(void_fn_t func);

	/**
	 * Remove all nodes from this tree, leaving it empty.  The provided
	 * function is called on each node prior to its removal.
	 */
	void		 clear		(void_fn_t func);

	/**
	 * Remove the given node from the tree.
	 * \param n node to remove
	 */
	void		 remove		(radix_node *n);

	/**
	 * Search the tree for the specified prefix.  The longest prefix matching
	 * the given string will be returned.
	 *
	 * \param pfx prefix to search for, with or without /mask
	 * \returns matching node, or NULL if no match was found.
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	radix_node	*search		(char const *pfx) const;

	/**
	 * Search the tree for the specified prefix.  The longest prefix matching
	 * the given prefix will be returned.
	 *
	 * \param pfx prefix to search for
	 * \returns matching node, or NULL if no match was found.
	 */
	radix_node	*search		(prefix const *pfx) const;

	/**
	 * Search the tree for the specified prefix.  The longest prefix matching
	 * the given string will be returned.
	 *
	 * \param pfx prefix to search for, with or without /mask
	 * \returns matching node, or NULL if no match was found.
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	radix_node	*search		(string const &pfx) const;

	/**
	 * Search the tree for the specified address.  The longest prefix matching
	 * the given address will be returned.  The address is considered to have
	 * a /32 mask.
	 *
	 * \param pfx address to search for
	 * \returns matching node, or NULL if no match was found.
	 */
	radix_node	*search		(sockaddr const *addr) const;

	/**
	 * Search the tree for the specified address.  A matching prefix will
	 * only be returned if it is the same length as the prefix searched for.
	 *
	 * \param pfx prefix to search for, with or without /mask
	 * \returns matching node, or NULL if no match was found.
	 */
	radix_node	*search_exact	(char const *pfx) const;

	/**
	 * Search the tree for the specified address.  A matching prefix will
	 * only be returned if it is the same length as the prefix searched for.
	 *
	 * \param pfx prefix to search for
	 * \returns matching node, or NULL if no match was found.
	 */
	radix_node	*search_exact	(prefix const *pfx) const;

	/**
	 * Search the ree for the specified address.  A matching prefix will
	 * only be returned if it is the same length as the prefix searched for.
	 *
	 * \param pfx prefix to search for, with or without /mask
	 * \returns matching node, or NULL if no match was found.
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	radix_node	*search_exact	(string const &pfx) const;

	/**
	 * Search thetfree for the specified address.  A matching prefix will
	 * only be returned if it is the same length as the prefix searched for.
	 * The address is considered to have a /32 mask.
	 *
	 * \param addr address to search for
	 * \returns matching node, or NULL if no match was found.
	 */
	radix_node	*search_exact	(sockaddr const *addr) const;

	/**
	 * Test if this tree has any elements.
	 * \returns true if the tree is empty, else false
	 */
	bool		 empty		(void) const {
		return head;
	}

private:
	radix_node 	*head;	/**< Head of the tree */
	uint32_t	 maxbits; /**< Longest node in this tree */
	uint32_t	 num_active_node; /**< Number of nodes in this tree */
	void_fn_t	 dtor;	/**< Function to apply to free node data */
};

/**
 * An IP access list based on a radix tree, giving very fast lookups even for
 * large lists.
 *
 * IP addresses or prefixes should be added to the list using allow() or deny().
 * The former allows an IP access; the latter denies it.  The most-specific
 * match is used when testing whether a client has access.  For example, if
 * 10.0.0.0/8 is allowed access, but 10.0.1.0/24 is not, 10.0.1.10/32 will not
 * be allowed access, because 10.0.1.0/24 is the most specific match.
 *
 * After prefixes have been added to the list, use allowed() to query the
 * status of a particular IP.  allowed() returns a bool indicating whether the
 * given prefix is allowed access, and a flags argument containing the contents
 * of the flags argument to allow().
 *
 * If the access list is empty, no clients are allowed.  If the list contains
 * entries but no match is found, the client is allowed access.
 *
 * The access list maintains two entirely separate access lists for IPv4
 * and IPv6 clients transparently to the user; any addresses provided may be
 * either family, and the access list will add them to the correct internal
 * list.
 */
struct access_list : private radix {
	/**
	 * Test whether the given prefix is allowed to connect.
	 * \param pfx prefix string, with or without /mask (/32 default)
	 * \returns see class description
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	pair<bool,uint16_t>	allowed	(char const *pfx) const;

	/**
	 * Test whether the given prefix is allowed to connect.
	 * \param pfx prefix string, with or without /mask (/32 default)
	 * \returns see class description
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	pair<bool,uint16_t>	allowed	(string const &pfx) const;

	/**
	 * Test whether the given IP address is allowed to connect.
	 * \param pfx address to test; /32 mask is assumed
	 * \returns see class description
	 */
	pair<bool,uint16_t>	allowed	(sockaddr const *pfx) const;

	/**
	 * Test whether the given prefix is allowed to connect.
	 * \param pfx prefix to test
	 * \returns see class description
	 */
	pair<bool,uint16_t>	allowed	(prefix const *pfx) const;

	/**
	 * Allow the given prefix to connect. 
	 * \param pfx the prefix to allow.  if the prefix has no mask, /32
	 * is assumed.
	 * \param flags flags value; returned verbatim by allowed()
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	void	allow	(char const *pfx, uint16_t flags = 0);

	/**
	 * Allow the given prefix to connect. 
	 * \param pfx the prefix to allow.  if the prefix has no mask, /32
	 * is assumed.
	 * \param flags flags value; returned verbatim by allowed()
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	void	allow	(string const &pfx, uint16_t flags = 0);

	/**
	 * Disallow the given prefix from connecting. 
	 * \param pfx the prefix to deny.  if the prefix has no mask, /32
	 * is assumed.
	 * \param flags flags value; returned verbatim by allowed()
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	void	deny	(char const *pfx, uint16_t flags = 0);

	/**
	 * Disallow the given prefix from connecting. 
	 * \param pfx the prefix to deny.  if the prefix has no mask, /32
	 * is assumed.
	 * \param flags flags value; returned verbatim by allowed()
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	void	deny	(string const &pfx, uint16_t flags = 0);

private:
	/**
	 * Add an entry to the base radix tree.
	 * \returns new entry, or existing entry if duplicate.
	 */
	radix_node	*_add	(prefix *, int);

	/**
	 * Search the tree for an address.
	 * \returns the prefix node if found, else NULL
	 */
	radix_node	*_get	(prefix const *) const;

	/**
	 * Test if the tree is empty.
	 * \returns true if empty, otherwise false
	 */
	bool		_empty	(void) const;

	static const int	_denyflg;	/**< Flag value for denied pfxs */
	static const int	_allowflg;	/**< Flag value for allowed pfxs */

	radix			_v4;	/**< Access list for v4 prefixes */
	radix			_v6;	/**< Access list for v6 prefixes */
};

#endif /* RADIX_H */
