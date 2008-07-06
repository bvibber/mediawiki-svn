/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

/*
 * Switchboard: a FastCGI-based request dispatcher for PHP with setuid
 * support.
 */

#include	<iostream>
#include	<cassert>

#include	<signal.h>
#include	<pwd.h>
#include	<grp.h>

#include	<boost/asio.hpp>
#include	<boost/format.hpp>
#include	<log4cxx/logger.h>
#include	<log4cxx/propertyconfigurator.h>

#include	"async_read_fcgi_record.h"
#include	"fcgi.h"

namespace asio = boost::asio;
using asio::ip::tcp;
using boost::format;

#include	"fcgi.h"
#include	"fcgi_listener.h"
#include	"sbcontext.h"
#include    "config.h"

namespace {
	std::vector<fcgi_listener *> listeners;
}

extern "C" void
sigexit(int)
{
	std::exit(0);
}

int
main(int argc, char **argv)
{
	sbcontext context;
	log4cxx::LoggerPtr mainlogger(log4cxx::Logger::getLogger("switchboard.main"));

	if (access(CONFDIR "/initlog.conf", R_OK) == -1) {
		std::fprintf(stderr, "switchboard: initial log configuration file \"%s\" "
				"cannot be read: %s\n",
				CONFDIR "/initlog.conf", std::strerror(errno));
		return 1;
	}

	log4cxx::PropertyConfigurator::configure(CONFDIR "/initlog.conf");

	LOG4CXX_INFO(mainlogger, "Starting up...");

	if (!getuid() || !getgid()) {
		struct passwd *pw;
		struct group *gr;

		if ((pw = getpwnam(SB_USER)) == NULL) {
			LOG4CXX_ERROR(mainlogger,
				format("cannot setuid to \"%s\": user lookup failed: %s")
				% SB_USER % std::strerror(errno));
			return 1;
		}

		if ((gr = getgrnam(SB_GROUP)) == NULL) {
			LOG4CXX_ERROR(mainlogger,
				format("cannot setgid to \"%s\": group lookup failed: %s")
				% SB_USER % std::strerror(errno));
			return 1;
		}

		if (!getgid()) {
			if (setgid(gr->gr_gid) == -1) {
				LOG4CXX_ERROR(mainlogger,
					format("cannot setgid to \"%s\": %s")
					% SB_GROUP % std::strerror(errno));
				return 1;
			}
		}

		if (initgroups(SB_USER, gr->gr_gid) == -1) {
			LOG4CXX_ERROR(mainlogger,
					format("cannot initialise supplemental group list: %s")
					% std::strerror(errno));
			return 1;
		}

		if (!getuid()) {
			if (setuid(pw->pw_uid) == -1) {
				LOG4CXX_ERROR(mainlogger,
					format("cannot setuid to \"%s\": %s")
					% SB_USER % std::strerror(errno));
				return 1;
			}
		}

		LOG4CXX_INFO(mainlogger, 
				format("now running as %s:%s")
				% SB_USER % SB_GROUP);
	}

	configuration_loader loader;
	if (!loader.load(CONFDIR "/main.conf", mainconf)) {
		LOG4CXX_ERROR(mainlogger, "cannot load configuration");
		return 1;
	}
    
	LOG4CXX_INFO(mainlogger, format("loaded configuration from \"%s\"")
			% CONFDIR "/main.conf");

	for (std::vector<conf_listener>::iterator
		it = mainconf.listeners.begin(),
		end = mainconf.listeners.end();
		it != end; ++it)
	{
		tcp::resolver resolver(context.service());
		tcp::resolver::query listenq(it->host, it->port);
		tcp::resolver::iterator rit = resolver.resolve(listenq);
		tcp::resolver::iterator rend;

		for (; rit != rend; ++rit) {
			LOG4CXX_INFO(mainlogger, format("Listening on %s[%s]:%d")
					% it->host
					% rit->endpoint().address()
					% rit->endpoint().port());
			try {
				listeners.push_back(new fcgi_listener(context, rit->endpoint()));
			} catch (std::exception &e) {
				LOG4CXX_ERROR(mainlogger, format("could not create FCGI listener: %s")
						% e.what());
				return 1;
			}
		}
	}

	std::string logconf = CONFDIR "/log.conf";
	if (!mainconf.logconf.empty()) 
		logconf = mainconf.logconf;

	if (access(logconf.c_str(), R_OK) == -1) {
		LOG4CXX_WARN(mainlogger,
			format("main log configuration \"%s\" cannot be read: %s")
			% logconf % std::strerror(errno));
		return 1;
	} else {
		LOG4CXX_INFO(mainlogger, format("re-initialising logging from \"%s\"")
			% logconf);
		log4cxx::PropertyConfigurator::configure(logconf);
	}

	signal(SIGTERM, sigexit);
	signal(SIGINT, sigexit);

	context.service().run();
}
