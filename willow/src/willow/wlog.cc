/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wlog: logging.
 */

#if defined __SUNPRO_CC || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
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

#include "config.h"

#include "wlog.h"
#include "wnet.h"
#include "wconfig.h"
#include "format.h"

struct log_variables logging;
static lockable log_lock;

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

void
wlog_init(void)
{
	if (logging.syslog)
		openlog("willow", LOG_PID, logging.facility);

	if (logging.file.empty())
		return;

	/*LINTED unsafe fopen*/
	logging.fp = fopen(logging.file.c_str(), "a");
	if (logging.fp == NULL) {
		wlog(WLOG_ERROR, format("cannot open error log file %s: %s")
			% logging.file % strerror(errno));
		exit(8);
	}
}

void
wlog(int sev, string const &e)
{
string	r;

	if (sev > WLOG_MAX)
		sev = WLOG_NOTICE;
	if (sev < logging.level)
		return;

	r = str(format("%s| %s: %s") % current_time_short % sev_names[sev] % e);

	HOLDING(log_lock);	
	if (logging.syslog)
		syslog(syslog_pri[sev], "%s", e.c_str());

	if (logging.fp) {
		if (fprintf(logging.fp, "%s\n", r.c_str()) < 0) {
			fclose(logging.fp);
			logging.fp = NULL;
			wlog(WLOG_ERROR, format("writing to logfile: %s")
				% strerror(errno));
			exit(8);
		}
	}
	
	if (config.foreground)
		cout << r << '\n';
}

void
wlog_close(void)
{
	if (logging.fp && fclose(logging.fp) == EOF) {
		logging.fp = NULL;
		wlog(WLOG_WARNING, format("closing logfile: %s")
			% strerror(errno));
	}
	
	if (logging.syslog)
		closelog();
}
