/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <stdio.h>
#include <stdlib.h>
#include <signal.h>
#include <stdarg.h>
#include <string.h>
#include <unistd.h>
#include <errno.h>
#include <pwd.h>
#include <grp.h>
#include <strings.h>
#include <ctype.h>

#include "wlog.h"
#include "wnet.h"
#include "wconfig.h"
#include "willow.h"
#include "whttp.h"
#include "wcache.h"
#include "confparse.h"
#include "radix.h"
#include "format.h"

static void stats_init(void);

static const char *progname;

#define min(x,y) ((x) < (y) ? (x) : (y))

/*ARGSUSED*/
static void 
sig_exit(int s)
{
	wnet_exit = 1;
}

static void
usage(void)
{
	(void)fprintf(stderr, "usage: %s [-fzv]\n"
			"\t-f\trun in foreground (don't detach)\n"
			"\t-z\tcreate cache directory structure and exit\n"
			"\t-v\tprint version number and exit\n"
			, progname);
}

int 
main(int argc, char *argv[])
{
	int	 i;
	int	 zflag = 0;
	char	*cfg = NULL;
	
	progname = argv[0];
	
	while ((i = getopt(argc, argv, "fzvc:")) != -1) {
		switch (i) {
			case 'z':
				zflag++;
			/*FALLTHRU*/
			case 'f':
				config.foreground = 1;
				break;
			case 'v':
				(void)fprintf(stderr, "%s\n", PACKAGE_VERSION);
				exit(0);
				/*NOTREACHED*/
			case 'c':
				cfg = optarg;
				break;
			default:
				usage();
				exit(8);
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

	wlog_init();
	if (zflag) {
		wcache_setupfs();
		exit(0);
	}

	ioloop = new ioloop_t;		
	whttp_init();
	wcache_init(1);
	stats_init();

	(void)signal(SIGINT, sig_exit);
	(void)signal(SIGTERM, sig_exit);
	
	wlog(WLOG_NOTICE, "running");

	if (!config.foreground)
		daemon(0, 0);

	ioloop->run();
	wlog_close();
	wcache_shutdown();
	whttp_shutdown();
	
	return EXIT_SUCCESS;
}

void
outofmemory(void)
{
	static int count;
	
	if (count++)
		abort();
	
	wlog(WLOG_ERROR, "fatal: out of memory. exiting.");
	exit(8);
}

char **
wstrvec(const char *str, const char *sep, int lim)
{
	char	**result = NULL;
	int	 nres = 0;
	char	*s;
const	char	*st = str;

	while ((!lim || --lim) && (s = strstr(st, sep))) {
		result = (char **)wrealloc(result, ++nres * sizeof(char *));
		while (isspace(*st))
			st++;
		result[nres - 1] = (char *)wmalloc((s - st) + 1);
		memcpy(result[nres - 1], st, s - st);
		result[nres - 1][s - st] = '\0';
		st = s + strlen(sep);
	}

	result = (char **)wrealloc(result, ++nres * sizeof(char *));
	while (isspace(*st))
		st++;
	result[nres - 1] = wstrdup(st);

	result = (char **)wrealloc(result, (nres + 1) * sizeof(char *));
	result[nres] = NULL;
	return result;
}

void
wstrvecfree(char **vec)
{
	char **s = vec;
	while (*s) {
		wfree(*s);
		s++;
	}
	wfree(vec);
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
                else    return res;
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
                else    return res;
                res += tval * mult;
                mult *= 10;
        }
        return res;
}

stats_stru stats;
static struct event stats_ev;
static struct timeval stats_tv;
static void stats_sched(void);
void add_stats_listener(pair<string,string> const &ip);

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
		s->sendto(buf, bufp - buf, addr);
	}
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

void
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
		wlog(WLOG_WARNING, format("resolving [%s]:%s: %s")
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
			wlog(WLOG_WARNING,
				format("creating statistics listener: %s:%s: %s")
				% ip.first % ip.second % e.what());
			delete sock;
			continue;
		}

		try {
			sock->bind();
		} catch (socket_error &e) {
			wlog(WLOG_WARNING,
				format("binding statistics listener %s: %s")
				% it->straddr() % e.what());
			delete sock;
			continue;
		}

		sock->readback(polycaller<wsocket *, int>(stats_handler, 
			&stats_handler_stru::callback), 0);
		wlog(WLOG_NOTICE, format("statistics listener: %s")
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
