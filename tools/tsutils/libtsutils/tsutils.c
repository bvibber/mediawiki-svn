/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include	<sys/mman.h>
#include	<sys/stat.h>
#include	<fcntl.h>
#include	<stdlib.h>
#include	<string.h>
#include	<stdarg.h>
#include	<syslog.h>
#include	<stdio.h>
#include	<unistd.h>
#include	"tsutils.h"

char *
realloc_strncat(str, add, len)
	char *str;
	char const *add;
	size_t len;
{
size_t	 newlen;

	newlen = len;
	if (str)
		newlen += strlen(str);
	str = realloc(str, newlen + 1);
	(void) strncat(str, add, len);
	return str;
}

char *
realloc_strcat(str, add)
	char *str;
	char const *add;
{
	return realloc_strncat(str, add, strlen(add));
}

void
strdup_free(s, new)
	char **s;
	char const *new;
{
	if (*s)
		free(*s);

	*s = strdup(new);
}

static int foreground = 1;

void
logmsg(char const *msg, ...)
{
va_list	ap;
	va_start(ap, msg);

	if (foreground) {
		(void) vfprintf(stderr, msg, ap);
		(void) fputs("\n", stderr);
	} else
		vsyslog(LOG_NOTICE, msg, ap);

	va_end(ap);
}

int
daemon_detach(progname)
	char const *progname;
{
	if (daemon(0, 0) < 0)
		return -1;
	openlog(progname, LOG_PID, LOG_DAEMON);
	foreground = 0;
	return 0;
}

char *
file_to_string(path)
	char const *path;
{
int		 i;
void		*addr;
struct stat	 st;
char		*str;

	if ((i = open(path, O_RDONLY)) == -1) {
		return NULL;
	}

	if (fstat(i, &st) == -1) {
		(void) close(i);
		return NULL;
	}

	if ((addr = mmap(0, st.st_size, PROT_READ, MAP_PRIVATE, i, 0)) == MAP_FAILED) {
		(void) close(i);
		return NULL;
	}

	str = malloc(st.st_size + 1);
	(void) memcpy(str, addr, st.st_size);
	str[st.st_size] = '\0';

	(void) munmap(addr, st.st_size);
	(void) close(i);

	return str;
}
