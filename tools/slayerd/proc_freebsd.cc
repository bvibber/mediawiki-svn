/* Copyright (c) 2007-2009 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include	<sys/stat.h>
#include	<sys/user.h>
#include	<sys/param.h>
#include	<sys/sysctl.h>
#include	<unistd.h>
#include	<kvm.h>
#include	<fcntl.h>

#include	<string>
#include	<iostream>
#include	<fstream>
#include	<boost/shared_ptr.hpp>
#include	<boost/filesystem.hpp>
#include	<boost/lexical_cast.hpp>

#include	"process.h"

struct process_freebsd : process {
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
	kvm_t *kvm;
	int nprocs;
	struct kinfo_proc *procs;
	std::vector<process::pointer> processes;
	int pagesz = sysconf(_SC_PAGE_SIZE);

	if ((kvm = kvm_open(NULL, NULL, NULL, O_RDONLY, NULL)) == NULL)
		throw std::runtime_error("kvm_open failed");

	procs = kvm_getprocs(kvm, KERN_PROC_PROC, 0, &nprocs);
	for (int i = 0; i < nprocs; ++i) {
		process_freebsd *p = new process_freebsd;

		char **argv = kvm_getargv(kvm, &procs[i], 2048);
		if (!argv) {
			delete p;
			continue;
		} else {
			for (std::size_t i = 0; argv[i]; ++i) {
				p->_fullcomm += argv[i];
				p->_fullcomm += " ";
			}
		}

		p->_pid = procs[i].ki_pid;
		p->_uid = procs[i].ki_uid;
		p->_vsize = procs[i].ki_size;
		p->_rss = procs[i].ki_rssize * pagesz;
		p->_comm = procs[i].ki_comm;

		processes.push_back(process::pointer(p));
	}

	kvm_close(kvm);
	return processes;
}
