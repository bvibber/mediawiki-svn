/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wlog: logging.
 */

#include <stdio.h>
#include <stdarg.h>

#include "wlog.h"

static const char *sev_names[] = {
	"notice",
	"warning"
};

void
wlog_init(void)
{
}

void
wlog(int sev, const char *fmt, ...)
{
	va_list ap;
	va_start(ap, fmt);
	if (sev > WLOG_MAX)
		sev = WLOG_NOTICE;
	fprintf(stderr, "%s: ", sev_names[sev]);
	vfprintf(stderr, fmt, ap);
	fputs("\n", stderr);
	va_end(ap);
}
