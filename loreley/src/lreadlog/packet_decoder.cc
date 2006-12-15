/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* lreadlog: Read UDP log packets and print human-readable log.		*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
#ifdef __INTEL_COMPILER
# pragma hdrstop
#endif

using boost::lexical_cast;

#include "packet_decoder.h"

struct logent_buf {
	uint32_t const	*r_reqtime;
	uint16_t const	*r_clilen, *r_pathlen, *r_belen;
	char const	*r_cliaddr;
	uint8_t	const	*r_reqtype;
	char const	*r_path;
	uint16_t const	*r_status;
	char const	*r_beaddr;
	uint8_t	const	*r_cached;
	uint32_t const	*r_docsize;
};

const char *reqtypes[] = { "GET", "POST", "HEAD", "TRACE", "OPTIONS", "PURGE" };
const int nreqtypes = sizeof(reqtypes) / sizeof(*reqtypes);

packet_decoder::packet_decoder(bool dodns)
	: _dodns(dodns)
	, _acl4(AF_INET, "IPv4 ACL")
	, _acl6(AF_INET6, "IPv6 ACL")
{
}

bool
packet_decoder::decode(logent &e, sockaddr *src, socklen_t srclen,
		       char const *buf, char const *end)
{
char const	*bufp = buf;
const aclnode	*an;

	if (!_acl4.acllist.empty())
		if ((an = _acl4.match(src)) && (an->action == ACL_BLOCK))
			return false;

	if (!_acl6.acllist.empty())
		if ((an = _acl6.match(src)) && (an->action == ACL_BLOCK))
			return false;

#define GET_BYTES(s) 	if (bufp + (s) >= end) {	\
				return false;		\
			} else {			\
				bufp += (s);		\
			}
logent_buf	b;
	b.r_reqtime = reinterpret_cast<uint32_t const *>(bufp);	GET_BYTES(4);
	b.r_clilen  = reinterpret_cast<uint16_t const *>(bufp);	GET_BYTES(2);
	b.r_cliaddr = reinterpret_cast<char const *>(bufp);	GET_BYTES(*b.r_clilen);
	b.r_reqtype = reinterpret_cast<uint8_t const *>(bufp);	GET_BYTES(1);
	b.r_pathlen = reinterpret_cast<uint16_t const *>(bufp);	GET_BYTES(2);
	b.r_path    = reinterpret_cast<char const *>(bufp);	GET_BYTES(*b.r_pathlen);
	b.r_status  = reinterpret_cast<uint16_t const *>(bufp);	GET_BYTES(2);
	b.r_belen   = reinterpret_cast<uint16_t const *>(bufp);	GET_BYTES(2);
	b.r_beaddr  = reinterpret_cast<char const *>(bufp);	GET_BYTES(*b.r_belen);
	b.r_cached =  reinterpret_cast<uint8_t const *>(bufp);	GET_BYTES(1);
	if (buf + 4 >= end)
		return false;
	b.r_docsize = reinterpret_cast<uint32_t const *>(bufp);
	if (*b.r_reqtype >= nreqtypes)
		return false;

	e.r_reqtime = *b.r_reqtime;
	if (_dodns)
		e.r_cliaddr = resolve(string(b.r_cliaddr, *b.r_clilen));
	else
		e.r_cliaddr = string(b.r_cliaddr, *b.r_clilen);
	e.r_reqtype = reqtypes[*b.r_reqtype];
	e.r_path = string(b.r_path, *b.r_pathlen);
	e.r_status = *b.r_status;
	e.r_beaddr = string(b.r_beaddr, *b.r_belen);
	e.r_cached = *b.r_cached;
	e.r_docsize = *b.r_docsize;

	return true;
}

string
packet_decoder::resolve(string const &host)
{
addrinfo	hints;
char		hoststr[NI_MAXHOST + 1];

	struct cleanup {
		addrinfo	*res, *res2;
		~cleanup() {
			if (res)
				freeaddrinfo(res);
			if (res2)
				freeaddrinfo(res2);
		}
	} vars = {NULL, NULL};

	memset(&hints, 0, sizeof(hints));
	hints.ai_socktype = SOCK_STREAM;

	if (getaddrinfo(host.c_str(), "80", &hints, &vars.res) != 0)
		return host;
	if (getnameinfo(vars.res->ai_addr, vars.res->ai_addrlen, 
	    hoststr, sizeof(hoststr), NULL, 0, 0) != 0)
		return host;
	if (getaddrinfo(hoststr, "80", &hints, &vars.res2) != 0)
		return host;
	if (vars.res->ai_addrlen != vars.res2->ai_addrlen ||
	    memcmp(vars.res->ai_addr, vars.res2->ai_addr, vars.res->ai_addrlen))
		return host;

	return hoststr;
}

string const &
logent::fmt_reqtime(const char *fmt) const
{
static string	 ret;
static char	 timebuf[40];
static time_t	 lasttime;
tm		*atm;
	if (r_reqtime == lasttime)
		return ret;

	lasttime = r_reqtime;
	if (_usegmt)
		atm = gmtime(&lasttime);
	else	atm = localtime(&lasttime);
	strftime(timebuf, sizeof(timebuf), fmt, atm);
	ret = timebuf;
	return ret;
}

bool
packet_decoder::add_acl(string const &host)
{
string::size_type i;
string	rhost = host;
int	len = 0;

	if ((i = host.find('/')) != string::npos) {
		len = lexical_cast<int>(host.substr(i + 1));
		rhost = host.substr(0, i);
	}

	if (host.find('.') != string::npos) {
		if (!len)
			len = 32;
		if (_acl4.add(rhost.c_str(), (uint8_t) len, ACL_PASS, ACLFL_NONE) == false)
			return false;
	}
#ifdef AF_INET6 
	else {
		if (!len)
			len = 128;
		if (_acl6.add(rhost.c_str(), (uint8_t) len, ACL_PASS, ACLFL_NONE) == false)
			return false;
	}
#endif
	return true;
}

void
packet_decoder::usedns(bool v)
{
	_dodns = v;
}
