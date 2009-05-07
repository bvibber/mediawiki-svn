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

#include	<string>
#include	<fstream>
#include	<boost/shared_ptr.hpp>
#include	<boost/filesystem.hpp>
#include	<boost/lexical_cast.hpp>

#include	"process.h"

namespace fs = boost::filesystem;

namespace {
	std::string PATH_PROC = "/proc";
}

struct process_linux : process {
	process_linux(fs::path const &pth);

	pid_t pid() const { return _pid; }
	std::string command() const { return _comm; }
	std::string cmdline() const { return _fullcomm; }
	std::size_t rss() const { return _mres; }
	std::size_t vsize() const { return _vsize; }
	uid_t uid() const { return _uid; }

	pid_t _pid;
	/* short command from /proc/{pid}/stat */
	std::string _comm;
	/* fulll command from /proc/{pid}/cmdline */
	std::string _fullcomm;
	unsigned long _vsize;
	long _rss;
	uid_t _uid;
	int _mres;

	void _read_proc_data(fs::path const &);
};

process_linux::process_linux(fs::path const &pth)
	: _pid(boost::lexical_cast<pid_t>(pth.leaf()))
{
	struct stat st;
	if (::stat(pth.native_directory_string().c_str(), &st) == -1)
		throw std::runtime_error("could not stat proc dir");
	_uid = st.st_uid;

	_read_proc_data(pth);
}

void
process_linux::_read_proc_data(fs::path const &pth)
{
	/* Everything that we can collect, but that doesn't belong in process. */
	unsigned long _flags, _minflt, _cminflt, _majflt, _cmajflt, _utime, _stime;
	long _cutime, _cstime, _priority, _itrealvalue, _starttime;
	unsigned long _rlim, _startcode, _endcode, _stackstart, _kstkesp, _kstkeip;
	unsigned long _signal, _blocked, _sigignore, _sigcatch, _wchan, _nswap, _cnswap;
	int _exit_signal, _processor;
	unsigned long _rt_priority, _policy;
	int _mshare, _mtext, _mlib, _mdata;
	pid_t _ppid, _pgrp, _sid, _tty, _tpgid, _state, _msize, _nice;
	int pagesz = sysconf(_SC_PAGE_SIZE);

	{
		std::ifstream f((pth / "stat").native_file_string().c_str());

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

	{
		std::ifstream f((pth / "statm").native_file_string().c_str());

		if (!f)
			throw std::runtime_error("could not read line from stat");

		if (!(f >> _msize >> _mres >> _mshare >> _mtext >> _mlib >> _mdata))
			throw std::runtime_error("could not parse statm line");
	}

	{
		std::ifstream f((pth / "cmdline").native_file_string().c_str());

		if (!f)
			throw std::runtime_error("could not read line from cmdline");

		if (!std::getline(f, _fullcomm))
			throw std::runtime_error("could not parse cmdline");
	}

	_mres *= pagesz;
	/* command is (%s) formatted, strip parentheses. */
	_comm = _comm.substr(1);
	_comm.resize(_comm.size() - 1);

	/* arguments are \0 separated, use spaces for display */
	std::replace(_fullcomm.begin(), _fullcomm.end(), '\0', ' ');
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
			list.push_back(process::pointer(new process_linux(pth)));
		} catch (...) {}
	}
};

template<typename C>
directory_enumerator<C>
enumerate_directory(C &list) {
	return directory_enumerator<C>(list);
}

std::vector<process::pointer>
enumerate_processes()
{
	std::vector<process::pointer> processes;
	fs::path proc(PATH_PROC);

	std::for_each(fs::directory_iterator(proc), fs::directory_iterator(),
			enumerate_directory(processes));
	return processes;
}
