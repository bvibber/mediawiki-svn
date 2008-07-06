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

int dflag;

int
main(int argc, char **argv)
{
	sbcontext context;
	std::string initlog(CONFDIR "/initlog.conf");
	std::string conf(CONFDIR "/main.conf");
	int c;

	log4cxx::LoggerPtr mainlogger(log4cxx::Logger::getLogger("switchboard.main"));

	while ((c = getopt(argc, argv, "df:i:")) != -1) {
		switch(c) {
		case 'd':
			dflag = 1;
			break;
		case 'f':
			conf = optarg;
			break;
		case 'i':
			initlog = optarg;
			break;
		default:
			return 1;
		}
	}	

	if (access(initlog.c_str(), R_OK) == -1) {
		std::cerr << format("switchboard: initial log configuration file \"%s\" "
				"cannot be read: %s\n")
			% initlog % std::strerror(errno);
		return 1;
	}

	log4cxx::PropertyConfigurator::configure(initlog);

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
	if (!loader.load(conf, mainconf)) {
		LOG4CXX_ERROR(mainlogger, "cannot load configuration");
		return 1;
	}
    
	if (mainconf.sockdir.empty()) {
		LOG4CXX_ERROR(mainlogger, "sockdir must be specified");
		return 1;
	}

	LOG4CXX_INFO(mainlogger, format("loaded configuration from \"%s\"")
			% conf);

	/*
	 * Make sure our sockdir is valid, and has the right permissions.
	 */
dostat:
	struct stat sb;
	if (stat(mainconf.sockdir.c_str(), &sb) == -1) {
		if (errno == ENOENT) {
			if (mkdir(mainconf.sockdir.c_str(), 0750) == -1) {
				LOG4CXX_ERROR(mainlogger,
					format("cannot create sockdir \"%s\": %s")
					% mainconf.sockdir % std::strerror(errno));
				return 1;
			}

			LOG4CXX_INFO(mainlogger,
				format("created sockdir \"%s\"")
				% mainconf.sockdir);
			goto dostat;
		} else {
			LOG4CXX_ERROR(mainlogger,
				format("cannot stat sockdir \"%s"": %s")
				% mainconf.sockdir % std::strerror(errno));
			return 1;
		}
	}

	if (sb.st_uid != getuid()) {
		LOG4CXX_ERROR(mainlogger,
			format("sockdir \"%s\" is not owned by my uid %d")
			% mainconf.sockdir % getuid());
		return 1;
	}

	if (sb.st_gid != getgid()) {
		LOG4CXX_ERROR(mainlogger,
			format("sockdir \"%s\" is not owned by my gid %d")
			% mainconf.sockdir % getgid());
		return 1;
	}

	if ((sb.st_mode & S_IRWXO) != 0) {
		LOG4CXX_ERROR(mainlogger,
			format("sockdir \"%s\" has wrong mode (access for others)")
			% mainconf.sockdir);
		return 1;
	}

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

	if (!dflag) {
		LOG4CXX_INFO(mainlogger, "detaching to background...");
		switch (fork()) {
		case -1:
			LOG4CXX_INFO(mainlogger, format("cannot fork: %s")
				% std::strerror(errno));
			return 1;
		case 0:
			break;
		default:
			return 0;
		}

		setsid();
		chdir("/");
		int fd;
		if ((fd = open("/dev/null", O_RDWR, 0)) != -1) {
			dup2(fd, STDIN_FILENO);
			dup2(fd, STDOUT_FILENO);
			dup2(fd, STDERR_FILENO);
			if (fd > STDERR_FILENO)
				close(fd);
		}
	}

	signal(SIGTERM, sigexit);
	signal(SIGINT, sigexit);

	context.service().run();
}
