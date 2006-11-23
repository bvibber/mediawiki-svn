/* @(#) $Id: wlog.h 17869 2006-11-23 01:05:44Z river $ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * log: logging.
 */

#ifndef WLOG_H
#define WLOG_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id: wlog.h 17869 2006-11-23 01:05:44Z river $"
#endif

#include <cstdio>
#include <string>
#include <fstream>
using std::ofstream;

#include "willow.h"
#include "autoconf.h"
#include "ptalloc.h"

enum log_level {
	ll_debug,
	ll_notice,
	ll_warn,
	ll_error
};

struct logger : noncopyable {
	logger();

	void syslog	(bool, int facility = 0);
	void level	(log_level);
	bool file	(string const &fname);

	bool open(void);
	void close(void);

	void debug (format const &f) {		_log(ll_debug, str(f));	}
	void notice(format const &f) {		_log(ll_notice, str(f));}
	void warn  (format const &f) {		_log(ll_warn, str(f));	}
	void error (format const &f) {		_log(ll_error, str(f));	}

	void debug (string const &s) {		_log(ll_debug, s);	}
	void notice(string const &s) {		_log(ll_notice, s);	}
	void warn  (string const &s) {		_log(ll_warn, s);	}
	void error (string const &s) {		_log(ll_error, s);	}

private:
	bool		 _syslog;
	int		 _facility;
	log_level	 _level;
	string		 _file;

	ofstream	*_fp;
	lockable	 _lock;

	void _log(log_level l, string const &);
};

extern logger wlog;

#ifndef WILLOW_DEBUG
# define WDEBUG(x) ((void)0)
#else
# define WDEBUG(x) wlog.debug(x)
#endif

#endif
