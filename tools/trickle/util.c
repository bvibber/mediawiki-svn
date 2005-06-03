/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * trickle: copy one directory to another, slowly.
 */

#pragma ident "@(#) $Id$"

#include <stdio.h>
#include <unistd.h>
#include <varargs.h>
#include <stdlib.h>
#include <errno.h>
#include <string.h>

#include "trickle.h"

char *
allocf(fmt, va_alist)
	const char *fmt;
	va_dcl
{
	FILE *f;
	size_t len;
	char *s;
	va_list args;
	if ((f = fopen("/dev/null", "w")) == NULL) {
		perror("/dev/null");
		exit(8);
	}
	va_start(args);
	len = vfprintf(f, fmt, args);
	va_end(args);
	fclose(f);
	s = malloc(len + 1);
	va_start(args);
	vsprintf(s, fmt, args);
	va_end(args);
	return s;
}

void
fatal(fmt, va_alist)
	const char *fmt;
	va_dcl
{
	va_list args;
	fprintf(stderr, "%s: fatal: ", progname);
	va_start(args);
	vfprintf(stderr, fmt, args);
	va_end(args);
	fputs("\n", stderr);
	exit(8);
}

void
pfatal(c, e)
	const char *c, *e;
{
	fprintf(stderr, "%s: %s (%s)\n", e, strerror(errno), c);
	exit(8);
}
