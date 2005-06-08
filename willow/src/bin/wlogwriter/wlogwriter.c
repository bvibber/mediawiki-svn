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

#include "config.h"

int
main(argc, argv)
	int argc;
	char **argv;
{
	FILE	*outf;
	char	*line;
	size_t	 lnsz;

	lnsz = 8192;
	line = malloc(lnsz);

	(void)signal(SIGPIPE, SIG_IGN);

	if (argc < 2) {
		(void)fprintf(stderr, "not enough arguments\n");
		exit(8);
	}
	
#ifdef HAVE_SETPROCTITLE
	setproctitle("log writer: %s", argv[1]);
#endif

	/*LINTED unsafe fopen*/
	if ((outf = fopen(argv[1], "a")) == NULL) {
		perror(argv[1]);
		exit(8);
	}

	while (fgets(line, lnsz, stdin)) {
		if (fputs(line, outf) == EOF || fflush(outf) == EOF) {
			exit(8);
		}
	}

	exit(0);
	/*NOTREACHED*/
}
