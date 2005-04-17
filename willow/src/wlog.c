/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wlog: logging.
 */

#include <stdio.h>
#include <stdarg.h>
#include <stdlib.h>
#include <stdarg.h>

#include "wlog.h"

static const char *sev_names[] = {
	"notice",
	"warning",
	"error",
};

void
wlog_init(void)
{
	logging.fp = fopen(logging.file, "a");
	if (logging.fp == NULL) {
		perror(logging.file);
		exit(8);
	}
}

void
wlog(int sev, const char *fmt, ...)
{
	char *s = malloc(1024);
	va_list ap;

	if (sev > WLOG_MAX)
		sev = WLOG_NOTICE;
	if (sev > logging.level)
		return;
	va_start(ap, fmt);
	sprintf(s, "%s: ", sev_names[sev]);
	vsnprintf(s, 1021-strlen(sev_names[sev]), fmt, ap);
	strcat(s, "\n");
	fputs(s, logging.fp);
	if (logging.level == WLOG_DEBUG)
		fputs(s, stderr);
	va_end(ap);
	free(s);
}

void
wlog_close(void)
{
	fclose(logging.fp);
}
