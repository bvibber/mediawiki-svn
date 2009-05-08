/* Copyright (c) 2007-2009 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include	<sys/stat.h>
#include	<unistd.h>
#include	<dirent.h>
#include	<fcntl.h>
#include	<procfs.h>

#include	<string>
#include	<iostream>
#include	<fstream>
#include	<boost/shared_ptr.hpp>
#include	<boost/filesystem.hpp>
#include	<boost/lexical_cast.hpp>
#include	<boost/format.hpp>

#include	"process.h"

struct process_solaris : process {
	pid_t pid() const { return _pid; }
	std::string command() const { return _comm; }
	std::string cmdline() const { return _fullcomm; }
	std::size_t rss() const { return _rss; }
	std::size_t vsize() const { return _vsize; }
	uid_t uid() const { return _uid; }

	pid_t _pid;
	std::string _comm;
	std::string _fullcomm;
	std::size_t _vsize;
	uid_t _uid;
	std::size_t _rss;
};

std::vector<process::pointer>
enumerate_processes()
{
	DIR *dir;
	dirent *dent;
	std::vector<process::pointer> processes;
	int pagesz = sysconf(_SC_PAGE_SIZE);

	if ((dir = opendir("/proc")) == NULL)
		throw std::runtime_error("opendir(/proc) failed");

	while ((dent = readdir(dir)) != NULL) {
	int		fd;
	psinfo_t	info;
	std::string	infopath = boost::io::str(boost::format(
					"/proc/%s/psinfo") % dent->d_name);

		if ((fd = open(infopath.c_str(), O_RDONLY)) == -1)
			continue;

	retry:
		if (read(fd, (char *) &info, sizeof(info)) < 0) {
			close(fd);

			if (errno == EAGAIN)
				goto retry;

			continue;
		}

		close(fd);

		process_solaris *p = new process_solaris;
		p->_pid = info.pr_pid;
		p->_uid = info.pr_uid;
		p->_rss = info.pr_rssize * 1024;
		p->_vsize = info.pr_size * 1024;
		p->_comm = info.pr_fname;
		p->_fullcomm = info.pr_psargs;

		processes.push_back(process::pointer(p));
	}

	closedir(dir);
	return processes;
}
