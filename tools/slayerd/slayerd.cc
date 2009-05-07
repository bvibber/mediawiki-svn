/* Copyright (c) 2007-2009 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

/*
 * slayerd: monitor user activity and regulate users using too much RAM.
 */

#include	<sys/wait.h>
#include	<sys/mman.h>
#include	<syslog.h>

#include	<cerrno>
#include	<cstring>
#include	<csignal>
#include	<iostream>
#include	<map>
#include	<fstream>
#include	<boost/format.hpp>
#include	<boost/lexical_cast.hpp>

#include	"process.h"
#include	"config.h"
#include	"util.h"

namespace {
	std::string CONFFILE = "/etc/slayerd/slayerd.conf";
}

struct user {
	user() : uid(-1), rss(0) {}

	uid_t uid;
	std::size_t rss;
	std::vector<process::pointer> processes;
};

void run_once();
void check_user(user &u);

void
version(void) {
	std::cerr << "slayerd " << VERSION << "\n";
	std::cerr << "Copyright (C) 2007, River Tarnell <river@attenuate.org>.\n";
}

void
usage(void) {
	std::cerr << "usage: slayerd [-fvh] [-c <conf>]\n"
;
}

void
rmpidfile(void)
{
	unlink(config.pidfile.c_str());
}

void
do_signal(int sig)
{
	rmpidfile();
	_exit(0);
}

bool
do_pidfile(void)
{
	{
		std::ifstream pf(config.pidfile.c_str());
		if (pf) {
			std::string pid;
			std::getline(pf, pid);
			std::cerr << boost::format("slayerd already running (pid %d) or stale pidfile %s\n")
				% pid % config.pidfile;
			return false;
		}
	}

	std::ofstream pf(config.pidfile.c_str());
	if (!pf) {
		std::cerr << boost::format("cannot open pidfile %s: %s\n")
			% config.pidfile % std::strerror(errno);
		return false;
	}

	pf << getpid() << '\n';
	atexit(rmpidfile);
	signal(SIGINT, do_signal);
	signal(SIGTERM, do_signal);
	return true;
}

int
main(int argc, char **argv)
{
	int fflag = 0;
	int c;

	config.exempt.insert(0);	/* root is always exempt */

	while ((c = getopt(argc, argv, "fvhc:D")) != -1) {
		switch (c) {
			case 'f':
				fflag++;
				break;

			case 'c':
				CONFFILE = optarg;
				break;

			case 'v':
				version();
				return 0;

			case 'h':
				version();
				usage();
				return 0;

			case 'D':
				config.debug = true;
				fflag++;
				break;

			default:
				version();
				usage();
				return 1;
		}
	}
	argc -= optind;
	argv += optind;

	if (!config.debug)
		openlog("slayerd", LOG_PID, LOG_DAEMON);

	if (!configure(CONFFILE))
		return 1;

	if (config.limit == 0 || config.thresh == 0) {
		std::cerr << "invalid limit/threshold\n";
		return 1;
	}

	if (!fflag && daemon(0, 0) == -1) {
		std::cerr << boost::format("cannot daemonise: %s\n") % std::strerror(errno);
		return 1;
	}

	if (!fflag && !do_pidfile())
		return 1;

	if (mlockall(MCL_CURRENT | MCL_FUTURE) == -1) 
		log(str(boost::format("warning: cannot lock memory: %s") % std::strerror(errno)));

	log(str(boost::format("delay: %d, limit: %dM, threshold: %dM")
		% config.delay % (config.limit / 1024 / 1024) % (config.thresh / 1024 / 1024)));

	for (;;) {
		run_once();
		sleep(config.delay);
	}
}

struct process_sort_rss {
	bool operator() (process::pointer a, process::pointer b) const {
		return a->rss() > b->rss();
	}
};

void
run_once()
{
	std::vector<process::pointer> processes(enumerate_processes());

	/*
	 * Aggregate the processes by user.
	 */
	std::vector<user> users;
	for (std::size_t i = 0, end = processes.size(); i < end; ++i) {
		process::pointer p = processes[i];
		user *u = 0;

		for (std::size_t ui = 0, uend = users.size(); ui != uend; ++ui)
			if (users[ui].uid == p->uid()) {
				u = &users[ui];
				break;
			}

		if (u == 0) {
			std::size_t n = users.size();
			users.resize(n + 1);
			users[n].uid = p->uid();
			u = &users[n];
		}

		u->rss += p->rss();
		u->processes.push_back(p);
	}

	for (std::size_t i = 0, end = users.size(); i < end; ++i) {
		check_user(users[i]);
	}
}

void
check_user(user &u)
{
	std::size_t bytes = u.rss;

	if (config.exempt.find(u.uid) != config.exempt.end())
		return;

	if (config.minuid >= 0 && u.uid < config.minuid)
		return;

	if (bytes < config.limit)
		return;

	std::string uname = username(u.uid);
	std::string process_list;

	log(str(boost::format("user \"%s\" is using %dM, over configured limit %dM")
				% uname
				% (bytes / 1024 / 1024)
				% (config.limit / 1024 / 1024)));

	std::sort(u.processes.begin(), u.processes.end(), process_sort_rss());

	while (bytes >= config.thresh && !u.processes.empty()) {
		process::pointer p = u.processes[0];

		std::string const &comm = p->command();
		std::string const &cm = p->cmdline();

		if (!config.debug)
			kill(p->pid(), SIGKILL);

		std::size_t thissize = std::size_t(p->rss());

		log(str(boost::format("    killed process \"%s\" (pid %d) using %dM, usage now %dM")
				% comm % p->pid()
				% (thissize / 1024 / 1024)
				% ((bytes - thissize) / 1024 / 1024)));

		process_list += str(boost::format("    %s (pid %d), using %d megabyte(s).\n"
						  "       command: %s\n")
				% comm % p->pid() % (thissize / 1024 / 1024) % p->cmdline());

		bytes -= thissize;
		u.processes.erase(u.processes.begin());
	}

	log(str(boost::format("    usage is now within acceptable limits (%dM)")
			% (bytes / 1024 / 1024)));

	char nodename[255];
	gethostname(nodename, sizeof nodename);

	std::map<std::string, std::string> msgvars;
	msgvars["limit"] = boost::lexical_cast<std::string>(config.limit / 1024 / 1024);
	msgvars["thresh"] = boost::lexical_cast<std::string>(config.thresh / 1024 / 1024);
	msgvars["user"] = uname;
	msgvars["hostname"] = nodename;
	msgvars["current"] = boost::lexical_cast<std::string>(bytes / 1024 / 1024);
	msgvars["processes"] = process_list;

	std::string message = replace_file(config.mailmessage, msgvars);
	if (!config.debug && !message.empty())
		sendmail(uname, message);
}
