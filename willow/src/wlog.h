/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wlog: logging.
 */

#ifndef WLOG_H
#define WLOG_H

#define WLOG_NOTICE 0
#define WLOG_WARNING 1
#define WLOG_MAX 1

void wlog_init(void);
void wlog(int, const char *, ...);

#endif

