/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* access: simple IP access control					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef ACCESS_H
#define ACCESS_H

#include "prefix.h"

struct access_list_impl;

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
struct access_list {
	access_list();
	~access_list();

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
	pair<bool,uint16_t>	allowed	(prefix const &pfx) const;

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

	/**
	 * Test if the tree is empty.
	 * \returns true if empty, otherwise false
	 */
	bool	empty	(void) const;

private:
	access_list_impl	*impl;
};

#endif
