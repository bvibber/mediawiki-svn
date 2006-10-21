/* 
 * Willow: Lightweight HTTP reverse-proxy.
 * acl: ACL definitions.
 * Copyright (C) 2001, 2002 Pim van Pelt <pim@ipng.nl>
 * Copyright 2006 River Tarnell <river@attenuate.org>
 *
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

#include <sys/types.h>
#include <sys/socket.h>

#include <netinet/in.h>
#include <arpa/inet.h>

#include <cstdlib>
#include <cstdarg>
#include <cstdio>
#include <cstring>
#include <cctype>
#include <vector>
#include <string>
using std::string;
using std::vector;

#include <unistd.h>

struct aclprefix;
struct aclnode;
struct acl;

#define ACL_NONE        0x00000000
#define ACL_PASS	0x00000001
#define ACL_BLOCK       0x00000002

#define ACLFL_NONE      0x00000000
#define ACLFL_LOG       0x00000001

struct aclprefix
{
	bool	match	(const sockaddr *) const;
	bool	match	(const aclprefix *) const;

	uint8_t family;
	uint8_t prefixlen;
	union {
		uint8_t val[16];
		in_addr prefix4;
#ifdef AF_INET6
		in6_addr prefix6;
#endif
	} u;
};

struct aclnode {
        aclprefix	prefix;
        uint32_t	action;
        uint32_t	flags;
};

struct acl {
			acl	(int family, string const &name = "");
	string const &	name	(void) const;
	void		name	(string const &);
	int		family	(void) const;
	void		family	(int);
	bool		add	(const char *prefix, const uint8_t prefixlen,
				 const uint32_t action, const uint32_t flags);
const	aclnode *	match	(const sockaddr *sa);

        uint8_t		_family;
	vector<aclnode>	acllist;
	string		_name;
};

#endif /* __ACL_H */
