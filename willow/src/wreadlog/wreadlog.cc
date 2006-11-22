/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wreadlog: Read UDP log packets and print human-readable log.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#define _XOPEN_SOURCE 500
#define __EXTENSIONS__

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
#include <memory>
#include <map>
#include <set>
#include <iostream>
using std::strftime;
using std::strlen;
using std::fprintf;
using std::sprintf;
using std::max;
using std::min;
using std::vector;
using std::strcpy;
using std::strchr;
using std::auto_ptr;
using std::exit;
using std::memset;
using std::atoi;
using std::memcpy;
using std::memcmp;
using std::strerror;
using std::perror;
using std::FILE;
using std::strcmp;
using std::map;
using std::multiset;
using std::cout;

#include <boost/multi_index_container.hpp>
#include <boost/multi_index/identity.hpp>
#include <boost/multi_index/member.hpp>
#include <boost/multi_index/ordered_index.hpp>
#include <boost/multi_index/tag.hpp>
#include <boost/format.hpp>
#include <boost/ref.hpp>
using boost::cref;
using boost::reference_wrapper;
using boost::format;
using boost::io::str;
using boost::multi_index_container;
using boost::multi_index::indexed_by;
using boost::multi_index::ordered_unique;
using boost::multi_index::ordered_non_unique;
using boost::multi_index::member;
using boost::multi_index::identity;
using boost::multi_index::tag;

#include <curses.h>

#ifdef __INTEL_COMPILER
# pragma warning (disable: 1418 383 981)
#endif

#include "acl.h"

namespace {
struct top_url {
	uint32_t	count;
	uint64_t	size;
	string		url;
	int		status;
	bool		cached;
};

struct url{};
struct count{};
typedef multi_index_container<top_url,
	indexed_by<
		ordered_unique<tag<url>,
			member<top_url, string, &top_url::url> >,
		ordered_non_unique<tag<count>,
			member<top_url, uint32_t, &top_url::count> >
	>
> url_set;

typedef vector<reference_wrapper<top_url const> > toplist;

url_set	top_urls;

void
url_hit(string const &url_, uint64_t size, int status, bool cached)
{
url_set::index<url>::type::const_iterator
	it = top_urls.get<url>().find(url_)
,
	end = top_urls.get<url>().end();

	if (it == top_urls.get<url>().end()) {
	top_url	t;
		t.count = 1;
		t.url = url_;
		t.size = size;
		t.status = status;
		t.cached = cached;
		top_urls.insert(t);
		return;
	}

top_url	n = *it;
	n.count++;
	n.size = size;
	n.status = status;
	n.cached = cached;
	top_urls.replace(it, n);
}

toplist
get_topn(int n)
{
toplist	ret;
url_set::index<count>::type::const_reverse_iterator 
	it = top_urls.get<count>().rbegin(),
	end = top_urls.get<count>().rend();

	for (; it != end && n--; ++it)
		ret.push_back(cref(*it));
	return ret;
}

bool Tflag;
int interval = 5;
FILE *outfile = stdout;
void handle_packet(int fd);
const char *reqtypes[] = { "GET", "POST", "HEAD", "TRACE", "OPTIONS", "PURGE" };
const int nreqtypes = sizeof(reqtypes) / sizeof(*reqtypes);
bool usegmt = true, dodns = true;

static acl acl4(AF_INET, "IPv4 ACL")
#ifdef AF_INET6
	, acl6(AF_INET6, "IPv6 ACL")
#endif
	;

struct logent {
	uint32_t	*r_reqtime;
	uint16_t	*r_clilen, *r_pathlen, *r_belen;
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

const char *
fmttime(time_t when, const char *fmt)
{
static char	 timebuf[40];
static time_t	 lasttime;
tm		*atm;
	if (when == lasttime)
		return timebuf;
	lasttime = when;
	if (usegmt)
		atm = gmtime(&lasttime);
	else	atm = localtime(&lasttime);
	strftime(timebuf, sizeof(timebuf), fmt, atm);
	return timebuf;
}

static const char *
resolve(const char *host_, int hostlen)
{
addrinfo	hints, *res = NULL, *res2 = NULL;
static char	hoststr[NI_MAXHOST + 1];
vector<char>	host(hostlen + 1);
	if (!dodns)
		goto err;
	if (hostlen > NI_MAXHOST)
		goto err;

	memcpy(&host[0], host_, hostlen);
	host[hostlen] = '\0';

	memset(&hints, 0, sizeof(hints));
	hints.ai_socktype = SOCK_STREAM;

	if (getaddrinfo(&host[0], "80", &hints, &res) != 0)
		goto err;
	if (getnameinfo(res->ai_addr, res->ai_addrlen, hoststr, sizeof(hoststr), NULL, 0, 0) != 0)
		goto err;
	if (getaddrinfo(hoststr, "80", &hints, &res2) != 0)
		goto err;
	if (memcmp(res->ai_addr, res2->ai_addr, min(res->ai_addrlen, res2->ai_addrlen)))
		goto err;
	return hoststr;
err:	if (res)
		freeaddrinfo(res);
	if (res2)
		freeaddrinfo(res2);
	strcpy(hoststr, host_);
	return hoststr;
}

void doprint_willow(logent &e)
{
	fprintf(outfile, "[%s] %s %s \"%.*s\" %lu %d %.*s %s\n", fmttime(*e.r_reqtime, "%Y-%m-%d %H:%M:%S"), 
		resolve(e.r_cliaddr, *e.r_clilen),
		reqtypes[*e.r_reqtype], (int)*e.r_pathlen, e.r_path, 
		(unsigned long)*e.r_docsize, (int)*e.r_status,
		(int)*e.r_belen, e.r_beaddr, *e.r_cached ? "HIT" : "MISS");
}

static void
doprint_clf(logent &e)
{
	fprintf(outfile, "%s - - [%s] \"%s %.*s HTTP/1.0\" %d %lu\n",
		resolve(e.r_cliaddr, *e.r_clilen),
		fmttime(*e.r_reqtime, "%d/%b/%Y %H:%M:%S %z"), 
		reqtypes[*e.r_reqtype], (int)*e.r_pathlen, e.r_path,
		(int)*e.r_status, (unsigned long)*e.r_docsize);
} 

static void
doprint_squid(logent &e)
{
	fprintf(outfile, "%lu.0      0 %s TCP_%s/%d %lu %s %.*s - ", (unsigned long)*e.r_reqtime,
		resolve(e.r_cliaddr, *e.r_clilen), *e.r_cached ? "HIT" : "MISS",
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
	for (;;) {
		memset(&rfds, 0, sizeof(rfds));
		FD_ZERO(&rfds);
		for (it = socks.begin(), end = socks.end(); it != end; ++it)
			FD_SET(*it, &rfds);
		if (select(maxfd + 1, &rfds, NULL, NULL, NULL) < 1) {
			if (errno == EINTR)
				continue;
			fprintf(stderr, "select: %s\n", strerror(errno));
			exit(1);
		}
		return;
	}
}

void
ioloop(vector<int> &socks)
{
int	maxfd = 0;
fd_set	rfds;
vector<int>::iterator	it, end;

	for (it = socks.begin(), end = socks.end(); it != end; ++it)
		maxfd = max(maxfd, *it);

	if (Tflag)
		initscr();

	for (;;) {
		wait_event(socks, maxfd, rfds);
		for (it = socks.begin(), end = socks.end(); it != end; ++it)
			if (FD_ISSET(*it, &rfds))
				handle_packet(*it);
	}
}

void
handle_packet(int sfd)
{
sockaddr_storage	cliaddr;
socklen_t		clilen;
int	n;
char	buf[65535], *end, *bufp = buf;
const aclnode	*an;
static time_t lastprint;
	clilen = sizeof(cliaddr);
	bufp = buf;
	if ((n = recvfrom(sfd, buf, 65535, 0, (sockaddr *)&cliaddr, &clilen)) < 0) {
		perror("recvfrom");
		exit(8);
	}

	end = buf + n;

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
	e.r_clilen  = (uint16_t *) bufp;	GET_BYTES(2);
	e.r_cliaddr = (char *)     bufp;	GET_BYTES(*e.r_clilen);
	e.r_reqtype = (uint8_t *)  bufp;	GET_BYTES(1);
	e.r_pathlen = (uint16_t *) bufp;	GET_BYTES(2);
	e.r_path    = (char *)     bufp;	GET_BYTES(*e.r_pathlen);
	e.r_status  = (uint16_t *) bufp;	GET_BYTES(2);
	e.r_belen   = (uint16_t *) bufp;	GET_BYTES(2);
	e.r_beaddr  = (char *)     bufp;	GET_BYTES(*e.r_belen);
	e.r_cached =  (uint8_t *)  bufp;	GET_BYTES(1);
	if (buf + 4 >= end)
		return;
	e.r_docsize = (uint32_t *)bufp;
	if (*e.r_reqtype >= nreqtypes)
		return;

	if (Tflag) {
		if (!lastprint)
			time(&lastprint);

		url_hit(string(e.r_path, *e.r_pathlen), *e.r_docsize,
			*e.r_status, *e.r_cached);

		if (lastprint + 5 <= time(0)) {
		toplist	urls;
		int	 i = 2;
		char	 timestr[64];
		time_t	 now;
		tm	*tm;
			time(&now);
			tm = localtime(&now);
			strftime(timestr, sizeof(timestr), 
				"%a, %d %b %Y %H:%M:%S GMT", tm);
			urls = get_topn(LINES - 2);
			clear();
			move(0, 0);
			addstr(timestr);
			move(1, 0);
			addstr("    # Hits  Cached       Size  URL");
			move(2, 0);
			for (toplist::iterator it = urls.begin(),
			     end = urls.end(); it != end; ++it) {
			top_url const	&u = it->get();
				addstr(str(format("%10d  ") % u.count).c_str());
				if (u.cached)
					addstr("   YES  ");
				else
					addstr("    NO  ");
				addstr(str(format("%9d  ") % u.size).c_str());
				addstr(u.url.c_str());
				move(i + 1, 0);
				++i;
			}
			refresh();
			time(&lastprint);
		}
	} else {
		doprint(e);
	}
}

void
usage(const char *progname)
{
	fprintf(stderr,
"usage: %1$s [-46dn] [-F file] [-a addr] [-p port] [-s mask] [-f format]\n"
"usage: %1$s -T [-46] [-a addr] [-p port] [-s mask] [interval]\n"
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
"\t-L           print timestamps in local time, not GMT\n"
"\t-n           don't try to resolve client IPs to names\n"
"\t-T           produce top-like output showing most requested URLs,\n"
"\t             outputting every [interval] seconds (default 5)\n"
		, progname);
}

} // anonymous namespace

int
main(int argc, char *argv[])
{
vector<int>	 sfds;
int		 i;
const char	*port = "4445", *host = NULL;
char		*progname = argv[0];
struct addrinfo hints, *res;
bool		 daemon = false;
	memset(&hints, 0, sizeof(hints));
	doprint = doprint_willow;
	while ((i = getopt(argc, argv, "h46a:p:f:s:F:dLnT")) != -1) {
                switch (i) {
		case '4':
			hints.ai_family = AF_INET;
			break;
		case '6':
			hints.ai_family = AF_INET6;
			break;
		case 'n':
			dodns = false;
			break;
		case 'd':
			daemon = true;
			break;
		case 'L':
			usegmt = false;
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
				if (acl4.add(p, (uint8_t) len, ACL_PASS, ACLFL_NONE) == false) {
					fprintf(stderr, "%s: could not parse IP mask: %s\n", argv[0], optarg);
					return 1;
				}
			}
#ifdef AF_INET6 
			else {
				if (!len)
					len = 128;
				if (acl6.add(p, (uint8_t) len, ACL_PASS, ACLFL_NONE) == false) {
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
		case 'T':
			Tflag = true;
			break;

		default:
			usage(argv[0]);
			return 1;
		}
	}
	argc -= optind;
	argv += optind;

	if (Tflag && ((outfile != stdout) || daemon)) {
		fprintf(stderr, "%s: -T cannot be used with -F or -d\n",
			progname);
		usage(progname);
		return 1;
	}

	if (argc) {
		usage(progname);
		return 1;
	}

	if (!acl4.acllist.empty() || !acl6.acllist.empty()) {
		acl4.add("0.0.0.0", 0, ACL_BLOCK, ACLFL_NONE);
		acl6.add("::", 0, ACL_BLOCK, ACLFL_NONE);
	}

	hints.ai_socktype = SOCK_DGRAM;
	hints.ai_flags |= AI_PASSIVE;

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
