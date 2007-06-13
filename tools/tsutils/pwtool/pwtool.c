/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

/*
 * pwtool: allow users to edit their shell and realname.
 */

#define _GNU_SOURCE

#include	<sys/types.h>
#include	<sys/stat.h>
#include	<sys/capability.h>
#include	<stdio.h>
#include	<string.h>
#include	<errno.h>
#include	<pwd.h>
#include	<fcntl.h>
#include	<unistd.h>
#include	<ctype.h>
#include	<stdlib.h>
#include	<signal.h>

#define PASSWD		"/etc/passwd"
#define TMPFILE		"/etc/passwd.tmp"

static int isvalidshell(char const *);
static int isvalidgecos(char const *);
static void cleanup(void);

static int outfd = -1;

cap_t caps;

int
main(argc, argv)
	int argc;
	char *argv[];
{
int		 infd;
FILE		*inf, *outf;
struct stat	 st;
struct passwd	*pwd;
uid_t		 me;
int		 c;
char		*newshell = NULL, *newgecos = NULL;
cap_value_t	 wantcaps[] = { CAP_CHOWN };

	(void) umask(022);

	if ((caps = cap_init()) == NULL) {
		(void) fprintf(stderr, "cannot initialise capabilities: %s\n",
				strerror(errno));
		return 1;
	}

	/*
	 * Drop all capabilities from our effective and inheritable sets.  
	 * Leave CAP_CHOWN in permitted because we need it for fchown later.
	 */
	cap_clear(caps);
	cap_set_flag(caps, CAP_PERMITTED, sizeof(wantcaps) / sizeof(*wantcaps), wantcaps, CAP_SET);
	
	if (cap_set_proc(caps) == -1) {
		(void) fprintf(stderr, "cannot set capabilities: %s\n",
				strerror(errno));
		return 1;
	}

	while ((c = getopt(argc, argv, "s:g:h")) != -1) {
		switch (c) {
		case 's':
			newshell = optarg;
			break;

		case 'g':
			newgecos = optarg;
			break;

		case 'h':
			(void) fprintf(stderr, "usage: pwtool [-h] [-s shell] [-g gecos]\n");
			return 0;

		default:
			return 1;
		}
	}
	argc -= optind;
	argv += optind;

	if (argc != 0) {
		(void) fprintf(stderr, "usage: pwtool [-s shell] [-g gecos]\n");
		return 1;
	}

	if (!newgecos && !newshell)
		return 0;


	if (newshell && !isvalidshell(newshell)) {
		(void) fprintf(stderr, "pwtool: \"%s\" is not a valid login shell\n",
				newshell);
		return 1;
	}

	if (newgecos && !isvalidgecos(newgecos)) {
		(void) fprintf(stderr, "pwtool: GECOS may only contain ASCII alphanumerics and whitespace\n");
		return 1;
	}

	me = getuid();

	if ((infd = open(PASSWD, O_RDONLY)) == -1) {
		(void) fprintf(stderr, "pwtool: cannot open %s: %s\n", PASSWD, strerror(errno));
		return 1;
	}

	if ((inf = fdopen(infd, "r")) == NULL) {
		(void) fprintf(stderr, "pwtool: cannot open %s: %s\n", PASSWD, strerror(errno));
		return 1;
	}

	if (fstat(infd, &st) == -1) {
		(void) fprintf(stderr, "pwtool: cannot fstat %s: %s\n", PASSWD, strerror(errno));
		return 1;
	}

	atexit(cleanup);
	signal(SIGINT, SIG_IGN);
	signal(SIGTERM, SIG_IGN);
	signal(SIGQUIT, SIG_IGN);

	if ((outfd = open(TMPFILE, O_WRONLY | O_EXCL | O_CREAT, st.st_mode)) == -1) {
		(void) fprintf(stderr, "pwtool: cannot open %s: %s\n", TMPFILE, strerror(errno));
		return 1;
	}

	/*
	 * CAP_CHOWN is needed to change the owner of the tmp file correctly.
	 */
	cap_set_flag(caps, CAP_EFFECTIVE, sizeof(wantcaps) / sizeof(*wantcaps), wantcaps, CAP_SET);

	if (cap_set_proc(caps) == -1) {
		(void) fprintf(stderr, "cannot set capabilities: %s\n",
				strerror(errno));
		return 1;
	}

	if (fchown(outfd, st.st_uid, st.st_gid) == -1) {
		(void) fprintf(stderr, "pwtool: cannot chown %s: %s\n", TMPFILE, strerror(errno));
		return 1;
	}

	/*
	 * Now we can drop CAP_CHOWN from both permitted and effective.
	 */
	cap_set_flag(caps, CAP_PERMITTED, sizeof(wantcaps) / sizeof(*wantcaps), wantcaps, CAP_CLEAR);
	cap_set_flag(caps, CAP_EFFECTIVE, sizeof(wantcaps) / sizeof(*wantcaps), wantcaps, CAP_CLEAR);

	if (cap_set_proc(caps) == -1) {
		(void) fprintf(stderr, "cannot set capabilities: %s\n",
				strerror(errno));
		return 1;
	}

	if ((outf = fdopen(outfd, "w")) == NULL) {
		(void) fprintf(stderr, "pwtool: cannot open %s: %s\n", TMPFILE, strerror(errno));
		return 1;
	}

	while ((pwd = fgetpwent(inf)) != NULL) {
		if (pwd->pw_uid == me) {
			if (newgecos)
				pwd->pw_gecos = newgecos;
			if (newshell)
				pwd->pw_shell = newshell;
		}

		if (putpwent(pwd, outf) == -1) {
			(void) fprintf(stderr, "pwtool: error writing to %s: %s",
					TMPFILE, strerror(errno));
			return 1;
		}
	}

	if (ferror(inf)) {
		(void) fprintf(stderr, "pwtool: error reading %s: %s\n",
				PASSWD, strerror(errno));
		return 1;
	}

	if (fsync(fileno(outf)) == -1) {
		(void) fprintf(stderr, "pwtool: error syncing %s: %s\n",
				TMPFILE, strerror(errno));
		return 1;
	}

	if (fclose(outf) == EOF) {
		(void) fprintf(stderr, "pwtool: error closing %s: %s\n",
				TMPFILE, strerror(errno));
		outfd = -1;
		return 1;
	}

	outfd = -1;
	fclose(inf);

	if (rename(TMPFILE, PASSWD) == -1) {
		(void) fprintf(stderr, "pwtool: cannot rename %s to %s: %s\n",
				TMPFILE, PASSWD, strerror(errno));
		return 1;
	}

	return 0;
}

static int
isvalidshell(sh)
	char const *sh;
{
char const	*s;
	setusershell();
	while ((s = getusershell()) != NULL)
		if (!strcmp(s, sh))
			return 1;
	return 0;
}

static int
isvalidgecos(ge)
	char const *ge;
{
	while (*ge) {
		if (!isascii(*ge) || !(isalnum(*ge) || isblank(*ge)))
			return 0;

		ge++;
	}

	return 1;
}

static void
cleanup()
{
	if (outfd != -1)
		(void) unlink(TMPFILE);
}
