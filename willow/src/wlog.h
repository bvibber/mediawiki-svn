/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wlog: logging.
 */

#ifndef WLOG_H
#define WLOG_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include "config.h"

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
/*PRINTFLIKE2*/
void wlog(int, const char *, ...);
void wlog_close(void);

#ifndef WILLOW_DEBUG
# define WDEBUG(x) ((void)0)
#else
# define WDEBUG(x) wlog x
#endif

#endif
