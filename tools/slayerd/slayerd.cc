/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
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

#include <string>
#include <iostream>
#include <fstream>
#include <algorithm>
#include <stdexcept>
#include <vector>
#include <map>
#include <set>
#include <cerrno>

#include <sys/types.h>
#include <sys/mman.h>
#include <sys/stat.h>
#include <unistd.h>
#include <pwd.h>
#include <signal.h>
#include <syslog.h>

#include <boost/filesystem/path.hpp>
#include <boost/filesystem/operations.hpp>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>

namespace fs = boost::filesystem;

namespace {
	std::string PATH_PROC = "/proc";
	std::string SENDMAIL = "/usr/lib/sendmail";
}

struct process {
	process(fs::path const &pth);

	pid_t _pid;
	std::string _comm;
	char _state;
	pid_t _ppid;
	pid_t _pgrp;
	pid_t _sid;
	int _tty;
	pid_t _tpgid;
	unsigned long _flags;
	unsigned long _minflt;
	unsigned long _cminflt;
	unsigned long _majflt;
	unsigned long _cmajflt;
	unsigned long _utime;
	unsigned long _stime;
	long _cutime;
	long _cstime;
	long _priority;
	long _itrealvalue;
	long _starttime;
	unsigned long _vsize;
	long _rss;
	unsigned long _rlim;
	unsigned long _startcode;
	unsigned long _endcode;
	unsigned long _stackstart;
	unsigned long _kstkesp;
	unsigned long _kstkeip;
	unsigned long _signal;
	unsigned long _blocked;
	unsigned long _sigignore;
	unsigned long _sigcatch;
	unsigned long _wchan;
	unsigned long _nswap;
	unsigned long _cnswap;
	int _exit_signal;
	int _processor;
	unsigned long _rt_priority;
	unsigned long _policy;
	long _nice;
	uid_t _uid;

	void _read_proc_data(fs::path const &);
};

process::process(fs::path const &pth)
	: _pid(boost::lexical_cast<pid_t>(pth.leaf()))
{
	struct stat st;
	if (::stat(pth.native_directory_string().c_str(), &st) == -1)
		throw std::runtime_error("could not stat proc dir");
	_uid = st.st_uid;

	_read_proc_data(pth);
}

void
process::_read_proc_data(fs::path const &pth)
{
	std::ifstream f((pth / "stat").native_file_string().c_str());
	std::string sline;

	if (!f)
		throw std::runtime_error("could not read line from stat");

	long dummy;
	if (!(f >> _pid >> _comm >> _state >> _ppid >> _pgrp >> _sid >> _tty >> _tpgid
		>> _flags >> _minflt >> _cminflt >> _majflt >> _cmajflt >> _utime
		>> _stime >> _cutime >> _cstime >> _priority >> _nice >> dummy >> _itrealvalue
		>> _starttime >> _vsize >> _rss >> _rlim >> _startcode >> _endcode
		>> _stackstart >> _kstkesp >> _kstkeip >> _signal >> _blocked >> _sigignore
		>> _sigcatch >> _wchan >> _nswap >> _cnswap >> _exit_signal >> _processor
		>> _rt_priority >> _policy
	))
		throw std::runtime_error("could not parse stat line");
}

std::string
username(uid_t uid)
{
	struct passwd *p;
	if ((p = getpwuid(uid)) == 0)
		return boost::lexical_cast<std::string>(uid);
	return std::string(p->pw_name);
}

uid_t
uid(std::string const &username)
{
	struct passwd *p;
	if ((p = getpwnam(username.c_str())) == 0)
		return -1;
	return p->pw_uid;
}

template<typename C>
struct directory_enumerator {
	C &list;

	directory_enumerator(C &list) : list(list) {}

	void operator() (fs::path const &pth) const {
		/*
		 * Ensure it is actually a pid.
		 */
		try {
			boost::lexical_cast<pid_t>(pth.leaf());
		} catch (boost::bad_lexical_cast const &) {
			return;
		}

		try {
			list.push_back(process(pth));
		} catch (...) {}
	}
};

template<typename C>
directory_enumerator<C>
enumerate_directory(C &list) {
	return directory_enumerator<C>(list);
}

struct user {
	user() : uid(-1), rss(0) {}

	uid_t uid;
	unsigned long rss;
	std::vector<process> processes;
};

/*
 * A sort comparator that uses a particular struct field.
 */
template<typename S, typename T, T (S::*F)>
bool
field_comparator(S const &a, S const &b)
{
	return b.*F < a.*F;
}

void
version(void) {
	std::cerr << "slayerd $Revision$\n";
	std::cerr << "Copyright (C) 2007, River Tarnell <river@attenuate.org>.\n";
}

void
usage(void) {
	std::cerr <<
"usage: slayerd [-vh] -l <limit> -t <thread> [-e <user>]\n"
;
}

void
log(std::string const &m)
{
	syslog(LOG_NOTICE, "%s", m.c_str());
}

void
sendmail(std::string const &username, std::string const &message)
{
	std::string cmd = str(boost::format("%s -oi -bm -- %s") % SENDMAIL % username);
	FILE *p = popen(cmd.c_str(), "w");
	if (p == 0) {
		log(str(boost::format("cannot send mail using %s: %s") % SENDMAIL % std::strerror(errno)));
		return;
	}

	fwrite(message.data(), message.size(), 1, p);
	pclose(p);
}

int
main(int argc, char **argv)
{
	int delay = 10, pagesize = sysconf(_SC_PAGE_SIZE);
	std::size_t limit = 0, thresh = 0;
	int c;
	std::set<uid_t> exempt;

	char nodename[255];
	gethostname(nodename, sizeof nodename);

	while ((c = getopt(argc, argv, "l:t:e:vh")) != -1) {
		switch (c) {
			case 'l':
				try {
					limit = boost::lexical_cast<std::size_t>(optarg) * 1024 * 1024;
				} catch (boost::bad_lexical_cast &) {
					std::cerr << boost::format("\"%s\" is not a valid number\n") % optarg;
					return 1;
				}
				break;

			case 't':
				try {
					thresh = boost::lexical_cast<std::size_t>(optarg) * 1024 * 1024;
				} catch (boost::bad_lexical_cast &) {
					std::cerr << boost::format("\"%s\" is not a valid number\n") % optarg;
					return 1;
				}
				break;

			case 'd':
				try {
					delay = boost::lexical_cast<std::size_t>(optarg);
				} catch (boost::bad_lexical_cast &) {
					std::cerr << boost::format("\"%s\" is not a valid number\n") % optarg;
					return 1;
				}
				break;

			case 'e':
				uid_t u;
				if ((u = uid(optarg)) == -1) {
					std::cerr << boost::format("user \"%s\" does not exist\n") % optarg;
					return 1;
				}
				exempt.insert(u);
				break;

			case 'v':
				version();
				return 0;

			case 'h':
				version();
				usage();
				return 0;

			default:
				version();
				usage();
				return 1;
		}
	}
	argc -= optind;
	argv += optind;

	if (limit == 0 || thresh == 0) {
		usage();
		return 1;
	}

	openlog("slayerd", LOG_PID, LOG_DAEMON);

	if (daemon(0, 0) == -1) {
		std::cerr << boost::format("cannot daemonise: %s\n") % std::strerror(errno);
		return 1;
	}

	if (mlockall(MCL_CURRENT | MCL_FUTURE) == -1) 
		log(str(boost::format("warning: cannot lock memory: %s\n") % std::strerror(errno)));

	log(str(boost::format("delay: %d, limit: %dM, threshold: %dM\n")
		% delay % (limit / 1024 / 1024) % (thresh / 1024 / 1024)));

	for (;;) {
		fs::path proc(PATH_PROC);
		std::vector<process> processes;

		std::for_each(fs::directory_iterator(proc), fs::directory_iterator(),
				enumerate_directory(processes));

		/*
		 * Aggregate the processes by user.
		 */
		std::vector<user> users;
		for (std::size_t i = 0, end = processes.size(); i < end; ++i) {
			process &p = processes[i];
			user *u = 0;

			for (std::size_t ui = 0, uend = users.size(); ui != uend; ++ui)
				if (users[ui].uid == p._uid) {
					u = &users[ui];
					break;
				}

			if (u == 0) {
				std::size_t n = users.size();
				users.resize(n + 1);
				users[n].uid = p._uid;
				u = &users[n];
			}

			u->rss += p._rss;
			u->processes.push_back(p);
		}

		/*
		 * Sort user by RSS.
		 */
		std::sort(users.begin(), users.end(), field_comparator<user, unsigned long, &user::rss>);

		for (std::size_t i = 0, end = users.size(); i < end; ++i) {
			user &u = users[i];
			std::size_t bytes = u.rss * pagesize;

			if (exempt.find(u.uid) != exempt.end())
				continue;

			if (bytes < limit)
				continue;

			std::string uname = username(u.uid);
			std::string message = str(boost::format(
"From: slayerd <slayerd@%1%>\n"
"To: %2% <%2%@%1%>\n"
"Subject: Excessive memory usage from your processes.\n"
"Reply-To: Wikimedia Toolserver Administrators <ts-admins@wikimedia.org>\n"
"X-Mailer: slayerd $Revision$\n"
"\n"
"This message was automatically generated by slayerd on %1%.\n"
"\n"
"Hello,\n"
"\n"
"One or more of your processes on the host %1%\n"
"were exceeding the configured memory limit, which is %3% megabytes.\n"
"I have killed enough of your processes to bring your usage back to the\n"
"threshold limit, which is %4% megabytes.\n"
"\n"
"These are the processes I killed:\n"
"\n"
		) % nodename % uname % (limit / 1024 / 1024) % (thresh / 1024 / 1024));

			log(str(boost::format("user \"%s\" is using %dM, over configured limit %dM")
						% uname
						% (bytes / 1024 / 1024)
						% (limit / 1024 / 1024)));

			std::sort(u.processes.begin(), u.processes.end(), field_comparator<process, long, &process::_rss>);

			while (bytes >= thresh && !u.processes.empty()) {
				process &p = u.processes[0];
				std::string comm = p._comm.substr(1);
				comm.resize(comm.size() - 1);

				kill(p._pid, SIGKILL);

				log(str(boost::format("    killed process \"%s\" (pid %d) using %dM, usage now %dM")
						% comm % p._pid
						% (p._rss * pagesize / 1024 / 1024)
						% ((bytes - p._rss * pagesize) / 1024 / 1024)));

				message += str(boost::format("    %s (pid %d), using %d megabyte(s)\n")
						% comm % p._pid % (p._rss * pagesize / 1024 / 1024));

				bytes -= p._rss * pagesize;
				u.processes.erase(u.processes.begin());
			}

			log(str(boost::format("    usage is now within acceptable limits (%dM)")
					% (bytes / 1024 / 1024)));

			message += str(boost::format(
"\n"
"Your total memory usage is now %d megabyte(s).\n"
"\n"
"Excessive memory usage is usually a symptom of a broken program.  Please\n"
"investigate the cause of the problem and fix it before you restart these\n"
"processes.\n"
"\n"
"Regards,\n"
"      slayerd (the process slayer)\n"
	) % (bytes / 1024 / 1024));

			sendmail(uname, message);
		}

		sleep(delay);
	}
}
