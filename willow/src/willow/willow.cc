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

#ifdef __lint
# pragma error_messages(off, E_H_C_CHECK2)
#endif

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
		
	/*
	 * HTTP should be initialised before the network so that
	 * the wlogwriter exits cleanly.
	 */
	whttp_init();
	wnet_init();
	wcache_init(1);
	stats_init();

	(void)signal(SIGINT, sig_exit);
	(void)signal(SIGTERM, sig_exit);
	
	wlog(WLOG_NOTICE, "running");

	if (!config.foreground)
		daemon(0, 0);

	wnet_run();
	wlog_close();
	wcache_shutdown();
	whttp_shutdown();
	
	return EXIT_SUCCESS;
}

#ifdef __lint
# pragma error_messages(default, E_H_C_CHECK2)
#endif

void
outofmemory(void)
{
	static int count;
	
	if (count++)
		abort();
	
	wlog(WLOG_ERROR, "fatal: out of memory. exiting.");
	exit(8);
}

void
realloc_addchar(char **sp, int c)
{
	char	*p;
	int	 len;
	
	if (*sp)
		len = strlen(*sp);
	else
		len = 0;
	
	if ((*sp = (char *)wrealloc(*sp, len + 2)) == NULL)
		outofmemory();
	p = *sp + len;
	*p++ = (char) c;
	*p++ = '\0';
}

void
realloc_strcat(char **sp, const char *s)
{
	int	 len;
	
	if (*sp)
		len = strlen(*sp);
	else
		len = 1;
	if ((*sp = (char *)wrealloc(*sp, len + strlen(s) + 1)) == NULL)
		outofmemory();
	(void)strcat(*sp, s);
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

stats_stru stats;
static struct event stats_ev;
static struct timeval stats_tv;
static void stats_sched(void);

static void
stats_cb(fde *e)
{
char	buf[65535], *bufp = buf, *endp = buf + sizeof(buf);
char	rdata[3];
int	i;
sockaddr_storage	ss;
socklen_t		sslen = sizeof(ss);
char	str[NI_MAXHOST];
	if (recvfrom(e->fde_fd, rdata, sizeof(rdata), 0, (sockaddr *)&ss, &sslen) != 2)
		return;
	if (rdata[0] != 1 || rdata[1] != 0)
		return;
	if (stats.v4_access || stats.v6_access) {
		if (getnameinfo((sockaddr *)&ss, sslen, str, sizeof(str), NULL, 0, NI_NUMERICHOST) != 0)
			return;
		if (stats.v4_access && ss.ss_family == AF_INET)
			if (!radix_search(stats.v4_access, str))
				return;
		if (stats.v6_access && ss.ss_family == AF_INET6)
			if (!radix_search(stats.v6_access, str))
				return;
	}

	/*
	 * Stats format:
	 *   <version><treqok><treqfail><trespok><trespfail><reqoks><respoks>
	 *   <reqfails><respfails>
	 */
	ADD_UINT8(bufp, 1, endp);		/* stats format version */
	ADD_STRING(bufp, PACKAGE_VERSION, endp);
	ADD_UINT32(bufp, stats.cur.n_httpreq_ok, endp);
	ADD_UINT32(bufp, stats.cur.n_httpreq_fail, endp);
	ADD_UINT32(bufp, stats.cur.n_httpresp_ok, endp);
	ADD_UINT32(bufp, stats.cur.n_httpresp_fail, endp);
	ADD_UINT32(bufp, stats.n_httpreq_oks, endp);
	ADD_UINT32(bufp, stats.n_httpresp_oks, endp);
	ADD_UINT32(bufp, stats.n_httpreq_fails, endp);
	ADD_UINT32(bufp, stats.n_httpresp_fails, endp);
	sendto(e->fde_fd, buf, bufp - buf, 0, (sockaddr *)&ss, sslen);
}

static void
stats_update(int, short, void *)
{
	stats.n_httpreq_oks = (stats.cur.n_httpreq_ok - stats.last.n_httpreq_ok) / stats.interval;
	stats.n_httpreq_fails = (stats.cur.n_httpreq_fail - stats.last.n_httpreq_fail) / stats.interval;
	stats.n_httpresp_oks = (stats.cur.n_httpresp_ok - stats.last.n_httpresp_ok) / stats.interval;
	stats.n_httpresp_fails = (stats.cur.n_httpresp_fail - stats.last.n_httpresp_fail) / stats.interval;
	stats.last = stats.cur;

	stats_sched();
}

static void
stats_init(void)
{
addrinfo	hints, *res, *r;
int		i;
char		portstr[6];
	if (!config.stats_port)
		return;

	/*
	 * Create the UDP listener.
	 */
	sprintf(portstr, "%d", config.stats_port);
	memset(&hints, 0, sizeof(hints));
	hints.ai_socktype = SOCK_DGRAM;
	if ((i = getaddrinfo(NULL, portstr, &hints, &res)) != 0) {
		wlog(WLOG_WARNING, "resolving statistics listener: %s: %s",
			portstr, strerror(errno));
		return;
	}
	for (r = res; r; r = r->ai_next) {
	int	sfd;
		if ((sfd = wnet_open("statistics listener", prio_stats, r->ai_family, r->ai_socktype)) == -1) {
			wlog(WLOG_WARNING, "creating statistics listener: %s", strerror(errno));
			continue;
		}
		if (bind(sfd, r->ai_addr, r->ai_addrlen) < 0) {
			wlog(WLOG_WARNING, "binding %s: %s", wnet::fstraddr("", r->ai_addr, r->ai_addrlen).c_str(),
				strerror(errno));
			wnet_close(sfd);
			continue;
		}
		wnet_register(sfd, FDE_READ, stats_cb, NULL);
		wlog(WLOG_NOTICE, "statistics listener: %s", wnet::fstraddr("", r->ai_addr, r->ai_addrlen).c_str());
	}
	freeaddrinfo(r);
	stats_sched();
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
	event_add(&stats_ev, &stats_tv);
}
