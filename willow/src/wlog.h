/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wlog: logging.
 */

#ifndef WLOG_H
#define WLOG_H

#define WLOG_DEBUG 0
#define WLOG_NOTICE 1
#define WLOG_WARNING 2
#define WLOG_ERROR 3
#define WLOG_MAX 3

extern struct log_variables {
	char *file;
	int level;
	FILE *fp;
	int syslog;
	int facility;
} logging;

void wlog_init(void);
void wlog(int, const char *, ...);
void wlog_close(void);

#ifndef WILLOW_DEBUG
# define DEBUG(x)
#else
# define DEBUG(x) wlog x
#endif

#endif
