/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wlog: logging.
 */

#ifndef WLOG_H
#define WLOG_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <cstdio>
#include <string>
using std::FILE;

#include "willow.h"
#include "config.h"
#include "ptalloc.h"

#define WLOG_DEBUG 0
#define WLOG_NOTICE 1
#define WLOG_WARNING 2
#define WLOG_ERROR 3
#define WLOG_MAX 3

extern struct log_variables {
	string	 file;
	int	 level;
	FILE	*fp;
	bool	 syslog;
	int	 facility;
} logging;

void wlog_init(void);
/*PRINTFLIKE2*/
void wlog(int, string const &);

template<typename charT, typename traits, typename allocator>
void wlog(int lev, basic_format<charT, traits, allocator> const &f)
{
	wlog(lev, str(f));
}

void wlog_close(void);

#ifndef WILLOW_DEBUG
# define WDEBUG(x) ((void)0)
#else
# define WDEBUG(x) wlog x
#endif

#endif
