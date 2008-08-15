/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<iostream>
#include	<cstdio>
#include	<cerrno>
#include	<cstring>
#include	<map>
#include	<string>
#include	<functional>
#include	<iterator>
#include	<csignal>

#include	<sys/types.h>
#include	<sys/socket.h>
#include	<sys/un.h>
#include	<sys/wait.h>

#include	<netinet/in.h>
#include	<arpa/inet.h>

#include	<pthread.h>
#include	<pwd.h>
#include	<grp.h>
#include	<fcntl.h>
#include	<netdb.h>
#include	<cstdio>

#include	<boost/format.hpp>
#include	<log4cxx/logger.h>
#include	<log4cxx/propertyconfigurator.h>

#include	"fcgi.h"
#include	"request_thread.h"
#include	"config.h"
#include	"version.h"
#include	"process_factory.h"

	extern "C" void *	acceptor_thread(void *);

extern "C" void
sigexit(int)
{
	std::exit(0);
}

int
main(int argc, char **argv)
{
	std::string initlog(CONFDIR "/initlog.conf");
	std::string conf(CONFDIR "/main.conf");
	int c, dflag = 0;

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
		std::cerr << boost::format("switchboard: initial log configuration file \"%s\" "
				"cannot be read: %s\n")
			% initlog % std::strerror(errno);
		return 1;
	}

	log4cxx::LoggerPtr mainlogger(log4cxx::Logger::getLogger("main"));
	log4cxx::PropertyConfigurator::configure(initlog);

	LOG4CXX_INFO(mainlogger, "switchboard " SB_VERSION " starting up...");

	configuration_loader loader;
	if (!loader.load(conf, mainconf)) {
		LOG4CXX_ERROR(mainlogger, "cannot load configuration");
		return 1;
	}
    
	if (mainconf.sockdir.empty()) {
		LOG4CXX_ERROR(mainlogger, "sockdir must be specified");
		return 1;
	}

	if (mainconf.userdir.empty()) {
		LOG4CXX_ERROR(mainlogger, "userdir must be specified");
		return 1;
	}

	if (mainconf.docroot.empty()) {
		LOG4CXX_ERROR(mainlogger, "docroot must be specified");
		return 1;
	}

	if (mainconf.servtype == serv_unknown) {
		LOG4CXX_ERROR(mainlogger, "server type not set");
		return 1;
	}

	LOG4CXX_INFO(mainlogger, boost::format("loaded configuration from \"%s\"") % conf);

	for (std::size_t i = 0; i < mainconf.listeners.size(); ++i) {
		if (mainconf.listeners[i].host[0] == '/') {
			struct sockaddr_un addr;
			int lsnsock;
			std::memset(&addr, 0, sizeof(addr));
			std::strncpy(addr.sun_path, mainconf.listeners[i].host.c_str(), sizeof(addr.sun_path) - 1);
			addr.sun_family = AF_UNIX;

			unlink(addr.sun_path);

			if ((lsnsock = socket(AF_UNIX, SOCK_STREAM, 0)) == -1) {
				LOG4CXX_ERROR(mainlogger,
					boost::format("%s: cannot create socket: %s\n")
					% mainconf.listeners[i].host % std::strerror(errno));
				return 1;
			}

			int one = 1;
			setsockopt(lsnsock, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));
			fcntl(lsnsock, F_SETFD, FD_CLOEXEC);

			if (bind(lsnsock, (sockaddr *) &addr, sizeof(addr)) == -1) {
				LOG4CXX_ERROR(mainlogger,
					boost::format("%s: cannot bind: %s\n")
					% mainconf.listeners[i].host % std::strerror(errno));
				return 1;
			}

			if (listen(lsnsock, 50) == -1) {
				LOG4CXX_ERROR(mainlogger,
					boost::format("%s cannot listen: %s\n")
					% mainconf.listeners[i].host % std::strerror(errno));
				return 1;
			}

			pthread_t tid;
			pthread_create(&tid, NULL, acceptor_thread, reinterpret_cast<void *>((intptr_t) lsnsock));
		} else {
			int r;
			struct addrinfo hints, *res, *iter;
			std::memset(&hints, 0, sizeof(hints));
			hints.ai_flags = AI_PASSIVE;
			hints.ai_socktype = SOCK_STREAM;

			if ((r = getaddrinfo(mainconf.listeners[i].host.c_str(),
						mainconf.listeners[i].port.c_str(),
						&hints, &res)) != 0) {
				LOG4CXX_ERROR(mainlogger, 
					boost::format("cannot resolve [%s]:%s: %s\n")
					% mainconf.listeners[i].host
					% mainconf.listeners[i].port
					% gai_strerror(r));
				return 1;
			}

			for (iter = res; iter; iter = iter->ai_next) {
				char ahost[NI_MAXHOST], aserv[NI_MAXSERV];
				int lsnsock;

				getnameinfo(iter->ai_addr, iter->ai_addrlen, 
						ahost, sizeof(ahost), aserv, sizeof(aserv),
						AI_NUMERICHOST | AI_ADDRCONFIG);

				if ((lsnsock = socket(iter->ai_family, iter->ai_socktype, iter->ai_protocol)) == -1) {
					LOG4CXX_ERROR(mainlogger,
						boost::format("[%s]:%s: cannot create socket: %s\n")
						% ahost % aserv % std::strerror(errno));
					return 1;
				}

				int one = 1;
				setsockopt(lsnsock, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));
				fcntl(lsnsock, F_SETFD, FD_CLOEXEC);

				if (bind(lsnsock, iter->ai_addr, iter->ai_addrlen) == -1) {
					LOG4CXX_ERROR(mainlogger,
						boost::format("[%s]:%s: cannot bind: %s\n")
						% ahost % aserv % std::strerror(errno));
					return 1;
				}

				if (listen(lsnsock, 50) == -1) {
					LOG4CXX_ERROR(mainlogger,
						boost::format("[%s]:%s: cannot listen: %s\n")
						% ahost % aserv % std::strerror(errno));
					return 1;
				}

				pthread_t tid;
				pthread_create(&tid, NULL, acceptor_thread, reinterpret_cast<void *>((intptr_t) lsnsock));
			}

			freeaddrinfo(res);
		}
	}

	std::string logconf = CONFDIR "/log.conf";
	if (!mainconf.logconf.empty()) 
		logconf = mainconf.logconf;

	if (access(logconf.c_str(), R_OK) == -1) {
		LOG4CXX_WARN(mainlogger,
			boost::format("main log configuration \"%s\" cannot be read: %s")
			% logconf % std::strerror(errno));
		return 1;
	} else {
		LOG4CXX_INFO(mainlogger, boost::format("re-initialising logging from \"%s\"")
			% logconf);
		log4cxx::PropertyConfigurator::configure(logconf);
	}

	if (!getuid()) {
		struct passwd *pw;
		struct group *gr;

		if ((pw = getpwnam(SB_USER)) == NULL) {
			LOG4CXX_ERROR(mainlogger,
				boost::format("cannot setuid to \"%s\": user lookup failed: %s")
				% SB_USER % std::strerror(errno));
			return 1;
		}

		if ((gr = getgrnam(SB_GROUP)) == NULL) {
			LOG4CXX_ERROR(mainlogger,
				boost::format("cannot setgid to \"%s\": group lookup failed: %s")
				% SB_USER % std::strerror(errno));
			return 1;
		}

		if (!getgid()) {
			if (setgid(gr->gr_gid) == -1) {
				LOG4CXX_ERROR(mainlogger,
					boost::format("cannot setgid to \"%s\": %s")
					% SB_GROUP % std::strerror(errno));
				return 1;
			}
		}

		if (initgroups(SB_USER, gr->gr_gid) == -1) {
			LOG4CXX_ERROR(mainlogger,
					boost::format("cannot initialise supplemental group list: %s")
					% std::strerror(errno));
			return 1;
		}

		if (!getuid()) {
			if (setuid(pw->pw_uid) == -1) {
				LOG4CXX_ERROR(mainlogger,
					boost::format("cannot setuid to \"%s\": %s")
					% SB_USER % std::strerror(errno));
				return 1;
			}
		}

		LOG4CXX_INFO(mainlogger, 
				boost::format("now running as %s:%s")
				% SB_USER % SB_GROUP);
	}

	/*
	 * Make sure our sockdir is valid, and has the right permissions.
	 */
dostat:
	struct stat sb;
	if (stat(mainconf.sockdir.c_str(), &sb) == -1) {
		if (errno == ENOENT) {
			if (mkdir(mainconf.sockdir.c_str(), 0750) == -1) {
				LOG4CXX_ERROR(mainlogger,
					boost::format("cannot create sockdir \"%s\": %s")
					% mainconf.sockdir % std::strerror(errno));
				return 1;
			}

			LOG4CXX_INFO(mainlogger,
				boost::format("created sockdir \"%s\"")
				% mainconf.sockdir);
			goto dostat;
		} else {
			LOG4CXX_ERROR(mainlogger,
				boost::format("cannot stat sockdir \"%s"": %s")
				% mainconf.sockdir % std::strerror(errno));
			return 1;
		}
	}

	if (sb.st_uid != getuid()) {
		LOG4CXX_ERROR(mainlogger,
			boost::format("sockdir \"%s\" is not owned by my uid %d")
			% mainconf.sockdir % getuid());
		return 1;
	}

	if (sb.st_gid != getgid()) {
		LOG4CXX_ERROR(mainlogger,
			boost::format("sockdir \"%s\" is not owned by my gid %d")
			% mainconf.sockdir % getgid());
		return 1;
	}

	if ((sb.st_mode & S_IRWXO) != 0) {
		LOG4CXX_ERROR(mainlogger,
			boost::format("sockdir \"%s\" has wrong mode (access for others)")
			% mainconf.sockdir);
		return 1;
	}


	if (!dflag) {
		LOG4CXX_INFO(mainlogger, "detaching to background...");
		switch (fork()) {
		case -1:
			LOG4CXX_INFO(mainlogger, boost::format("cannot fork: %s")
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

	std::signal(SIGTERM, sigexit);
	std::signal(SIGINT, sigexit);
	std::signal(SIGPIPE, SIG_IGN);
	std::signal(SIGCHLD, SIG_IGN);

	process_factory::instance().cleanup_thread();
}

extern "C" void *
acceptor_thread(void *arg)
{
	int fd = reinterpret_cast<intptr_t>(arg);
	int newfd;
	struct sockaddr addr;
	socklen_t addrlen = sizeof(addr);

	while ((newfd = accept(fd, &addr, &addrlen)) != -1) {
		request_thread *req = new request_thread(newfd);
		req->start();
	}

	std::printf("accept failed: %s\n", std::strerror(errno));
	return NULL;
}
