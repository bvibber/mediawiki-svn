/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* log: logging.							*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef WLOG_H
#define WLOG_H

#include <cstdio>
#include <string>
#include <fstream>
using std::ofstream;

#include "loreley.h"
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
	void _log_unlocked(log_level l, string const &);
};

extern logger wlog;

#ifndef LORELEY_DEBUG
# define WDEBUG(x) ((void)0)
#else
# define WDEBUG(x) wlog.debug(x)
#endif

#endif
