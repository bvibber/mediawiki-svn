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

#include	<string>
#include	<iostream>
#include	<fstream>
#include	<algorithm>
#include	<stdexcept>
#include	<vector>
#include	<map>
#include	<set>
#include	<cerrno>
#include	<cstring>

#include	<sys/types.h>
#include	<sys/mman.h>
#include	<sys/stat.h>
#include	<sys/wait.h>
#include	<unistd.h>
#include	<pwd.h>
#include	<signal.h>
#include	<syslog.h>

#include	<boost/filesystem/path.hpp>
#include	<boost/filesystem/operations.hpp>
#include	<boost/lexical_cast.hpp>
#include	<boost/format.hpp>
#include	<boost/shared_ptr.hpp>

#include	"process.h"

namespace fs = boost::filesystem;

namespace {
	std::string CONFFILE = "/etc/slayerd/slayerd.conf";
}

struct config_t {
	std::size_t limit;
	std::size_t thresh;
	int delay;
	std::string mailmessage;
	std::string sendmail;
	std::string pidfile;
	std::set<uid_t> exempt;
	int minuid;
	bool debug;

	config_t()
		: limit(0)
		, minuid(-1)
		, thresh(0)
		, delay(60)
		, mailmessage(ETCDIR "/mailmessage")
		, sendmail("/usr/lib/sendmail -oi -bm --")
		, pidfile("/var/run/slayerd.pid")
		, debug(false)
	{}
} config;

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

struct user {
	user() : uid(-1), rss(0) {}

	uid_t uid;
	std::size_t rss;
	std::vector<process::pointer> processes;
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

template<typename S, typename T, T (S::*F)(void) const>
bool
ptmf_comparator(boost::shared_ptr<S> const &a, boost::shared_ptr<S> const &b)
{
	return ((*b).*F)() < ((*a).*F)();
}

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
log(std::string const &m)
{
	if (config.debug)
		std::cerr << m << '\n';
	else
		syslog(LOG_NOTICE, "%s", m.c_str());
}

void
sendmail(std::string const &username, std::string const &message)
{
	char const *const args[] = {
		config.sendmail.c_str(),
		"-oi",
		"-bm",
		"--",
		username.c_str(),
		0
	};
	int fds[2];
	pid_t pid;

	if (pipe(fds) == -1) {
		log(str(boost::format("while sending mail: pipe: %s") % 
					std::strerror(errno)));
		return;
	}

	switch (pid = fork()) {
	case 0:
		if (dup2(fds[0], 0) == -1) {
			log(str(boost::format("mail child: dup2: %s") % 
						std::strerror(errno)));
			_exit(1);
		}

		close(fds[0]);
		close(fds[1]);
		if (execv(config.sendmail.c_str(), const_cast<char *const 
					*>(args)) == -1)
			log(str(boost::format("mail child: execv: %s") % 
						std::strerror(errno)));
		_exit(1);

	case -1:
		log(str(boost::format("sending mail: fork: %s") % 
					std::strerror(errno)));
		return;

	default:
		close(fds[0]);
		write(fds[1], message.data(), message.size());
		close(fds[1]);
	}

	int status;
	wait(&status);
	if (WIFEXITED(status)) {
		int ret = WEXITSTATUS(status);
		if (ret != 0)
			log(str(boost::format("sending mail: child exited with status %d") % ret));
	} else if (WIFSIGNALED(status)) {
		log(str(boost::format("sending mail: child exited with signal %d") % WTERMSIG(status)));
	}
}

std::string
replace_file(std::string const &filename, std::map<std::string, std::string> const &vars)
{
	std::string file;
	std::string result;

	std::ifstream f(filename.c_str());
	if (!f) {
		log(str(boost::format("cannot open %s: %s") % filename % std::strerror(errno)));
		return "";
	}

	std::string line;
	while (std::getline(f, line)) {
		std::string::size_type i, j;
		while ((i = line.find('%')) != std::string::npos) {
			if ((j = line.find('%', i + 1)) == std::string::npos) {
				result += line;
				line = "";
				break;
			}

			result += line.substr(0, i);
			std::string var = line.substr(i + 1, j - i - 1);
			std::map<std::string, std::string>::const_iterator it = vars.find(var);

			if (it == vars.end()) {
				result += line.substr(i);
				line = "";
				break;
			}

			result += it->second;
			line = line.substr(j + 1);
		}

		result += line;
		result += '\n';
	}

	return result;
}

bool
configure(void)
{
	std::ifstream conffile(CONFFILE.c_str());
	if (!conffile) {
		std::string err = str(boost::format("cannot open %s: %s") % CONFFILE % std::strerror(errno));
		std::cerr << err << '\n';
		log(err);
		return false;
	}

	std::string line;
	int lineno = 0;
	while (std::getline(conffile, line)) {
		++lineno;

		if (line[0] == '#')
			continue;
		if (line.empty())
			continue;

		std::istringstream l(line);
		std::string d;
		if (!(l >> d))
			continue;

		if (d == "limit") {
			if (!(l >> config.limit)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid limit") % CONFFILE % lineno);
				std::cerr << err << '\n';
				log(err);
				return false;
			}
			config.limit *= 1024 * 1024;
		} else if (d == "thresh") {
			if (!(l >> config.thresh)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid threshold") % CONFFILE % lineno);
				std::cerr << err << '\n';
				log(err);
				return false;
			}
			config.thresh *= 1024 * 1024;
		} else if (d == "exempt") {
			std::string username;
			if (!(l >> username)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid username") % CONFFILE % lineno);
				std::cerr << err << '\n';
				log(err);
				return false;
			}

			uid_t id = uid(username);
			if (id == -1) {
				std::string err = str(boost::format("\"%s\", line %d: user \"%s\" does not exist") 
						% CONFFILE % lineno % username);
				std::cerr << err << '\n';
				log(err);
				return false;
			}
			config.exempt.insert(id);
		} else if (d == "delay") {
			if (!(l >> config.delay)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid delay") % CONFFILE % lineno);
				std::cerr << err << '\n';
				log(err);
				return false;
			}
		} else if (d == "mailmessage") {
			if (!(l >> config.mailmessage)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid mail message") % CONFFILE % lineno);
				std::cerr << err << '\n';
				log(err);
				return false;
			}
		} else if (d == "sendmail") {
			if (!(l >> config.sendmail)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid sendmail command") % CONFFILE % lineno);
				std::cerr << err << '\n';
				log(err);
				return false;
			}
		} else if (d == "pidfile") {
			if (!(l >> config.pidfile)) {
				std::string err = str(boost::format("\"%s\", line %d: invalid pid file") % CONFFILE % lineno);
				std::cerr << err << '\n';
				log(err);
				return false;
			}
		} else if (d == "minuid") {
			if (!(l >> config.minuid) || config.minuid < 0) {
				std::string err = str(boost::format("\"%s\", line %d: invalid minimum uid") % CONFFILE % lineno);
				std::cerr << err << '\n';
				log(err);
				return false;
			}
		} else {
			std::string err = str(boost::format("\"%s\", line %d: invalid directive") % CONFFILE % lineno);
			std::cerr << err << '\n';
			log(err);
			return false;
		}
	}

	return true;
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
	int pagesize = sysconf(_SC_PAGE_SIZE), fflag = 0;
	int c;

	config.exempt.insert(0);	/* root is always exempt */

	char nodename[255];
	gethostname(nodename, sizeof nodename);

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

	if (!configure())
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

		/*
		 * Sort user by RSS.
		 */
		std::sort(users.begin(), users.end(), field_comparator<user, std::size_t, &user::rss>);

		for (std::size_t i = 0, end = users.size(); i < end; ++i) {
			user &u = users[i];
			std::size_t bytes = std::size_t(u.rss) * pagesize;

			if (config.exempt.find(u.uid) != config.exempt.end())
				continue;

			if (config.minuid >= 0 && u.uid < config.minuid)
				continue;

			if (bytes < config.limit)
				continue;

			std::string uname = username(u.uid);
			std::string process_list;

			log(str(boost::format("user \"%s\" is using %dM, over configured limit %dM")
						% uname
						% (bytes / 1024 / 1024)
						% (config.limit / 1024 / 1024)));

			std::sort(u.processes.begin(), u.processes.end(), ptmf_comparator<process, std::size_t, &process::rss>);

			while (bytes >= config.thresh && !u.processes.empty()) {
				process::pointer p = u.processes[0];

				/* command is (%s) formatted, strip parentheses. */
				std::string comm = p->command().substr(1);
				comm.resize(comm.size() - 1);

				/* arguments are \0 separated, use spaces for display */
				std::string cm = p->cmdline();
				std::replace(cm.begin(), cm.end(), '\0', ' ');

				if (!config.debug)
					kill(p->pid(), SIGKILL);

				std::size_t thissize = std::size_t(p->rss()) * pagesize;

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

		sleep(config.delay);
	}
}
