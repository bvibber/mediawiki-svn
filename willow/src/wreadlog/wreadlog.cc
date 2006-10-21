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

#include <netinet/in.h>

#include <arpa/inet.h>
#include <netdb.h>

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

#define IOV(s,l) do {	vecs[iovn].iov_base = (void *)s;	\
			vecs[iovn].iov_len = l;			\
			iovn++;					\
		} while (0)

void doprint_willow(logent &e)
{
	iovec	vecs[11];
	int	iovn = 0;
static	char	timebuf[25];
	int	timebufl;
static	time_t	lasttime;
	if (*e.r_reqtime != lasttime) {
	tm	*atm;
		lasttime = *e.r_reqtime;
		atm = gmtime(&lasttime);
		strftime(timebuf, sizeof timebuf, "[%Y-%m-%d %H:%M:%S] ", atm);
		timebufl = strlen(timebuf);
	}
	IOV(timebuf, timebufl);
	IOV(e.r_cliaddr, *e.r_clilen);
	IOV(" ", 1);
static const char *reqtypes[] = { "GET \"", "POST \"", "HEAD \"", "TRACE \"", "OPTIONS \"" };
	if (*e.r_reqtype >= sizeof(reqtypes) / sizeof(*reqtypes))
		return;
	IOV(reqtypes[*e.r_reqtype], strlen(reqtypes[*e.r_reqtype]));
	IOV(e.r_path, *e.r_pathlen);
	IOV("\" ", 2);
char	statstr[6];
	sprintf(statstr, "%d ", *e.r_status);
	IOV(statstr, strlen(statstr));
	IOV(e.r_beaddr, *e.r_belen);
	IOV(" ", 1);
	if (*e.r_cached)
		IOV("HIT", 3);
	else	IOV("MISS", 4);
	IOV("\n", 1);
	writev(1, vecs, sizeof (vecs) / sizeof(*vecs));
}

static void
doprint_clf(logent &e)
{
	iovec	vecs[8];
	int	iovn = 0;
static	char	timebuf[40];
	int	timebufl;
static	time_t	lasttime;
	if (*e.r_reqtime != lasttime) {
	tm	*atm;
		lasttime = *e.r_reqtime;
		atm = gmtime(&lasttime);
		strftime(timebuf, sizeof timebuf, " - - [%d/%b/%Y %H:%M:%S +0000] ", atm);
		timebufl = strlen(timebuf);
	}
	IOV(e.r_cliaddr, *e.r_clilen);
	IOV(timebuf, timebufl);
static const char *reqtypes[] = { "\"GET ", "\"POST ", "\"HEAD ", "\"TRACE ", "\"OPTIONS " };
	if (*e.r_reqtype >= sizeof(reqtypes) / sizeof(*reqtypes))
		return;
	IOV(reqtypes[*e.r_reqtype], strlen(reqtypes[*e.r_reqtype]));
	IOV(e.r_path, *e.r_pathlen);
	IOV(" HTTP/1.0\" ", 11);
char	tmpstr[16], tmpstr2[16];
int	tlen, tlen2;
	tlen = sprintf(tmpstr, "%d", *e.r_status);
	IOV(tmpstr, tlen);
	tlen2 = sprintf(tmpstr2, " %d", *e.r_docsize);
	IOV(tmpstr2, tlen2);
	IOV("\n", 1);
	writev(1, vecs, sizeof (vecs) / sizeof(*vecs));
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
		timebufl = sprintf(timebuf, "%ul.0 ", (unsigned long)*e.r_reqtime);
	IOV(timebuf, timebufl);
	IOV("     0 ", 7);	/* should be time to process request */
	IOV(e.r_cliaddr, *e.r_clilen);
	if (*e.r_cached)
		IOV(" TCP_HIT/", 9);
	else	IOV(" TCP_MISS/", 10);
char	tmpstr[16], tmpstr2[16];
int	tlen, tlen2;
	tlen = sprintf(tmpstr, "%d ", *e.r_status);
	IOV(tmpstr, tlen);
	tlen2 = sprintf(tmpstr2, "%d", *e.r_docsize);
	IOV(tmpstr2, tlen2);
static const char *reqtypes[] = { " GET ", " POST ", " HEAD ", " TRACE ", " OPTIONS " };
	if (*e.r_reqtype >= sizeof(reqtypes) / sizeof(*reqtypes))
		return;
	IOV(reqtypes[*e.r_reqtype], strlen(reqtypes[*e.r_reqtype]));
	IOV(e.r_path, *e.r_pathlen);
	IOV(" - PARENT_HIT/", 15);
	IOV(e.r_beaddr, *e.r_belen);
	IOV(" -", 2);	/* should be mime type */
	IOV("\n", 1);
	writev(1, vecs, sizeof (vecs) / sizeof(*vecs));
}

static void
wait_event(vector<int> &socks, int maxfd, fd_set &rfds)
{
vector<int>::iterator	it, end;
int	i;
	for (;;) {
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
	clilen = sizeof(cliaddr);
	bufp = buf;
	if ((n = recvfrom(sfd, buf, 65535, 0, (sockaddr *)&cliaddr, &clilen)) < 0) {
		perror("recvfrom");
		exit(8);
	}

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
"usage: %s [-46] [-p port] [-f format]\n"
"\t-4           listen on IPv4 socket\n"
"\t-6           listen on IPv6 socket\n"
"\t   (default: listen on  both)\n"
"\t-p <port>    listen on <port> (default 4445)\n"
"\t-f <format>  output logs in this format\n"
"\t             (one of: \"willow\" (default), \"clf\", \"squid\"\n"
		, progname);
}

int
main(int argc, char *argv[])
{
vector<int>	 sfds;
int		 i;
const char	*port = "4445";
struct sockaddr_in servaddr, cliaddr;
struct addrinfo hints, *res;
	memset(&hints, 0, sizeof(hints));
	doprint = doprint_willow;
	while ((i = getopt(argc, argv, "p:f:")) != -1) {
                switch (i) {
		case '4':
			hints.ai_family = AF_INET;
			break;
		case '6':
			hints.ai_family = AF_INET6;
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
		default:
			usage(argv[0]);
			return 1;
		}
	}
	argc -= optind;
	argv += optind;

	hints.ai_socktype = SOCK_DGRAM;
	if ((i = getaddrinfo(NULL, port, &hints, &res)) != 0) {
		fprintf(stderr, "%s: %s\n", port, gai_strerror(i));
		return 1;
	}

	for (addrinfo *r = res; r; r = r->ai_next) {
	int	sfd;
		if ((sfd = socket(r->ai_family, r->ai_socktype, r->ai_protocol)) == -1) {
			fprintf(stderr, "creating listening socket: %s\n", strerror(errno));
			continue;
		}
		if (bind(sfd, r->ai_addr, r->ai_addrlen) < 0) {
			perror("bind");
			continue;
		}
		sfds.push_back(sfd);
	}
	if (sfds.empty()) {
		fprintf(stderr, "cannot bind any listening sockets\n");
		return 1;
	}

	freeaddrinfo(res);

        ioloop(sfds);
        return 0;
}
