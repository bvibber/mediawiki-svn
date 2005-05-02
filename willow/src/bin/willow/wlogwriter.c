/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wlogwriter: child process for log writing.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include <stdio.h>
#include <unistd.h>
#include <errno.h>
#include <string.h>
#include <stdlib.h>
#include <signal.h>

#include "willow.h"
#include "wlogwriter.h"
#include "wconfig.h"
#include "wlog.h"

static void wlogwriter_run(int);

void
wlogwriter_start(fd)
	int *fd;
{
	switch (fork()) {
	case -1:
		wlog(WLOG_ERROR, "fork: %s", strerror(errno));
		exit(8);
		/*NOTREACHED*/
	case 0:
		(void)close(fd[0]);
		wlogwriter_run(fd[1]);
		break;
	default:
		(void)close(fd[1]);
		break;
	}
}

static void
wlogwriter_run(pipe)
	int pipe;
{
	FILE	*inf, *outf;
	char	*line;
	size_t	 lnsz;

	lnsz = 8192;
	line = malloc(lnsz);

	(void)signal(SIGPIPE, SIG_IGN);

#ifdef HAVE_SETPROCTITLE
	setproctitle("log writer: %s", config.access_log);
#endif
	wlog(WLOG_NOTICE, "wlogwriter starting (pid %d) for %s", (int)getpid(), config.access_log);

	if ((inf = fdopen(pipe, "r")) == NULL) {
		perror("fdopen");
		exit(8);
	}
	/*LINTED unsafe fopen*/
	if ((outf = fopen(config.access_log, "a")) == NULL) {
		perror(config.access_log);
		exit(8);
	}

	while (fgets(line, lnsz, inf)) {
		if (fputs(line, outf) == EOF || fflush(outf) == EOF) {
			wlog(WLOG_NOTICE, "fatal: writing access log: %s", strerror(errno));
			exit(8);
		}
	}

	wlog(WLOG_NOTICE, "wlogwriter terminating");
	exit(0);
}
	
