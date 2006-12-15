/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* log: logging.							*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
#ifdef __INTEL_COMPILER
# pragma hdrstop
#endif

using std::cout;
using std::ios;
using std::endl;

#include "log.h"
#include "net.h"
#include "config.h"

logger wlog;

static const char *sev_names[] = {
	"Debug",
	"Notice",
	"Warning",
	"Error",
};

static const int syslog_pri[] = {
	LOG_INFO,
	LOG_INFO,
	LOG_WARNING,
	LOG_ERR,
};

bool
logger::open(void)
{
	if (_syslog)
		openlog("loreley", LOG_PID, _facility);

	if (_file.empty())
		return true;

	_fp = new ofstream(_file.c_str(), ios::app);
	if (!_fp->is_open()) {
		wlog.error(format("cannot open error log file %s: %s")
			% _file % strerror(errno));
		delete _fp;
		_fp = NULL;
		return false;
	}

	return true;
}

void
logger::log(log_level sev, string const &e)
{
	HOLDING(_lock);
	_log_unlocked(sev, e);
}

void
logger::_log_unlocked(log_level sev, string const &e)
{
string	r;

	if (sev < _level)
		return;

	r = str(format("%s| %s: %s") % (char *)current_time_short % sev_names[sev] % e);

	if (_syslog)
		::syslog(syslog_pri[sev], "%s", e.c_str());

	if (_fp) {
		if (!(*_fp << r << endl)) {
			_fp->close();
			delete _fp;
			_fp = NULL;
			_log_unlocked(ll_error, 
				str(format("writing to logfile: %s")
					% strerror(errno)));
			exit(8);
		}
	}
	
	if (config.foreground)
		cout << sev_names[sev] << ": " << e << '\n';
}

void
logger::close(void)
{
	HOLDING(_lock);

	if (_fp)
		_fp->close();
	
	if (_syslog)
		closelog();
}

void
logger::syslog(bool do_, int facility)
{
	_syslog = do_;
	_facility = facility;
}

bool
logger::file(string const &file)
{
	_file = file;
	return true;
}

void
logger::level(log_level l)
{
	_level = l;
}

logger::logger(void)
	: _syslog(false)
	, _facility(0)
	, _level(ll_notice)
{
}
