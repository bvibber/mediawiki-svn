#include <string>
#include <iostream>
#include <fstream>
#include <algorithm>
#include <stdexcept>
#include <vector>
#include <map>
#include <sys/stat.h>
#include <unistd.h>
#include <pwd.h>
#include <boost/filesystem/path.hpp>
#include <boost/filesystem/operations.hpp>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>

namespace fs = boost::filesystem;

namespace {
	std::string PATH_PROC = "/proc";
}

struct process {
	process(fs::path const &pth);

	pid_t pid() const { return _pid; }
	std::string comm() const { return _comm; }
	unsigned long time_diff() const { return _time_diff; }
	unsigned long time() const { return _time; }
	long rss() const { return _rss; }
	double time_diff_secs() const {
		return static_cast<double>(_time_diff) / sysconf(_SC_CLK_TCK);
	}

	void set_time_diff(unsigned long n) {
		_time_diff = n;
	}

	uid_t uid() const { return _uid; }

private:
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

	unsigned long _time_diff;
	unsigned long _time;

	void _read_proc_data(fs::path const &);
};

struct process_list {
	typedef std::vector<process>::size_type size_type;
	typedef std::vector<process>::const_iterator iterator;
	typedef std::vector<process>::const_iterator const_iterator;

	void push_back(process const &p);
	size_type size() const { return procs.size(); }
	iterator begin() const { return procs.begin(); }
	iterator end() const { return procs.end(); }

	iterator find_by_pid(pid_t) const;
	process const &operator[] (size_type i) const { return procs[i]; }

	static std::vector<process> highest_cpu(process_list const &, process_list const &);
	static bool sort_by_time_diff(process const &, process const &);

private:
	std::vector<process> procs;
};

struct enumerate_directory {
	process_list &list;

	enumerate_directory(process_list &list) : list(list) {}

	void operator() (fs::path const &pth) const {
		/*
		 * Ensure it is actually a pid.
		 */
		try {
			boost::lexical_cast<pid_t>(pth.leaf());
		} catch (boost::bad_lexical_cast const &) {
			return;
		}

		list.push_back(process(pth));
	}
};

process::process(fs::path const &pth)
	: _pid(boost::lexical_cast<pid_t>(pth.leaf()))
	, _time_diff(0)
{
	struct stat st;
	if (::stat(pth.native_directory_string().c_str(), &st) == -1)
		throw std::runtime_error("could not stat proc dir");
	_uid = st.st_uid;

	_read_proc_data(pth);
	_time = _utime + _stime;

}

void
process::_read_proc_data(fs::path const &pth)
{
	std::ifstream f((pth / "stat").native_file_string().c_str());
	std::string sline;

	if (!f)
		throw std::runtime_error("could not read line from stat");

	long dummy;
	// 1 (init) S 0 1 1 0 -1 4194560 592 4233908 21 9550 2 69 124250 34578 
	// 15 0 1 0 9 2084864 143 4294967295 134512640 134544676 3215730864 
	// 3215729548 10183682 0 0 1475401980 671819267 0 0 0 0 0 0 0 160
	//
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

void
process_list::push_back(process const &p)
{
	procs.push_back(p);
}

process_list::iterator
process_list::find_by_pid(pid_t pid) const
{
	for (iterator it = procs.begin(), end = procs.end(); it != end; ++it)
		if (it->pid() == pid)
			return it;
	return end();
}

std::vector<process>
process_list::highest_cpu(
		process_list const &a,
		process_list const &b
		)
{
	std::vector<process> result;

	for (int i = 0, end = a.size(); i < end; ++i) {
		iterator it = b.find_by_pid(a[i].pid());
		if (it == b.end())
			/* process has gone away... */
			continue;
		process n(*it);
		if (a[i].time() > it->time())
			std::cout << "ack!!\n";
		n.set_time_diff(it->time() - a[i].time());
		result.push_back(n);
	}

	std::sort(result.begin(), result.end(), sort_by_time_diff);
	return result;
}

bool
process_list::sort_by_time_diff(process const &a, process const &b)
{
	return a.time_diff() > b.time_diff();
}

struct user_desc {
	user_desc() : time(0), nproc(0), rss(0) {}
	double time_diff;
	unsigned long time;
	int nproc;
	uid_t uid;
	unsigned long rss;
};

std::string
fmttime(unsigned long time)
{
	time /= sysconf(_SC_CLK_TCK);
	unsigned long hr = time / (60 * 60);
	time %= 60 * 60;
	unsigned long mn = time / 60;
	time %= 60;
	unsigned long sc = time;
	return str(boost::format("%02d:%02d:%02d") % hr % mn % sc);
}

bool
sort_by_time_diff(user_desc const &a, user_desc const &b)
{
	return b.time_diff < a.time_diff;
}

std::string
username(uid_t uid)
{
	struct passwd *p;
	if ((p = getpwuid(uid)) == 0)
		return boost::lexical_cast<std::string>(uid);
	return std::string(p->pw_name).substr(0, 8);
}

std::string
fmtpages(int pages)
{
	unsigned long bytes = pages * sysconf(_SC_PAGESIZE);
	if (bytes > (1024 * 1024 * 1024))
		return str(boost::format("%dG") % (bytes / (1024 * 1024 * 1024)));
	if (bytes > (1024 * 1024))
		return str(boost::format("%dM") % (bytes / (1024 * 1024)));
	if (bytes > 1024)
		return str(boost::format("%dK") % (bytes / 1024));
	return str(boost::format("%d ") % bytes);
}

int
main(int argc, char **argv)
{
	fs::path	proc(PATH_PROC);
	process_list	a, b;
	int		delay = 1;

	if (argv[1])
		delay = boost::lexical_cast<int>(argv[1]);

	std::for_each(fs::directory_iterator(proc), fs::directory_iterator(), enumerate_directory(a));
	sleep(delay);
	std::for_each(fs::directory_iterator(proc), fs::directory_iterator(), enumerate_directory(b));

	std::vector<process> top = process_list::highest_cpu(a, b);
	std::map<uid_t, user_desc> users;

	for (int i = 0, end = top.size(); i < end; ++i) {
		users[top[i].uid()].time_diff += top[i].time_diff_secs();
		users[top[i].uid()].time += top[i].time();
		users[top[i].uid()].rss += top[i].rss();
		users[top[i].uid()].nproc++;
	}

	std::vector<user_desc> sorted;
	for (std::map<uid_t, user_desc>::iterator it = users.begin(), end = users.end();
	     it != end; ++it)
	{
		user_desc &d = it->second;
		d.uid = it->first;
		if (d.time_diff <= 0)
			continue;
		sorted.push_back(d);
	}

	std::sort(sorted.begin(), sorted.end(), sort_by_time_diff);
	std::cout << boost::format("NPROC USERNAME      RSS      TIME   CPU\n");
	for (int i = 0; i < sorted.size(); ++i) {
		std::string perc = str(boost::format("%.1f%%") % ((sorted[i].time_diff / delay) * 100));
		std::cout << boost::format("% 5d %-9s %7s %9s %6s\n")
				% sorted[i].nproc
				% username(sorted[i].uid)
				% fmtpages(sorted[i].rss)
				% fmttime(sorted[i].time)
				% perc;
	}
}
