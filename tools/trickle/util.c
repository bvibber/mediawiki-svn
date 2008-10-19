/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * trickle: copy one directory to another, slowly.
 */

#pragma ident "@(#) $Id$"

#include <stdio.h>
#include <unistd.h>
#include <stdlib.h>
#include <errno.h>
#include <string.h>

#ifdef __STDC__
# include <stdarg.h>
# define T_VA_START(ap,a) va_start(ap, a)
# define STD_VARARGS
#else
# include <varargs.h>
# define T_VA_START(ap,a) va_start(a)
#endif

#include "trickle.h"

#ifndef STD_VARARGS
char *
allocf(fmt, va_alist)
	const char *fmt;
	va_dcl
#else
char *
allocf(const char *fmt, ...)
#endif
{
	FILE *f;
	size_t len;
	char *s;
	va_list args;
	if ((f = fopen("/dev/null", "w")) == NULL) {
		perror("/dev/null");
		exit(8);
	}
	T_VA_START(args, fmt);
	len = vfprintf(f, fmt, args);
	va_end(args);
	fclose(f);
	s = malloc(len + 1);
	T_VA_START(args, fmt);
	vsprintf(s, fmt, args);
	va_end(args);
	return s;
}

#ifndef STD_VARARGS
void
fatal(fmt, va_alist)
	const char *fmt;
	va_dcl
#else
void
fatal(const char *fmt, ...)
#endif
{
	va_list args;
	fprintf(stderr, "%s: fatal: ", progname);
	T_VA_START(args, fmt);
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
}

#if defined(__sun) && !defined(__svr4__)
extern int sys_nerr;
extern char *sys_errlist[];

const char *strerror(n)
	int n;
{
	if (n >= sys_nerr)
		return "Unknown error";
	return sys_errlist[n];
}
#endif
