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
using std::ofstream;
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
using std::strcmp;
using std::map;
using std::multiset;
using std::cout;
using std::ios;
using std::max_element;
using std::exception;

using boost::format;
using boost::io::str;
using boost::shared_ptr;
using boost::assign::map_list_of;
using boost::lexical_cast;
using boost::bad_lexical_cast;

#ifdef __INTEL_COMPILER
# pragma warning (disable: 1418 383 981)
#endif

#include "acl.h"
#include "printer.h"
#include "packet_decoder.h"

namespace {

shared_ptr<packet_decoder> decoder;
shared_ptr<entry_printer> printer;

bool usegmt = true;
void handle_packet(int fd);

struct packet {
	char			buf[65535];
	ssize_t			len;
	sockaddr_storage	cliaddr;
	socklen_t		clilen;
};

struct select_timeout : exception {
};

struct packet_receiver {
	vector<int> _socks;

	int num_fds(void) const {
		return _socks.size();
	}

	void add_sock(int s) {
		_socks.push_back(s);
	}

	vector<int>	wait_event(int timeo);
	packet		receive(int timeo);
};

packet
packet_receiver::receive(int timeo)
{
int	sock;
packet	p;
vector<int>		fds;
vector<int>::iterator	it, end;

	fds = wait_event(timeo);
	assert(!fds.empty());

	sock = *fds.begin();

	p.clilen = sizeof(p.cliaddr);
	if ((p.len = recvfrom(sock, p.buf, sizeof(p.buf), 0,
	     (sockaddr *)&p.cliaddr, &p.clilen)) < 0) {
		perror("recvfrom");
		exit(8);
	}

	return p;
}

vector<int>
packet_receiver::wait_event(int timeo)
{
vector<int>::iterator	it, end;
int		i, maxfd;
timeval		tv;
fd_set		rfds;
vector<int>	ret;
	maxfd = *max_element(_socks.begin(), _socks.end());

	for (;;) {
		memset(&rfds, 0, sizeof(rfds));
		FD_ZERO(&rfds);
		for (it = _socks.begin(), end = _socks.end(); it != end; ++it)
			FD_SET(*it, &rfds);

		tv.tv_sec = timeo;
		tv.tv_usec = 0;

		if ((i = select(maxfd + 1, &rfds, NULL, NULL, &tv)) < 0) {
			if (errno == EINTR)
				continue;
			fprintf(stderr, "select: %s\n", strerror(errno));
			exit(1);
		}

		if (i == 0)
			throw select_timeout();

		for (it = _socks.begin(), end = _socks.end(); it != end; ++it) {
			if (FD_ISSET(*it, &rfds)) {
				ret.push_back(*it);
				if (--i == 0)
					return ret;
			}
		}

		return ret;
	}
}

packet_receiver receiver;

void
ioloop(int interval)
{
	for (;;) {
	packet	p;
		try {
			p = receiver.receive(interval);
		} catch (select_timeout &) {
			printer->update();
			continue;
		}

	logent	e(usegmt);
		if (decoder->decode(e, (sockaddr *)&p.cliaddr, p.clilen,
				    p.buf, p.buf + p.len))
			printer->print(e);
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
"\t           (one of: \"loreley\" (default), \"clf\", \"squid\")\n"
"\t-F <file>    write to this file instead of stdout\n"
"\t-d           become a daemon after startup\n"
"\t-L           print timestamps in local time, not GMT\n"
"\t-n           don't try to resolve client IPs to names\n"
"\t-T           produce top-like output showing most requested URLs,\n"
"\t             outputting every [interval] seconds (default 5)\n"
		, progname);
}

} // anonymous namespace

enum logfmt_t {
	lf_loreley,
	lf_clf,
	lf_squid,
	lf_topurl
} logfmt = lf_loreley;

int
main(int argc, char *argv[])
{
struct addrinfo hints, *res;
int		 i;
const char	*port = "4445", *host = NULL;
char		*progname = argv[0];
bool		 daemon = false;
ostream		*outfile = NULL;
bool		 Tflag = false;
int		 interval = 5;

map<string, logfmt_t> logfmt_names = map_list_of
	("loreley",	lf_loreley)
	("clf",		lf_clf)
	("squid",	lf_squid)
	("topurl",	lf_topurl)
	;

	decoder = shared_ptr<packet_decoder>(new packet_decoder);

	while ((i = getopt(argc, argv, "h46a:p:f:s:F:dLnT")) != -1) {
                switch (i) {
		case '4':
			hints.ai_family = AF_INET;
			break;

		case '6':
			hints.ai_family = AF_INET6;
			break;

		case 'n':
			decoder->usedns(false);
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

		case 'f': {
		map<string, logfmt_t>::iterator it = logfmt_names.find(optarg);
			if (it == logfmt_names.end()) {
				fprintf(stderr, "unrecognised log format \"%s\"\n", optarg);
				usage(argv[0]);
				return 1;
			}

			logfmt = it->second;
			break;
		}

		case 'a':
			host = optarg;
			break;

		case 'h':
			usage(argv[0]);
			return 0;

		case 's':
			if (!decoder->add_acl(optarg)) {
				fprintf(stderr, "%s: could not parse IP mask: %s\n", argv[0], optarg);
				return 1;
			}
			break;

		case 'F':
			outfile = new ofstream(optarg, ios::app);
			if (!static_cast<ofstream *>(outfile)->is_open()) {
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

	if (Tflag && (outfile || daemon)) {
		fprintf(stderr, "%s: -T cannot be used with -F or -d\n",
			progname);
		usage(progname);
		return 1;
	}

	if (Tflag)
		logfmt = lf_topurl;

	if (!outfile)
		outfile = &cout;

	if ((!Tflag && argc) || (Tflag && argc > 1)) {
		usage(progname);
		return 1;
	} else if (Tflag) try {
		interval = lexical_cast<int>(argv[0]);
	} catch (bad_lexical_cast &) {
		cout << format("%s: invalid interval \"%s\"\n")
				% progname % argv[0];
		return 1;
	}

	switch (logfmt) {
	case lf_loreley:
		printer = shared_ptr<entry_printer>(new loreley_printer(*outfile));
		break;

	case lf_clf:
		printer = shared_ptr<entry_printer>(new clf_printer(*outfile));
		break;

	case lf_squid:
		printer = shared_ptr<entry_printer>(new squid_printer(*outfile));
		break;

	case lf_topurl:
		printer = shared_ptr<entry_printer>(new topurl_printer(interval));
		break;
	};

	memset(&hints, 0, sizeof(hints));
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
		receiver.add_sock(sfd);
	}

	if (receiver.num_fds() == 0) {
		fprintf(stderr, "%s: could not create any listening sockets\n",
			progname);
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

        ioloop(interval);
        return 0;
}
