/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whtcp: command-line HTCP client.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#define _XOPEN_SOURCE 500
#define __EXTENSIONS__

#include <boost/archive/iterators/base64_from_binary.hpp>
#include <boost/archive/iterators/transform_width.hpp>

#include <sys/types.h>
#include <sys/socket.h>

#include <netdb.h>
#include <unistd.h>
#include <iostream>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <cerrno>
#include <string>
#include <iterator>
#include <fstream>
using std::fprintf;
using std::memset;
using std::strchr;
using std::strerror;
using std::exit;
using std::ostream;
using std::cout;
using std::copy;
using std::back_inserter;
using std::ifstream;

#include "htcp.h"

#ifdef __INTEL_COMPILER
# pragma warning (disable: 383 981 1418)
#endif

template<typename T>
struct tab_outputter : public std::iterator<std::output_iterator_tag, void, void, void, void> {
	tab_outputter(ostream &stream_)
		: stream(stream_)
	{}

	tab_outputter& operator++(void) {
		return *this;
	}

	tab_outputter& operator++(int) {
		return *this;
	}

	tab_outputter& operator=(T const &v) {
		stream << '\t' << v << '\n';
		return *this;
	}

	tab_outputter& operator*(void) {
		return *this;
	}

private:
	ostream &stream;
};

void
makekey(string const &name)
{
using	boost::archive::iterators::base64_from_binary;
using	boost::archive::iterators::transform_width;
u_char	key[64];	/* 64 bytes for HMAC-MD5 */
string	b64key;
int	fd;
typedef base64_from_binary<transform_width<u_char const *, 6, 8> > base64_text;
	if ((fd = open("/dev/urandom", O_RDONLY)) == -1) {
		perror("/dev/urandom");
		exit(1);
	}

	if (read(fd, key, sizeof(key)) < (int)sizeof(key)) {
		fprintf(stderr, "not enough entropy in /dev/urandom\n");
		exit(1);
	}

	copy(base64_text(key), base64_text(key + sizeof(key)),
		back_inserter(b64key));
	std::cout << name << ' ' << b64key << '\n';		
}

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

ustring
getkey(string const &keystore, string const &keyname)
{
ifstream	f(keystore.c_str());
	if (!f.is_open()) {
		fprintf(stderr, "%s: cannot open: %s\n",
			keystore.c_str(), strerror(errno));
		return ustring();
	}

string	s;
int	line = 0;
	while (getline(f, s)) {
	string			name, key;
	string::size_type	i;
		++line;
		if ((i = s.find(' ')) == string::npos) {
			fprintf(stderr, "%s(%d): syntax error\n",
				keystore.c_str(), line);
			continue;
		}
		name = s.substr(0, i);
		key = s.substr(i + 1);

		if (key.size() != 86) {
			fprintf(stderr, "%s(%d): key has wrong length\n",
				keystore.c_str(), line);
			continue;
		}

		if (name != keyname)
			continue;

	ustring		bkey;
	unbase64_string it(key.begin());
		for (size_t i = 0; i < 64; ++i) {
			bkey.push_back(*it++);
		}

		return bkey;
	}

	return ustring();
}

void
usage(const char *progname)
{
	fprintf(stderr, 
"usage: %1$s -n [-S <keyname>] [-K <keystore>] <hostname>[/port]\n"
"       %1$s -q [-S <keyname>] [-K <keystore>] <hostname>[/port]\n"
"               <url> [<url>, ...]\n"
"       %1$s -g <keyname>\n"
"\n"
"  -n           perform NOP query (tests HTCP functionality and RTT)\n"
"  -q           query the status of the given url(s) in the cache\n"
"  -g           generate an HTCP HMAC-MD5 key of the given name\n"
"  -S keyname   sign packets with this key and require signed packets\n"
"               from server\n"
"  -K keystore  use this file as signing keystore\n"
	, progname);
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
bool		 nflag = false, qflag = false;
timeval		 start, finish;
bool		 gflag = false;
string		 keystore, keyname;
ustring		 key;

	while ((i = getopt(argc, argv, "nqgS:K:")) != -1) {
		switch (i) {
		case 'n':
			nflag = true;
			break;

		case 'q':
			qflag = true;
			break;

		case 'g':
			gflag = true;
			break;

		case 'S':
			keyname = optarg;
			break;

		case 'K':
			keystore = optarg;
			break;

		default:
			usage(progname);
			return 1;
		}
	}
	argc -= optind;
	argv += optind;

	if (gflag) {
		if (!argv[0]) {
			fprintf(stderr, "%s: not enough arguments\n",
				progname);
			usage(progname);
			return 1;
		}

		makekey(argv[0]);
		return 0;
	}

	if (nflag + qflag > 1) {
		fprintf(stderr, 
			"%s: only one of -nq may be specified\n",
			progname);
		usage(progname);
		return 1;
	}

	if (!argv[0] || (qflag && !argv[1]) || (nflag && argv[1])) {
		usage(progname);
		return 1;
	}

	host = argv[0];
	if ((port = strchr(argv[0], '/')) != NULL) {
		*port++ = '\0';
		pstr = port;
	} else	pstr = "4827";

	memset(&hints, 0, sizeof(hints));
	hints.ai_socktype = SOCK_DGRAM;
	hints.ai_flags = AI_ADDRCONFIG;
	if ((i = getaddrinfo(host, pstr, &hints, &res)) != 0) {
		fprintf(stderr, "resolving %s:%s: %s\n", host, pstr, gai_strerror(i));
		return 1;
	}

htcp_encoder	obuf;
htcp_opdata_tst	tstdata;
htcp_opdata_nop	nopdata;

	obuf.rd(true);
	if (nflag) {		/* NOP */
		obuf.opcode(htcp_op_nop);
		obuf.opdata(&nopdata);
	} else if (qflag) {	/* TST */
		tstdata.tst_specifier.hs_method = "GET";
		tstdata.tst_specifier.hs_version = "1.1";
		tstdata.tst_specifier.hs_url = argv[1];
		obuf.opcode(htcp_op_tst);
		obuf.opdata(&tstdata);
	}

	if (!keyname.empty()) {
	ustring	key = getkey(keystore, keyname);
		if (key.empty()) {
			fprintf(stderr, "%s: key \"%s\" not found\n",
				progname, keyname.c_str());
			return 1;
		}

		obuf.key(keyname, key);
	}


int	sfd = -1;
timeval	tv;
	tv.tv_sec = timeo;
	tv.tv_usec = 0;
	for (r = res; r; r = r->ai_next) {
		if ((sfd = ::socket(r->ai_family, r->ai_socktype, r->ai_protocol)) == -1) {
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

	sockaddr_storage	 local;
	socklen_t		 loclen = sizeof(local);
		getsockname(sfd, reinterpret_cast<sockaddr *>(&local), &loclen);
		obuf.build_packet(reinterpret_cast<sockaddr *>(&local), r->ai_addr);

	char const		*buf = obuf.packet();
	size_t			 len = obuf.packet_length();

		gettimeofday(&start, NULL);

		if (send(sfd, buf, len, 0) < (ssize_t)len) {
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
	gettimeofday(&finish, NULL);
	std::cout << "response okay, RTT: " <<
		(((finish.tv_sec * 1000000.) + finish.tv_usec) - 
		((start.tv_sec * 1000000.) + start.tv_usec)) / 1000000. 
		<< " sec.\n";

htcp_decoder	ibuf(rbuf, i);
	if (!ibuf.okay()) {
		fprintf(stderr, "cannot decode received packet\n");
		return 1;
	}

	if (ibuf.transid() != obuf.transid())
		std::cerr << progname << ": warning: reply has incorrect trans-id "
			"(" << ibuf.transid() << ", expected " << obuf.transid() << ")\n";

	switch (ibuf.opcode()) {
	case htcp_op_nop:
		return 0;


	case htcp_op_tst: {	
		cout << "Entity: " << argv[1];

		if (ibuf.response() == 0) { /* entity found */
		htcp_opdata_tst_resp_found *opd =
			(htcp_opdata_tst_resp_found *) ibuf.opdata();
			cout << " is cached;\n";
			cout << "Response headers:\n";
			copy(opd->tf_detail.hd_resphdrs.begin(),
			     opd->tf_detail.hd_resphdrs.end(),
			     tab_outputter<string>(cout));

			cout << "Entity headers:\n";
			copy(opd->tf_detail.hd_enthdrs.begin(),
			     opd->tf_detail.hd_enthdrs.end(),
			     tab_outputter<string>(cout));

			cout << "Cache headers:\n";
			copy(opd->tf_detail.hd_cachehdrs.begin(),
			     opd->tf_detail.hd_cachehdrs.end(),
			     tab_outputter<string>(cout));
		} else {
		htcp_opdata_tst_resp_notfound *opd =
			(htcp_opdata_tst_resp_notfound *) ibuf.opdata();
			cout << " is not cached;\n";
			cout << "Cache headers:\n";
			copy(opd->tn_cachehdrs.begin(),
			     opd->tn_cachehdrs.end(),
			     tab_outputter<string>(cout));
		}

		return 0;
	}
	}

	freeaddrinfo(res);
}
