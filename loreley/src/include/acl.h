/* Loreley: Lightweight HTTP reverse-proxy.			*/
/* acl: ACL definitions.					*/
/* Copyright (C) 2001, 2002 Pim van Pelt <pim@ipng.nl>		*/
/* Copyright (c) 2006 River Tarnell <river@attenuate.org>	*/
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
/* $Id$ */

#ifndef ACL_H
#define ACL_H

using std::string;
using std::vector;

struct aclprefix;
struct aclnode;
struct acl;

#define ACL_NONE        0x00000000
#define ACL_PASS	0x00000001
#define ACL_BLOCK       0x00000002

#define ACLFL_NONE      0x00000000
#define ACLFL_LOG       0x00000001

/**
 * A prefix in an ACL entry.  Stores the address family, address and prefix
 * length.  Both IPv4 and IPv6 prefixes are supported.
 *
 * The ACL does not distinguish between IPv4 and IPv6 addresses.  To match
 * on both address families, two ACLs should be used.
 *
 * This ACL implementation is O(n) for lookups.  For a an unordered, but
 * much faster, access list, see radix.h
 */
struct aclprefix
{
	/**
	 * Match the provided socket address against this ACL entry.  The prefix
	 * length is assumed to be 32 bits (for IPv4) or 128 bits (for IPv6).
	 *
	 * \param addr address to match
	 * \returns true if the address matches, otherwise false
	 */
	bool	match	(const sockaddr *addr) const;

	/**
	 * Match another ACE against this one.
	 *
	 * \param pfx prefix to match
	 * \returns true if the prefix matches, otherwise false
	 */
	bool	match	(const aclprefix *pfx) const;

	uint8_t family;		/**< Address family, AF_INET or AF_INET6 */
	uint8_t prefixlen;	/**< Prefix length of this ACE */
	union {
		uint8_t val[16];
		in_addr prefix4;
#ifdef AF_INET6
		in6_addr prefix6;
#endif
	} u;
};

/**
 * One entry (ACE) in an ACL.
 */
struct aclnode {
        aclprefix	prefix; /**< This entry's prefix */
        uint32_t	action; /**< Action to take, ACL_NONE, ACL_PASS or ACL_BLOCK */
        uint32_t	flags;  /**< Can be ACLFL_LOG to log this match. */
};

/**
 * An ACL.  Call add() to add entries to the ACL, then match() addresses
 * against the list.
 */
struct acl {
	/**
	 * Create a new ACL with the given address family and name.
	 *
	 * \param family address family, AF_INET or AF_INET6
	 * \param name name of this ACL.  The ACL itself never uses this value.
	 */
	acl	(int family, string const &name = "");

	/**
	 * Return the name of this ACL.
	 */
	string const &	name	(void) const;

	/**
	 * Rename this ACL.
	 * \param newname new name for the ACL
	 */
	void		name	(string const &newname);

	/**
	 * Return this ACL's address family.
	 */
	int		family	(void) const;

	/**
	 * Add a new entry to this ACL.
	 *
	 * \param prefix IP prefix to add, without the mask
	 * \param prefixlen length of the prefix to add, in bits
	 * \param action action to take on this entry, one of ACL_NONE,
	 * ACL_PASS or ACP_BLOCK
	 * \param flags flags for this entry, 0 or ACLFL_LOG
	 *
	 * \return true if the entry was added, false if the prefix could
	 * not be parsed.
	 */
	bool		add	(const char *prefix, const uint8_t prefixlen,
				 const uint32_t action, const uint32_t flags);

	/**
	 * Match an IP address against this ACL.
	 *
	 * \return the matching ACE if any, otherwise NULL.
	 */
const	aclnode *	match	(const sockaddr *sa);

        uint8_t		_family;
	vector<aclnode>	acllist;
	string		_name;
};

#endif /* ACL_H */
