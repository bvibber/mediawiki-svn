/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wreadlog: Read UDP log packets and print human-readable log.
 */


#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/select.h>
#include <sys/stat.h>

#include <netinet/in.h>

#include <arpa/inet.h>
#include <netdb.h>
#include <fcntl.h>

#include <cerrno>
#include <cstdio>
#include <unistd.h>
#include <cstdlib>
#include <cstring>
#include <ctime>
#include <algorithm>
#include <vector>
using std::strftime;
using std::strlen;
using std::fprintf;
using std::sprintf;
using std::max;
using std::vector;
using std::strcpy;
using std::strchr;

#include "acl.h"

static acl acl4(AF_INET, "IPv4 ACL")
#ifdef AF_INET6
	, acl6(AF_INET6, "IPv6 ACL")
#endif
	;

FILE *outfile = stdout;

static void handle_packet(int fd);

struct logent {
	uint32_t	*r_reqtime;
	uint32_t	*r_clilen, *r_pathlen, *r_belen;
	char		*r_cliaddr;
	uint8_t		*r_reqtype;
	char		*r_path;
	uint16_t	*r_status;
	char		*r_beaddr;
	uint8_t		*r_cached;
	uint32_t	*r_docsize;
};

static void (*doprint) (logent &);
static void doprint_willow (logent &);
static void doprint_clf (logent &);
static void doprint_squid (logent &);

void doprint_willow(logent &e)
{
static	char	timebuf[25];
static	time_t	lasttime;
	if (*e.r_reqtime != lasttime) {
	tm	*atm;
		lasttime = *e.r_reqtime;
		atm = gmtime(&lasttime);
		strftime(timebuf, sizeof timebuf, "[%Y-%m-%d %H:%M:%S]", atm);
	}
static const char *reqtypes[] = { "GET", "POST", "HEAD", "TRACE", "OPTIONS" };
	if (*e.r_reqtype >= sizeof(reqtypes) / sizeof(*reqtypes))
		return;
	fprintf(outfile, "%s %.*s %s \"%.*s\" %d %.*s %s\n", timebuf, (int)*e.r_clilen, e.r_cliaddr,
		reqtypes[*e.r_reqtype], (int)*e.r_pathlen, e.r_path, (int)*e.r_status,
		(int)*e.r_belen, e.r_beaddr, *e.r_cached ? "HIT" : "MISS");
}

static void
doprint_clf(logent &e)
{
static	char	timebuf[40];
static	time_t	lasttime;
	if (*e.r_reqtime != lasttime) {
	tm	*atm;
		lasttime = *e.r_reqtime;
		atm = gmtime(&lasttime);
		strftime(timebuf, sizeof timebuf, "- - [%d/%b/%Y %H:%M:%S +0000]", atm);
	}
static const char *reqtypes[] = { "GET", "POST", "HEAD", "TRACE", "OPTIONS" };
	if (*e.r_reqtype >= sizeof(reqtypes) / sizeof(*reqtypes))
		return;
	fprintf(outfile, "%.*s %s \"%s %.*s HTTP/1.0\" %d %lu\n", (int)*e.r_clilen, e.r_cliaddr, timebuf, 
		reqtypes[*e.r_reqtype], (int)*e.r_pathlen, e.r_path,
		(int)*e.r_status, (unsigned long)*e.r_docsize);
} 

static void
doprint_squid(logent &e)
{
	iovec	vecs[12];
	int	iovn = 0;
static	char	timebuf[16];
static	int	timebufl;
static	time_t	lasttime;
	if (*e.r_reqtime != lasttime)
		sprintf(timebuf, "%ul.0", (unsigned long)*e.r_reqtime);
static const char *reqtypes[] = { "GET", "POST", "HEAD", "TRACE", "OPTIONS" };
	if (*e.r_reqtype >= sizeof(reqtypes) / sizeof(*reqtypes))
		return;
	fprintf(outfile, "%s      0 %.*s TCP_%s/%d %lu %s %.*s - ", timebuf,
		(int)*e.r_clilen, e.r_cliaddr, *e.r_cached ? "HIT" : "MISS",
		(int)*e.r_status, (unsigned long)*e.r_docsize,
		reqtypes[*e.r_reqtype], (int)*e.r_pathlen, e.r_path);
	if (!*e.r_cached)
		fprintf(outfile, "PARENT_HIT/%.*s -\n", (int)*e.r_belen, e.r_beaddr);
	else	fprintf(outfile, "NONE/- -\n");
}

static void
wait_event(vector<int> &socks, int maxfd, fd_set &rfds)
{
vector<int>::iterator	it, end;
int	i;
	for (;;) {
		bzero(&rfds, sizeof(rfds));
		FD_ZERO(&rfds);
		for (it = socks.begin(), end = socks.end(); it != end; ++it)
			FD_SET(*it, &rfds);
		if (select(maxfd + 1, &rfds, NULL, NULL, NULL) <1) {
			if (errno == EINTR)
				continue;
			fprintf(stderr, "select: %s\n", strerror(errno));
			exit(1);
		}
		return;
	}
}

static void
ioloop(vector<int> &socks)
{
int	maxfd;
fd_set	rfds;
vector<int>::iterator	it, end;

	for (it = socks.begin(), end = socks.end(); it != end; ++it)
		maxfd = max(maxfd, *it);

	for (;;) {
		wait_event(socks, maxfd, rfds);
		for (it = socks.begin(), end = socks.end(); it != end; ++it)
			if (FD_ISSET(*it, &rfds))
				handle_packet(*it);
	}
}

static void
handle_packet(int sfd)
{
sockaddr_storage	cliaddr;
socklen_t		clilen;
int	n, i, nlen;
char	buf[65535], *end = buf + sizeof(buf), *bufp = buf;
const aclnode	*an;
	clilen = sizeof(cliaddr);
	bufp = buf;
	if ((n = recvfrom(sfd, buf, 65535, 0, (sockaddr *)&cliaddr, &clilen)) < 0) {
		perror("recvfrom");
		exit(8);
	}

	if (((an = acl4.match((sockaddr *)&cliaddr)) && (an->action == ACL_BLOCK))
	    || ((an = acl6.match((sockaddr *)&cliaddr)) && (an->action == ACL_BLOCK)))
		return;

#define GET_BYTES(s) 	if (bufp + (s) >= end) {	\
				return;			\
			} else {			\
				bufp += (s);		\
			}
logent	e;
	e.r_reqtime = (uint32_t *) bufp;	GET_BYTES(4);
	e.r_clilen  = (uint32_t *) bufp;	GET_BYTES(4);
	e.r_cliaddr = (char *)     bufp;	GET_BYTES(*e.r_clilen);
	e.r_reqtype = (uint8_t *)  bufp;	GET_BYTES(1);
	e.r_pathlen = (uint32_t *) bufp;	GET_BYTES(4);
	e.r_path    = (char *)     bufp;	GET_BYTES(*e.r_pathlen);
	e.r_status  = (uint16_t *) bufp;	GET_BYTES(2);
	e.r_belen   = (uint32_t *) bufp;	GET_BYTES(4);
	e.r_beaddr  = (char *)     bufp;	GET_BYTES(*e.r_belen);
	e.r_cached =  (uint8_t *)  bufp;	GET_BYTES(1);
	if (buf + 4 >= end)
		return;
	e.r_docsize = (uint32_t *)bufp;
	doprint(e);
}

void
usage(const char *progname)
{
	fprintf(stderr,
"usage: %s [-46] [-a addr] [-p port] [-s mask] [-f format]\n"
"\t-4           listen on IPv4 socket\n"
"\t-6           listen on IPv6 socket\n"
"\t           (default: listen on both)\n"
"\t-a <addr>    listen only on this address\n"
"\t           (default: all addresses)\n"
"\t-p <port>    listen on <port>\n"
"\t           (default: 4445)\n"
"\t-s <mask>    only allow queries from this source IP, specified as\n"
"\t             single IP address or CIDR mask (127.0.0.0/8, 2000::/3)\n"
"\t             this option can be specified multiple times\n"
"\t           (default: allow all addresses)\n"
"\t-f <format>  output logs in this format\n"
"\t           (one of: \"willow\" (default), \"clf\", \"squid\")\n"
"\t-F <file>    write to this file instead of stdout\n"
"\t-d           become a daemon after startup\n"
		, progname);
}

int
main(int argc, char *argv[])
{
vector<int>	 sfds;
int		 i;
const char	*port = "4445", *host = NULL;
char		*progname = argv[0];
struct sockaddr_in servaddr, cliaddr;
struct addrinfo hints, *res;
bool		 daemon = false;
	memset(&hints, 0, sizeof(hints));
	doprint = doprint_willow;
	while ((i = getopt(argc, argv, "h46a:p:f:s:F:d")) != -1) {
                switch (i) {
		case '4':
			hints.ai_family = AF_INET;
			break;
		case '6':
			hints.ai_family = AF_INET6;
			break;
		case 'd':
			daemon = true;
			break;
		case 'p':
			port = optarg;
			break;
		case 'f':
			if (!strcmp(optarg, "willow"))
				doprint = doprint_willow;
			else if (!strcmp(optarg, "clf"))
				doprint = doprint_clf;
			else if (!strcmp(optarg, "squid"))
				doprint = doprint_squid;
			else {
				fprintf(stderr, "unrecognised log format \"%s\"\n", optarg);
				usage(argv[0]);
				return 1;
			}
			break;
		case 'a':
			host = optarg;
			break;
		case 'h':
			usage(argv[0]);
			return 0;
		case 's': {
		char	*lenp, *p = new char[strlen(optarg) + 1];
		int	len = 0;
			strcpy(p, optarg);
			if ((lenp = strchr(p, '/')) != NULL) {
				*lenp++ = '\0';
				len = atoi(lenp);
			}
			if (strchr(p, '.')) {
				if (!len)
					len = 32;
				if (acl4.add(p, len, ACL_PASS, ACLFL_NONE) == false) {
					fprintf(stderr, "%s: could not parse IP mask: %s\n", argv[0], optarg);
					return 1;
				}
			}
#ifdef AF_INET6 
			else {
				if (!len)
					len = 128;
				if (acl6.add(p, len, ACL_PASS, ACLFL_NONE) == false) {
					fprintf(stderr, "%s: could not parse IP mask: %s\n", argv[0], optarg);
					return 1;
				}
			}
#endif
			delete[] p;
			break;
		}
		case 'F':
			if ((outfile = fopen(optarg, "a")) == NULL) {
				fprintf(stderr, "%s: %s: %s\n", argv[0], optarg, strerror(errno));
				return 1;
			}
			break;
		default:
			usage(argv[0]);
			return 1;
		}
	}
	argc -= optind;
	argv += optind;

	if (!acl4.acllist.empty() || !acl6.acllist.empty()) {
		acl4.add("0.0.0.0", 0, ACL_BLOCK, ACLFL_NONE);
		acl6.add("::", 0, ACL_BLOCK, ACLFL_NONE);
	}

	hints.ai_socktype = SOCK_DGRAM;
	if ((i = getaddrinfo(host, port, &hints, &res)) != 0) {
		if (host)
			fprintf(stderr, "%s: resolving [%s]:%s: %s\n", progname, host, port, gai_strerror(i));
		else
			fprintf(stderr, "%s: resolving %s: %s\n", progname, port, gai_strerror(i));
		return 1;
	}

	for (addrinfo *r = res; r; r = r->ai_next) {
	int	sfd;
		if ((sfd = socket(r->ai_family, r->ai_socktype, r->ai_protocol)) == -1) {
			if (host)
				fprintf(stderr, "%s: binding to [%s]:%s: %s\n", 
					progname, host, port, strerror(errno));
			else
				fprintf(stderr, "%s: binding to %s: %s\n",
					progname, port, strerror(errno));
			continue;
		}
		if (bind(sfd, r->ai_addr, r->ai_addrlen) < 0) {
			if (host)
				fprintf(stderr, "%s: binding to [%s]:%s: %s\n",
					progname, host, port, strerror(errno));
			else
				fprintf(stderr, "%s: binding to %s: %s\n",
					progname, port, strerror(errno));
			continue;
		}
		sfds.push_back(sfd);
	}
	if (sfds.empty()) {
		fprintf(stderr, "Could not create any listening sockets\n");
		return 1;
	}

	freeaddrinfo(res);

	if (daemon) {
		switch (fork()) {
		case -1:
			fprintf(stderr, "%s: cannot fork: %s\n", progname, strerror(errno));
			return 1;
		case 0:
			break;
		default:
			_exit(0);
		}
		if (setsid() == -1)
			return 1;
		chdir("/");
	int	fd;
		if ((fd = open("/dev/null", O_RDWR, 0)) != -1) {
			dup2(fd, 0);
			dup2(fd, 1);
			dup2(fd, 2);
			close(fd);
		}
	}

        ioloop(sfds);
        return 0;
}
