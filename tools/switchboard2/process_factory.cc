/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<sys/stat.h>
#include	<pwd.h>

#include	<cerrno>

#include	<boost/lambda/lambda.hpp>

#include	"process_factory.h"
#include	"config.h"
#include	"util.h"

#if 0
namespace {

extern "C" void *
do_start_cleanup_thread(void *arg)
{
	process_factory *pf = static_cast<process_factory *>(arg);
	pf->cleanup_thread();
	return NULL;
}

} // anonymous namespace
#endif

process_factory &
process_factory::instance()
{
	static process_factory inst;
	return inst;
}

process_factory::process_factory()
	: id_(0)
{
#if 0
	pthread_t tid;
	pthread_create(&tid, NULL, do_start_cleanup_thread, this);
#endif
}

process_factory::~process_factory()
{
}

void
process_factory::cleanup_thread()
{
	for (;;) {
		sleep(30);

		lock l(lock_);

		std::time_t oldest = std::time(0) - 30;

		freelist_t::index<by_released>::type &idx = freelist_.get<by_released>();
		std::pair<
			freelist_t::index<by_released>::type::iterator,
			freelist_t::index<by_released>::type::iterator>
			range = idx.range(mi::unbounded, boost::lambda::_1 <= oldest);

		idx.erase(range.first, range.second);
	}
}

processp
process_factory::get_process(
	std::map<std::string, std::string> &params)
{
	std::map<std::string, std::string>::const_iterator it;

	uid_t uid;
	gid_t gid;

	std::string script_path;

	/*
	 * Trying to find the script from path the env is a mess.  Under SJS 
	 * web server, we take the value from SCRIPT_NAME, and translate it 
	 * into a path on disk using the 'docroot' and 'userdir' configuration 
	 * options.  Under Apache, this doesn't work, because SCRIPT_NAME 
	 * contains garbage.  Instead we take PATH_TRANSLATED, which is the 
	 * on-disk path with the PATH_INFO appended, and remove path components 
	 * from it until we end up with a path which exists.
	 *
	 * Other web servers might require different handling; I haven't tested 
	 * any other than SJS and Apache.
	 */
	if (mainconf.servtype == serv_apache) {
		if ((it = params.find("PATH_TRANSLATED")) == params.end())
			throw creation_failure("PATH_TRANSLATED not specified");

		std::string s = it->second;
		struct stat sb;

		while (stat(s.c_str(), &sb) == -1
			&& errno == ENOTDIR)
		{
			std::string::size_type n;
			if ((n = s.rfind('/')) == std::string::npos)
				throw creation_failure("script not found");

			s.erase(n);
		}

		script_path = s;
	} else if (mainconf.servtype == serv_sjs) {
		if ((it = params.find("SCRIPT_NAME")) == params.end())
			throw creation_failure("neither SCRIPT_NAME nor PATH_TRANSLATED specified");

		std::string script_name = it->second;
		if (script_name.empty())
			throw creation_failure("SCRIPT_NAME is empty");

		if (script_name.size() >= 2 &&
		    script_name[0] == '/' && script_name[1] == '~') {
			/*
			 * The format is /~user/path/to/script.php
			 * We need to change it to 
			 * /home/user/public_html/path/to/script.php.
			 */
			std::string username;
			std::string script;
			script_name.erase(script_name.begin(), script_name.begin() + 2);
			if (script_name.empty())
				throw creation_failure("invalid SCRIPT_NAME");
			std::string::size_type n = script_name.find('/');
			username.assign(script_name.begin(), script_name.begin() + n);
			script.assign(script_name.begin() + n + 1, script_name.end());

			struct passwd *pwd;
			if ((pwd = getpwnam(username.c_str())) == NULL)
				throw creation_failure("user does not exist");
			script_path = std::string(pwd->pw_dir) + '/' + mainconf.userdir
				+ '/' + script;
		} else {
			/*
			 * Script is relative to docroot.
			 */
			script_path = mainconf.docroot + script_name;
		}

		params["SCRIPT_FILENAME"] = script_path;
	}

	struct stat sb;
	if (lstat(script_path.c_str(), &sb) == 0) {
		uid = sb.st_uid;
		gid = sb.st_gid;

		/*
		 * Make sure the path is under the docroot or the 
		 * user's userdir.
		 */
		std::string x = mainconf.docroot + '/';
		if (script_path.substr(0, x.size()) != x) {
			struct passwd *pwd = getpwuid(uid);
			if (pwd == NULL)
				throw creation_failure("script owner doesn't exist");
			x = std::string(pwd->pw_dir) + '/' + mainconf.userdir + '/';
			if (script_path.substr(0, x.size()) != x)
				throw creation_failure("script not under docroot or userdir");
		}
	} else
		throw creation_failure("script doesn't exist");

	std::ostringstream strm;
	strm << mainconf.sockdir << '/' << "php_" << uid << "_" << id_++;

	/*
	 * Look for a process in the freelist.
	 */
	int &mppu = mainconf.max_procs_per_user;
	int &mp = mainconf.max_procs;
	int &mqpu = mainconf.max_q_per_user;

	lock l(lock_);
	int &thisuser = peruser_[uid];
	int &thisq = peruser_waiting_[uid];

	if (mqpu > 0 && thisq > mqpu)
		throw creation_failure("too many queued processes for user");

	thisq++;

	while ( (mppu > 0 && thisuser >= mppu) ||
		(mp > 0 && curprocs_ > mp)) 
	{
		pthread_cond_wait(&curprocs_cond, &lock_);
	}

	peruser_[uid]++;
	curprocs_++;
	thisq--;

	freelist_t::index<by_uid>::type &idx = freelist_.get<by_uid>();
	freelist_t::index<by_uid>::type::iterator fit;
	if ((fit = idx.find(uid)) != idx.end()) {
		processp p = fit->proc;
		idx.erase(fit);
		return p;
	}

	return processp(new process(uid, gid, strm.str()));
}

void
process_factory::release_process(processp proc)
{
	free_process p;
	p.uid = proc->uid();
	p.released = std::time(0);
	p.proc = proc;

	lock lck(lock_);
	freelist_.push_back(p);
	curprocs_--;
	peruser_[p.uid]--;
	pthread_cond_broadcast(&curprocs_cond);
}
	
void
process_factory::destroy_process(processp proc)
{
	uid_t uid = proc->uid();

	lock lck(lock_);
	curprocs_--;
	peruser_[uid]--;
	pthread_cond_broadcast(&curprocs_cond);
}
