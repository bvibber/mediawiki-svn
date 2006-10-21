/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wreadlog: Read UDP log packets and print human-readable log.
 */


#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id: wnet.cc 17121 2006-10-20 03:35:35Z river $"
#endif

#include <sys/types.h>
#include <sys/socket.h>

#include <netinet/in.h>

#include <arpa/inet.h>
#include <netdb.h>

#include <cerrno>
#include <cstdio>
#include <unistd.h>
#include <cstdlib>
#include <cstring>
#include <ctime>
using std::strftime;
using std::strlen;
using std::fprintf;

void
ioloop(int sfd)
{
sockaddr_storage	cliaddr;
socklen_t		clilen;
int	n, len;
char	buf[65535], *end = buf + sizeof(buf), *bufp = buf;

	for (;;) {
		clilen = sizeof(cliaddr);
		bufp = buf;
		if ((n = recvfrom(sfd, buf, 65535, 0, (sockaddr *)&cliaddr, &clilen)) < 0) {
			perror("recvfrom");
			exit(8);
		}

#define GET_BYTES(s) 	if (bufp + (s) >= end) {	\
				printf("not enough data (got %d, read %d, want %d)\n",n,bufp - buf, s);\
				continue;		\
			} else {			\
				bufp += (s);		\
			}
	uint32_t	*r_reqtime;
	uint32_t	*r_clilen, *r_pathlen, *r_belen;
	char		*r_cliaddr;
	uint8_t		*r_reqtype;
	char		*r_path;
	uint16_t	*r_status;
	char		*r_beaddr;
	uint8_t		*r_cached;
		r_reqtime = (uint32_t *) bufp;	GET_BYTES(4);
		r_clilen  = (uint32_t *) bufp;	GET_BYTES(4);
		r_cliaddr = (char *)     bufp;	GET_BYTES(*r_clilen);
		r_reqtype = (uint8_t *)  bufp;	GET_BYTES(1);
		r_pathlen = (uint32_t *) bufp;	GET_BYTES(4);
		r_path    = (char *)     bufp;	GET_BYTES(*r_pathlen);
		r_status  = (uint16_t *) bufp;	GET_BYTES(2);
		r_belen   = (uint32_t *) bufp;	GET_BYTES(4);
		r_beaddr  = (char *)     bufp;	GET_BYTES(*r_belen);
		if (buf + 1 >= end)
			continue;
		r_cached = (uint8_t *)bufp;
	iovec	vecs[11];
	int	iovn = 0;
static	char	timebuf[25];
	int	timebufl;
static	time_t	lasttime, now;
		time(&now);
		if (now != lasttime) {
		tm	*atm;
			atm = gmtime(&now);
			lasttime = now;
			strftime(timebuf, sizeof timebuf, "[%Y-%m-%d %H:%M:%S] ", atm);
			timebufl = strlen(timebuf);
		}
#define IOV(s,l) do {	vecs[iovn].iov_base = (void *)s;	\
			vecs[iovn].iov_len = l;			\
			iovn++;					\
		} while (0)
		IOV(timebuf, timebufl);
		IOV(r_cliaddr, *r_clilen);
		IOV(" ", 1);
static const char *reqtypes[] = { "GET \"", "POST \"", "HEAD \"", "TRACE \"", "OPTIONS \"" };
		if (*r_reqtype >= sizeof(reqtypes) / sizeof(*reqtypes))
			continue;
		IOV(reqtypes[*r_reqtype], strlen(reqtypes[*r_reqtype]));
		IOV(r_path, *r_pathlen);
		IOV("\" ", 2);
	char	statstr[6];
		sprintf(statstr, "%d ", *r_status);
		IOV(statstr, strlen(statstr));
		IOV(r_beaddr, *r_belen);
		IOV(" ", 1);
		if (*r_cached)
			IOV("HIT", 3);
		else	IOV("MISS", 4);
		IOV("\n", 1);
		writev(1, vecs, sizeof (vecs) / sizeof(*vecs));
	}
}

void
usage(const char *progname)
{
	fprintf(stderr,
"usage: %s [-46] [-p port] [-f format]\n"
"\t-4           listen on IPv4 socket\n"
"\t-6           listen on IPv6 socket\n"
"\t-p <port>    listen on <port> (default 4445)\n"
"\t-f <format>  output logs in this format\n"
"\t             (one of: \"clf\", \"willow\"\n"
		, progname);
}

int
main(int argc, char *argv[])
{
int		 sfd;
int		 i;
const char	*port = "4445";
struct sockaddr_in servaddr, cliaddr;
struct addrinfo hints, *res;
	memset(&hints, 0, sizeof(hints));
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

	if ((sfd = socket(res->ai_family, res->ai_socktype, res->ai_protocol)) == -1) {
		fprintf(stderr, "creating listening socket: %s\n", strerror(errno));
		return 1;
	}
	
        if (bind(sfd, res->ai_addr, res->ai_addrlen) < 0) {
                perror("bind");
                exit(8);
        }

        ioloop(sfd);
        return 0;
}
