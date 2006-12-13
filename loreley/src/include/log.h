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

using std::ofstream;

#include "loreley.h"
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

	void debug (format const &f) {		log(ll_debug, str(f));	}
	void notice(format const &f) {		log(ll_notice, str(f));}
	void warn  (format const &f) {		log(ll_warn, str(f));	}
	void error (format const &f) {		log(ll_error, str(f));	}

	void debug (string const &s) {		log(ll_debug, s);	}
	void notice(string const &s) {		log(ll_notice, s);	}
	void warn  (string const &s) {		log(ll_warn, s);	}
	void error (string const &s) {		log(ll_error, s);	}

	void log(log_level l, string const &);

private:
	bool		 _syslog;
	int		 _facility;
	log_level	 _level;
	string		 _file;

	ofstream	*_fp;
	lockable	 _lock;

	void _log_unlocked(log_level l, string const &);
};

extern logger wlog;

/**
 * A class to easily rate-limit certain log messages, so the error log isn't
 * spammed too much.
 *
 * Example usage:
 *
 *\code
 * rate_limited_logger rlog(5, ll_error, "%d + %d = %d")
 * rlog.log(1, 1, 2);
 * rlog.log(2, 2, 4);
 * sleep(5);
 * rlog.log(3, 3, 6);
 * \code
 *
 * Only the first and third messages will be logged.
 */
struct rate_limited_logger {
	/**
	 * Construct a new limited logger.
	 * \param nsecs Minimum number of seconds between logging instances of
	 * this message.
	 * \param lev Log level of this message.
	 * \param msg Format-string should this message.
	 */
	rate_limited_logger(int nsecs, log_level lev, string const &msg)
		: _nsecs(nsecs)
		, _lev(lev)
		, _format(msg)
		, _last(0)
	{}

	/**
	 * Log a message with no arguments.  Note that the msg is still a
	 * format string and needs appropriate escaping.
	 */
	void log(void) {
		if (!again())
			return;

		wlog.log(_lev, str(_format));
	}

	/**
	 * Log a message with one argument.	
	 */
	template<typename arg1_type>
	void log(arg1_type const &arg1) {
		if (!again())
			return;

		format f(_format);
		f % arg1;
		wlog.log(_lev, str(f));
	}

	/**
	 * Log a message with two arguments.
	 */
	template<typename arg1_type, typename arg2_type>
	void log(arg1_type const &arg1, arg2_type const &arg2) {
		if (!again())
			return;

		format f(_format);
		f % arg1 % arg2;
		wlog.log(_lev, str(f));
	}

	/**
	 * Log a message with three arguments.
	 */
	template<typename arg1_type, typename arg2_type, typename arg3_type>
	void log(arg1_type const &arg1, arg2_type const &arg2, 
		 arg3_type const &arg3) {
		if (!again())
			return;

		format f(_format);
		f % arg1 % arg2 % arg3;
		wlog.log(_lev, str(f));
	}

private:
	bool again(void) {
	time_t	now = time(0);
		if (_last + _nsecs < now) {
			_last = now;
			return true;
		}
		return false;
	}

	int		_nsecs;
	log_level	_lev;
	format		_format;
	int		_last;
};

#ifndef LORELEY_DEBUG
# define WDEBUG(x) ((void)0)
#else
# define WDEBUG(x) wlog.debug(x)
#endif

#endif
