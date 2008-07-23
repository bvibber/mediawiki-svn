/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<cstring>
using std::strerror;	/* for asio */

#include	<sys/types.h>
#include	<sys/stat.h>
#include	<sys/socket.h>
#include	<sys/un.h>
#include	<sys/wait.h>

#include	<cassert>
#include	<ctime>

#include	<boost/date_time/posix_time/posix_time.hpp>
#include	<boost/bind.hpp>
#include	<boost/lambda/lambda.hpp>
#include	<boost/format.hpp>
using boost::format;

#include	"process_factory.h"
#include	"sbcontext.h"	
#include	"config.h"

int process_factory::curid_;
int process_factory::nactive_;

process_ref::process_ref(sbcontext &context)
	: context_(context)
	, uid_(0)
	, gid_(0)
{
}

void
process_ref::lock()
{
	context_.factory().nactive_++;
}

process_ref::~process_ref()
{
	context_.factory().process_released(*this);
}

process_factory::process_factory(sbcontext &context)
	: context_(context)
	, reap_timer_(context.service(), boost::posix_time::seconds(5))
	, logger(log4cxx::Logger::getLogger("switchboard.process_factory"))
{
	reap_timer_.async_wait(boost::bind(&process_factory::reap, this,
				asio::placeholders::error));
}

void
process_factory::process_released(process_ref const &ref)
{
	LOG4CXX_DEBUG(logger,
		format("process_released, nactive=%d uid=%d") % nactive_ % ref.uid());

	nactive_--;

	uid_t uid = ref.uid();
	gid_t gid = ref.gid();
	if (uid == 0)
		return;

	if (peruser_[uid] > 0)
		peruser_[uid]--;

	waiterlist_t::index<by_uid>::type &idx = waiters_.get<by_uid>();
	waiterlist_t::index<by_uid>::type::iterator
		it = idx.find(uid);

	if (it != idx.end()) {
		waiter n = *it;
		idx.erase(it);
		_do_create_from_filename(n.filename, n.func, uid, gid);
		return;
	}

	if (!waiters_.empty()) {
		waiter n = waiters_.front();
		waiters_.pop_front();
		_do_create_from_filename(n.filename, n.func, n.uid, n.gid);
	}
}

void
process_factory::create_from_filename(
	std::string const &filename,
	boost::function<void (processp)> func)
{
	struct stat sb;

	if (lstat(filename.c_str(), &sb) == -1)
		throw creation_failure("cannot access pathname");

	if (mainconf.max_procs > 0 && nactive_ > mainconf.max_procs) {
		waiter n;
		n.filename = filename;
		n.func = func;
		n.uid = sb.st_uid;
		n.gid = sb.st_gid;
		waiters_.push_back(n);
		return;
	}

	freelist_t::index<by_uid>::type &idx = freelist_.get<by_uid>();
	if (mainconf.max_procs_per_user > 0 &&
	    peruser_[sb.st_uid] >= mainconf.max_procs_per_user) {
		waiter n;
		n.filename = filename;
		n.func = func;
		n.uid = sb.st_uid;
		n.gid = sb.st_gid;
		waiters_.push_back(n);
		return;
	}

	_do_create_from_filename(filename, func, sb.st_uid, sb.st_gid);
}

void
process_factory::_do_create_from_filename(
	std::string const &filename,
	boost::function<void (processp)> func,
	uid_t uid, gid_t gid)
{
	freelist_t::index<by_uid>::type &idx = freelist_.get<by_uid>();
	freelist_t::index<by_uid>::type::iterator
		it = idx.find(uid);

	peruser_[uid]++;

	if (it != idx.end()) {
		assert(it->uid == uid);
		processp proc(it->proc);
		idx.erase(it);
		context_.service().post(boost::bind(func, proc));
		return;
	}

	std::stringstream bindpath;
	int id = curid_++;
	
	bindpath << (mainconf.sockdir + "/switchboard_") << uid << "_" << id;
	processp newproc(new process(context_, uid, gid, bindpath.str()));
	context_.service().post(boost::bind(func, newproc));
}

void
process_factory::release(processp proc)
{
	free_process fp;
	fp.released = std::time(0);
	fp.proc = proc;
	fp.uid = proc->uid();

	freelist_.push_back(fp);
}

void
process_factory::reap(asio::error_code error)
{
	std::time_t oldest = std::time(0) - 30;

	freelist_t::index<by_released>::type &idx = freelist_.get<by_released>();
	std::pair<
		freelist_t::index<by_released>::type::iterator,
		freelist_t::index<by_released>::type::iterator>
		range = idx.range(mi::unbounded, boost::lambda::_1 <= oldest);

	idx.erase(range.first, range.second);

	reap_timer_.expires_from_now(boost::posix_time::seconds(5));
	reap_timer_.async_wait(boost::bind(&process_factory::reap, this,
				asio::placeholders::error));

	/*
	 * We don't really care about the exit status of children, just
	 * wait for them to prevent zombies.
	 */
	waitpid(-1, NULL, WNOHANG);
}

void
process_factory::handle_sigchld()
{
	pid_t pid;
	int status;
	while ((pid = waitpid(-1, &status, WNOHANG)) != (pid_t) -1) {
		LOG4CXX_DEBUG(logger, format("child %d died") % pid);
		freelist_t::index<by_pid>::type &idx = freelist_.get<by_pid>();
		freelist_t::index<by_pid>::type::iterator it;
		if ((it = idx.find(pid)) == idx.end())
			continue;
		idx.erase(it);
	}
}
