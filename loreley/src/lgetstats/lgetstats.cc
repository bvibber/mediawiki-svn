/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* wgetstats: Read statistics information from server.			*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id*/

#include "stdinc.h"

using std::fprintf;
using std::memset;
using std::strchr;
using std::strerror;
using std::string;
using std::exit;

#ifdef __INTEL_COMPILER
# pragma warning (disable: 383 981 1418)
#endif

string
fstraddr(string const &straddr, sockaddr const *addr, socklen_t len)
{
char	host[NI_MAXHOST];
char	port[NI_MAXSERV];
string	res;
	if (getnameinfo(addr, len, host, sizeof(host), port, sizeof(port), 
			     NI_NUMERICHOST | NI_NUMERICSERV) != 0)
		return "";
	return straddr + '[' + host + "]:" + port;
}

void
usage(const char *progname)
{
	fprintf(stderr, 
"usage: %s <hostname>[/port]\n", 
	progname);
}

int
main(int argc, char *argv[])
{
char		*progname = argv[0];
int		 i;
char		*host, *port;
const char	*pstr;
addrinfo	 hints, *res, *r;
int		 timeo = 10;
	while ((i = getopt(argc, argv, "")) != -1) {
		switch (i) {
		default:
			usage(progname);
			return 1;
		}
	}
	argc -= optind;
	argv += optind;

	if (!argv[0] || argv[1]) {
		usage(progname);
		return 1;
	}

	host = argv[0];
	if ((port = strchr(argv[0], '/')) != NULL) {
		*port++ = '\0';
		pstr = port;
	} else	pstr = "4446";

	memset(&hints, 0, sizeof(hints));
	hints.ai_socktype = SOCK_DGRAM;
	hints.ai_flags = AI_ADDRCONFIG;
	if ((i = getaddrinfo(host, pstr, &hints, &res)) != 0) {
		fprintf(stderr, "resolving %s:%s: %s\n", host, pstr, gai_strerror(i));
		return 1;
	}

int	sfd = -1;
char	sendbuf[2];
	sendbuf[0] = 1;	/* stats proto version */
	sendbuf[1] = 0;	/* get request */
timeval	tv;
	tv.tv_sec = timeo;
	tv.tv_usec = 0;
	for (r = res; r; r = r->ai_next) {
		if ((sfd = socket(r->ai_family, r->ai_socktype, r->ai_protocol)) == -1) {
			fprintf(stderr, "%s: %s\n", fstraddr(host, r->ai_addr, r->ai_addrlen).c_str(),
				strerror(errno));
			continue;
		}
		setsockopt(sfd, SOL_SOCKET, SO_RCVTIMEO, &tv, sizeof(tv));
		if (connect(sfd, r->ai_addr, r->ai_addrlen) < 0) {		
			fprintf(stderr, "%s: %s\n", fstraddr(host, r->ai_addr, r->ai_addrlen).c_str(),
				strerror(errno));
			continue;
		}
		if (send(sfd, sendbuf, 2, 0) < 0) {
			fprintf(stderr, "%s: %s\n", fstraddr(host, r->ai_addr, r->ai_addrlen).c_str(),
				strerror(errno));
			continue;
		}
		break;
	}
	if (sfd == -1)
		return 1;
char	rbuf[65535];
	if ((i = read(sfd, rbuf, sizeof (rbuf))) < 0) {
		if (errno == EWOULDBLOCK)
			fprintf(stderr, "%s: no reply received from server\n", 
				fstraddr(host, r->ai_addr, r->ai_addrlen).c_str());
		else
			fprintf(stderr, "%s: read: %s\n", fstraddr(host, r->ai_addr, r->ai_addrlen).c_str(),
				strerror(errno));
		return 1;
	}

	/*
	 * Stats format:
	 *   <version><treqok><treqfail><trespok><trespfail><reqoks><respoks>
	 *   <reqfails><respfails>
	 */

#define GET_BYTES(s) 	if (bufp + (s) >= end) {						\
				fprintf(stderr, "%s: reply from server too short\n",		\
					fstraddr(host, r->ai_addr, r->ai_addrlen).c_str());	\
				return 1;							\
			} else {								\
				bufp += (s);							\
			}
char	*bufp = rbuf, *end = rbuf + i;
char	*wvers;
uint32_t	*treqok, *treqfail, *trespok, *trespfail,
		*reqoks, *respoks, *reqfails, *respfails;
uint16_t	*wverslen, *repint;
uint8_t		*vers;
	vers 		= (uint8_t *)	bufp;	GET_BYTES(1);
	wverslen	= (uint16_t *)	bufp;	GET_BYTES(2);
	wvers		= (char *)	bufp;	GET_BYTES(*wverslen);
	repint		= (uint16_t *)	bufp;	GET_BYTES(2);
	treqok		= (uint32_t *)	bufp;	GET_BYTES(4);
	treqfail	= (uint32_t *)	bufp;	GET_BYTES(4);
	trespok		= (uint32_t *)	bufp;	GET_BYTES(4);
	trespfail	= (uint32_t *)	bufp;	GET_BYTES(4);
	reqoks		= (uint32_t *)	bufp;	GET_BYTES(4);
	respoks		= (uint32_t *)	bufp;	GET_BYTES(4);
	reqfails	= (uint32_t *)	bufp;	GET_BYTES(4);
	respfails	= (uint32_t *)	bufp;	GET_BYTES(3);

	if (*vers != 1) {
		fprintf(stderr,
	     "cannot decode this statistics format version (%d, expected 1)\n",
			(int) *vers);
		exit(1);
	}

	fprintf(stderr, "%s (Loreley %.*s), report interval %d seconds:\n", host, 
		(int)*wverslen, wvers, (int) *repint);
	fprintf(stderr, "\tTotal requests served: % 10lu (% 6d/sec)  Errors: % 6lu (% 6d/sec)\n",
		(unsigned long) *treqok, (int) *reqoks, (unsigned long) *treqfail, (int) *reqfails);
	fprintf(stderr, "\tBackend requests:      % 10lu (% 6d/sec) Invalid: % 6lu (% 6d/sec)\n",
		(unsigned long) *trespok, (int) *respoks, (unsigned long) *trespfail, (int) *respfails);
	
	/*
	 * Now there is a series of (listener,nconns) pairs.
	 */
	bufp++;
	for (;;) {
	uint16_t	 nlen;
	char		*name;
	uint32_t	 hi, lo;
	uint64_t	 nc;
		if (bufp + 2 > end)
			break;
		nlen = *(uint16_t *) bufp;
		bufp += 2;
		if (bufp + nlen > end) {
			fprintf(stderr, 
			"%s: warning: truncated packet? (no name, nlen = %d)\n",
				progname, (int) nlen);
			break;
		}
		name = bufp;
		bufp += nlen;
		if (bufp + 8 > end) {
			fprintf(stderr, "%s: warning: truncated packet? (no nc)\n",
				progname);
			break;
		}
		lo = *(uint32_t *) bufp;
		bufp += 4;
		hi = *(uint32_t *) bufp;		
		bufp += 4;
		nc = (uint64_t)hi << 32 | lo;
		fprintf(stderr, "\tListener %.*s: %llu connections\n",
			nlen, name, nc);
	}

	freeaddrinfo(res);
}
