/* Copyright (c) 2007-2009 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include	<sys/wait.h>
#include	<pwd.h>
#include	<syslog.h>

#include	<cerrno>
#include	<cstring>
#include	<iostream>
#include	<map>
#include	<fstream>
#include	<boost/format.hpp>
#include	<boost/lexical_cast.hpp>

#include	"util.h"
#include	"config.h"

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

void
log(std::string const &m)
{
	if (config.debug)
		std::cerr << m << '\n';
	else
		syslog(LOG_NOTICE, "%s", m.c_str());
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

