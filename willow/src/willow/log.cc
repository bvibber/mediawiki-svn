/* @(#) $Id: wlog.cc 17805 2006-11-20 14:07:17Z river $ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * log: logging.
 */

#if defined __SUNPRO_CC || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id: wlog.cc 17805 2006-11-20 14:07:17Z river $"
#endif

#include <boost/format.hpp>

#include <cstdio>
#include <cstdarg>
#include <cstdlib>
#include <cstring>
#include <syslog.h>
#include <cerrno>
#include <iostream>
using std::cout;
using std::fopen;
using std::fprintf;
using std::fclose;

#include "autoconf.h"

#include "log.h"
#include "net.h"
#include "config.h"
#include "format.h"

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
		openlog("willow", LOG_PID, _facility);

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
logger::_log(log_level sev, string const &e)
{
string	r;

	if (sev < _level)
		return;

	r = str(format("%s| %s: %s") % current_time_short % sev_names[sev] % e);

	HOLDING(_lock);
	if (_syslog)
		::syslog(syslog_pri[sev], "%s", e.c_str());

	if (_fp) {
		if (!(*_fp << r << '\n')) {
			_fp->close();
			wlog.error(format("writing to logfile: %s")
				% strerror(errno));
			exit(8);
		}
	}
	
	if (config.foreground)
		cout << r << '\n';
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
