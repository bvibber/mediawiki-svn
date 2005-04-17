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
#include <string.h>
#include <syslog.h>

#include "wlog.h"
#include "wnet.h"
#include "wconfig.h"

struct log_variables logging;

static const char *sev_names[] = {
	"Notice",
	"Warning",
	"Error",
};

static const int syslog_pri[] = {
	LOG_INFO,
	LOG_WARNING,
	LOG_ERR,
};

void
wlog_init(void)
{
	if (logging.syslog)
		openlog("willow", LOG_PID, logging.facility);

	if (!logging.file)
		return;

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
	int i;

	if (sev > WLOG_MAX)
		sev = WLOG_NOTICE;
	if (sev < logging.level)
		return;
	va_start(ap, fmt);
	i = sprintf(s, "%s| %s: ", current_time_short, sev_names[sev]);
	vsnprintf(s + i, 1021 - i, fmt, ap);
	if (logging.syslog)
		syslog(syslog_pri[sev], "%s", s + i);
	strcat(s, "\n");
	if (logging.fp)
		fputs(s, logging.fp);
	if (config.foreground)
		fputs(s, stderr);
	va_end(ap);
	free(s);
}

void
wlog_close(void)
{
	fclose(logging.fp);
}
