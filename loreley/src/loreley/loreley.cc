/* Loreley: Lightweight HTTP reverse-proxy.				*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.	*/
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty. 
 */

/* @(#) $Id$ */

#include <sys/mman.h>
#include <fcntl.h>
#include <unistd.h>
#include <pwd.h>
#include <grp.h>
#include <limits.h>

#include <cstdio>
#include <cstdlib>
#include <csignal>
#include <cstdarg>
#include <cstring>
#include <unistd.h>
#include <cerrno>
#include <cctype>
#include <iostream>
using std::streamsize;

#include "log.h"
#include "net.h"
#include "config.h"
#include "loreley.h"
#include "http.h"
#include "cache.h"
#include "confparse.h"
#include "access.h"
#include "format.h"
#include "mbuffer.h"
#include "htcp.h"

static void stats_init(void);
static int pagesize;
static const char *progname;

event checkexit_ev;
timeval checkexit_tv;

#define min(x,y) ((x) < (y) ? (x) : (y))

static void checkexit_sched(void);

static void
checkexit_update(int, short, void *)
{
timeval	tv = {0, 0};
	if (wnet_exit) {
		event_del(&checkexit_ev);
		event_base_loopexit(evb, &tv);
		return;
	}
	checkexit_sched();
}

static void
checkexit_sched(void)
{
	checkexit_tv.tv_usec = 0;
	checkexit_tv.tv_sec = 1;
	evtimer_set(&checkexit_ev, checkexit_update, NULL);
	event_base_set(evb, &checkexit_ev);
	event_add(&checkexit_ev, &checkexit_tv);
}

static void
usage(void)
{
	fprintf(stderr, "usage: %s [-hfzv] [-F cfg] [-D cond[=value]]\n"
"      -h                    print this message\n"
"      -f                    run in foreground (don't detach)\n"
"      -v                    print version number and exit\n"
"      -z                    create initial cache directories and exit\n"
"      -D cond[=value]       set 'cond' to 'value' (which should be 'true' or\n"
"                            'false') in the configuration parser.  if 'value'\n"
"                            is not specified, defaults to true\n"
"      -F cfg                load configuration from this file, instead of default\n"
"                            (%s)\n"                          
			, progname, SYSCONFDIR "/loreley.conf");
}

int 
main(int argc, char *argv[])
{
int	 i;
char	*cfg = NULL;
char	*dval;
bool	 zflag = false;
	progname = argv[0];
	pagesize = sysconf(_SC_PAGESIZE);

	while ((i = getopt(argc, argv, "fvc:D:hzF:")) != -1) {
		switch (i) {
			case 'h':
				usage();
				return 0;

			case 'f':
				config.foreground = 1;
				break;

			case 'v':
				fprintf(stderr, "%s\n", PACKAGE_VERSION);
				exit(0);

			case 'c':
				cfg = optarg;
				break;

			case 'z':
				zflag = true;
				config.foreground = true;
				break;

			case 'F':
				cfg = optarg;
				break;

			case 'D':
				dval = NULL;
				if ((dval = strchr(optarg, '=')) != NULL)
					*dval++ = '\0';

				conf::add_if_entry(optarg, 
					dval ? strtoll(dval, NULL, 0) : 1);
				break;

			default:
				usage();
				return 8;
		}
	}

	argv += optind;
	argc -= optind;

	if (argc) {
		(void)fprintf(stderr, "%s: too many argments\n", progname);
		usage();
		exit(8);
	}
	
	wnet_set_time();

	wconfig_init(cfg);

	if (config.sgid != "") {
		struct group *group = getgrnam(config.sgid.c_str());
		if (!group) {
			fprintf(stderr, "group %s does not exist", config.sgid.c_str());
			exit(8);
		}
		if (setgid(group->gr_gid) < 0) {
			perror("setgid");
			exit(8);
		}
	}

	if (config.suid != "") {
		struct passwd *user = getpwnam(config.suid.c_str());
		if (!user) {
			fprintf(stderr, "user %s does not exist", config.suid.c_str());
			exit(8);
		}
		if (setuid(user->pw_uid) < 0) {
			perror("setuid");
			exit(8);
		}
	}

	if (!wlog.open())
		return 1;

	if (zflag) {
		if (!entitycache.create())
			return 1;
		return 0;
	}

	make_event_base();
	ioloop = new ioloop_t;		
	checkexit_sched();
	if (!entitycache.open())
		return 1;

	whttp_init();
	stats_init();
	htcp_init();

	wlog.notice("running");

	if (!config.foreground)
		daemon(0, 0);

	ioloop->run();
	wlog.notice("shutting down");
	wlog.close();
	whttp_shutdown();
	entitycache.close();

	pthread_exit(NULL);	
	return EXIT_SUCCESS;
}

void
outofmemory(void)
{
	static int count;
	
	if (count++)
		abort();
	
	wlog.error("fatal: out of memory. exiting.");
	exit(8);
}

int char_table[256] = {
	/* 0   */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 8   */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 16  */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 24  */ 0, 0, 0, 0, 0, 0, 0, 0, 
	/* 32  */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 40  */ 0, 0, 0, 0, 0, 0, CHAR_HOST, 0,
	/* 48  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST, 
	/* 52  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 56  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, 0,
	/* 60  */ 0, 0, 0, 0,
	/* 64  */ 0, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 68  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 72  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 76  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 80  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 84  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 88  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, 0,
	/* 92  */ 0, 0, 0, 0,
	/* 96  */ 0, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 100 */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 104 */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 108 */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 112 */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 116 */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 120 */ CHAR_HOST, CHAR_HOST, 0, 0,
	/* 124 */ 0, 0, 0, 0,
	/* 136 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 144 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 152 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 160 */ 0, 0, 0, 0, 0, 0, 0, 0, 
	/* 168 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 176 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 184 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 192 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 200 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 208 */ 0, 0, 0, 0, 0, 0, 0, 0, 
	/* 216 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 224 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 232 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 240 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 248 */ 0, 0, 0, 0, 0, 0, 0, 0,
};

int
str16toint(char const *src, int len)
{
int     mult = 1;
int     res = 0;
        for (; len; len--) {
        int     tval;
        char    c = src[len - 1];
                if (c >= '0' && c <= '9')
                        tval = c - '0';
                else if (c >= 'a' && c <= 'f')
                        tval = 10 + c - 'a';
                else if (c >= 'A' && c <= 'F')
                        tval = 10 + c - 'A';
                else    return -1;
                res += tval * mult;
                mult <<= 4;
        }
        return res;
}

int
str10toint(char const *src, int len)
{
int     mult = 1;
int     res = 0;
        for (; len; len--) {
        int     tval;
        char    c = src[len - 1];
                if (c >= '0' && c <= '9')
                        tval = c - '0';
                else    return -1;
                res += tval * mult;
                mult *= 10;
        }
        return res;
}

stats_stru stats;
static struct event stats_ev;
static struct timeval stats_tv;
static void stats_sched(void);
static void add_stats_listener(pair<string,string> const &ip);

struct stats_handler_stru : noncopyable {
	void	callback (wsocket *, int);
};
static stats_handler_stru stats_handler;
 
void
stats_handler_stru::callback(wsocket *s, int)
{
char		buf[65535], *bufp = buf, *endp = buf + sizeof(buf);
char		rdata[3];
address		addr;

	s->readback(polycaller<wsocket *, int>(stats_handler, 
		&stats_handler_stru::callback), 0);

	if (s->recvfrom(rdata, sizeof(rdata), addr) != 2)
		return;

	if (rdata[0] != 1 || rdata[1] != 0)
		return;	/* wrong version or length */

	if (!stats.access.allowed(addr.addr()).first)
		return;

	/*
	 * Stats format:
	 *   <version><interval><treqok><treqfail><trespok><trespfail><reqoks><respoks>
	 *   <reqfails><respfails>
	 */
	{	HOLDING(stats.cur_lock);
		ADD_UINT8(bufp, 1, endp);		/* stats format version */
		ADD_STRING(bufp, string(PACKAGE_VERSION), endp);
		ADD_UINT16(bufp, stats.interval, endp);
		ADD_UINT32(bufp, stats.cur.n_httpreq_ok, endp);
		ADD_UINT32(bufp, stats.cur.n_httpreq_fail, endp);
		ADD_UINT32(bufp, stats.cur.n_httpresp_ok, endp);
		ADD_UINT32(bufp, stats.cur.n_httpresp_fail, endp);
		ADD_UINT32(bufp, stats.n_httpreq_oks, endp);
		ADD_UINT32(bufp, stats.n_httpresp_oks, endp);
		ADD_UINT32(bufp, stats.n_httpreq_fails, endp);
		ADD_UINT32(bufp, stats.n_httpresp_fails, endp);
	}
	/*
	 * Listener stats: <name><ncons>
	 */
vector<listener *>::iterator lit = listeners.begin(), lend = listeners.end();
	for (; lit != lend; ++lit) {
	uint64_t	c = (*lit)->nconns;
		ADD_STRING(bufp, (*lit)->sock->straddr(), endp);
		ADD_UINT32(bufp, c & (uint32_t)-1, endp);
		ADD_UINT32(bufp, c >> 32, endp);
	}

	s->sendto(buf, bufp - buf, addr);
}

static void
stats_update(int, short, void *)
{
	{	HOLDING(stats.cur_lock);
		stats.n_httpreq_oks = (stats.cur.n_httpreq_ok - stats.last.n_httpreq_ok) / stats.interval;
		stats.n_httpreq_fails = (stats.cur.n_httpreq_fail - stats.last.n_httpreq_fail) / stats.interval;
		stats.n_httpresp_oks = (stats.cur.n_httpresp_ok - stats.last.n_httpresp_ok) / stats.interval;
		stats.n_httpresp_fails = (stats.cur.n_httpresp_fail - stats.last.n_httpresp_fail) / stats.interval;
		stats.last = stats.cur;
	}

	stats_sched();
}

static void
stats_init(void)
{
	if (!config.udp_stats)
		return;

vector<pair<string,string> >::iterator	it = config.stats_hosts.begin(),
					end = config.stats_hosts.end();
	for (; it != end; ++it)
		add_stats_listener(*it);
	stats_sched();
}

static void
add_stats_listener(pair<string,string> const &ip)
{
addrlist	*alist;
const char	*hstr = NULL, *pstr = DEFAULT_STATS_PORT;
	if (!ip.first.empty())
		hstr = ip.first.c_str();
	if (!ip.second.empty())
		pstr = ip.second.c_str();

	try {
		alist = addrlist::resolve(hstr, pstr, st_dgram);
	} catch (socket_error &e) {
		wlog.warn(format("resolving [%s]:%s: %s")
			% hstr % pstr % e.what());
		return;
	}

addrlist::iterator	it = alist->begin(), end = alist->end();
	for (; it != end; ++it) {
	wnet::socket	*sock = NULL;
		try {
			sock = it->makesocket("statistics listener", prio_stats);
			sock->nonblocking(true);
		} catch (socket_error &e) {
			wlog.warn(format("creating statistics listener: %s:%s: %s")
				% ip.first % ip.second % e.what());
			delete sock;
			continue;
		}

		try {
			sock->bind();
		} catch (socket_error &e) {
			wlog.warn(format("binding statistics listener %s: %s")
				% it->straddr() % e.what());
			delete sock;
			continue;
		}

		sock->readback(polycaller<wsocket *, int>(stats_handler, 
			&stats_handler_stru::callback), 0);
		wlog.notice(format("statistics listener: %s")
			% sock->straddr());
	}
}

/*
 * Schedule the update event.
 */
static void
stats_sched(void)
{
	stats_tv.tv_usec = 0;
	stats_tv.tv_sec = stats.interval;
	evtimer_set(&stats_ev, stats_update, NULL);
	event_base_set(evb, &stats_ev);
	event_add(&stats_ev, &stats_tv);
}

void
diobuf::resize(size_t newsize)
{
	if (newsize <= _reserved) {
		_size = newsize;
		return;
	}

	if (_fd != -1) {
		munmap(_buf, _size);
		_size = newsize;
		_reserved = pagesize * (newsize / pagesize + 1);
		WDEBUG(format("new size %d reserved %d") % _size % _reserved);
		lseek(_fd, _reserved, SEEK_SET);
		write(_fd, "", 1);
		_buf = (char *)mmap(0, _reserved, PROT_READ | PROT_WRITE, MAP_SHARED, _fd, 0);
		if (_buf == MAP_FAILED)
			abort();
		lseek(_fd, 0, SEEK_SET);
	} else {
	char	*n;
		n = new char[newsize];
		memcpy(n, _buf, _size);
		delete[] _buf;
		_buf = n;
		_size = _reserved = newsize;
	}
}

void
diobuf::finished(void)
{
}

diobuf::~diobuf(void)
{
	if (_fd != -1) {
		close(_fd);
		if (_buf)
			munmap(_buf, _reserved);
	} else {
		delete[] _buf;
	}
}

diobuf::diobuf(size_t size)
	: _fd(-1)
	, _buf(0)
	, _size(0)
	, _reserved(pagesize * (size / pagesize + 1))
{
	WDEBUG(format("reserved: %d") % _reserved);

	if (size == 0)
		_reserved = size = pagesize;

	if (!config.use_dio) {
		_buf = new char[size];
		return;
	}
	char	path[PATH_MAX];
	snprintf(path, sizeof(path), "/dev/shm/loreley.diobuf.%d.%d.%d",
		(int) getpid(), (int) pthread_self(), rand());
		if ((_fd = open(path, O_CREAT | O_EXCL | O_RDWR, 0600)) == -1) {
		wlog.warn(format("opening diobuf %s: %s") 
			% path % strerror(errno));
		_buf = new char[size];
		return;
	}
	unlink(path);

	if (lseek(_fd, _reserved, SEEK_SET) == -1) {
		wlog.warn(format("seeking diobuf %s: %s") 
			% path % strerror(errno));
		close(_fd);
		_fd = -1;
		_buf = new char[size];
		return;
	}

	if (write(_fd, "", 1) < 1) {
		wlog.warn(format("extending diobuf %s: %s") 
			% path % strerror(errno));
		close(_fd);
		_fd = -1;
		_buf = new char[size];
		return;
	}

	_buf = (char *)mmap(0, _reserved, PROT_READ | PROT_WRITE, MAP_SHARED, _fd, 0);
	if (_buf == MAP_FAILED) {
		wlog.warn(format("mapping diobuf %s: %s") 
			% path % strerror(errno));
		close(_fd);
		_fd = -1;
		_buf = new char[size];
		return;
	}
	lseek(_fd, 0, SEEK_SET);
}

bool
diobuf::loadfile(istream &f, size_t n)
{
	resize(n);
	if (!f.read(_buf, n) || f.gcount() != (streamsize)n)
		return false;
	return true;
}
