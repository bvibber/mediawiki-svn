/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* ifname_to_address: convert an interface name to a sockaddr		*/
/* Copyright (c) 2006 River Tarnell <river@attenuate.org>.              */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

/*
 * On Solaris, the header required for this function, <net/if.h>, defines
 * a struct ::map which conflicts with "using std::map".  Rather than
 * change all uses of map in Loreley, we define a function here which
 * performs the required function without including <map>.
 */

#define BSD_COMP

#include <string>
#include <cstring>

#include <sys/socket.h>
#include <sys/ioctl.h>
#include <net/if.h>
#include <netinet/in.h>

int
ifname_to_address(int s, sockaddr_in *addr, char const *ifname)
{
ifreq   ifr;
	std::memset(&ifr, 0, sizeof(ifr));
	std::strncpy(ifr.ifr_name, ifname, IFNAMSIZ);
	if (ioctl(s, SIOCGIFADDR, &ifr) < 0)
		return -1;
	std::memcpy(addr, &ifr.ifr_addr, sizeof(sockaddr_in));
	return 0;
}	

unsigned int
if_nametoindex_wrap(const char *ifname)
{
	return if_nametoindex(ifname);
}


