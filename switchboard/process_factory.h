/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef PROCESS_FACTORY_H
#define PROCESS_FACTORY_H

#include	<stdexcept>
#include	<map>
#include	<set>
#include	<ctime>
#include	<deque>

#include	<asio/deadline_timer.hpp>

#include	<boost/multi_index_container.hpp>
#include	<boost/multi_index/indexed_by.hpp>
#include	<boost/multi_index/member.hpp>
#include	<boost/multi_index/ordered_index.hpp>
#include	<boost/multi_index/tag.hpp>
#include	<boost/multi_index/sequenced_index.hpp>

#include	<log4cxx/logger.h>

#include	"process.h"

namespace mi = boost::multi_index;

struct sbcontext;

struct by_uid {};
struct by_pid {};
struct by_released {};

struct free_process {
	uid_t uid;
	pid_t pid;
	std::time_t released;
	processp proc;
};

/*
 * Someone waiting for a process.
 */
struct waiter {
	std::string filename;
	boost::function<void (processp)> func;
	uid_t uid;
};

typedef boost::multi_index_container<
	free_process,
	mi::indexed_by<
		mi::sequenced<>,
		mi::ordered_non_unique<mi::tag<by_uid>, 
			mi::member<free_process, uid_t, &free_process::uid> >,
		mi::ordered_non_unique<mi::tag<by_released>, 
			mi::member<free_process, std::time_t, &free_process::released> >,
		mi::ordered_non_unique<mi::tag<by_pid>, 
			mi::member<free_process, pid_t, &free_process::pid> >
	>
> freelist_t;

typedef boost::multi_index_container<
	waiter,
	mi::indexed_by<
		mi::sequenced<>,
		mi::ordered_non_unique<mi::tag<by_uid>,
			mi::member<waiter, uid_t, &waiter::uid> >
	>
> waiterlist_t;

/*
 * A handle to represent ownership of a process.  This doesn't represent an
 * actual process, rather the intent to own one.  That is, the holder of a
 * process_ref may obtain and release several processes, as long as only
 * one is outstanding at a time.
 */
struct process_ref {
	process_ref(sbcontext &);
	~process_ref();

	void lock();

	void uid(uid_t u) { uid_ = u; }
	uid_t uid() const { return uid_; }
	void gid(gid_t u) { gid_ = u; }
	gid_t gid() const { return gid_; }

private:
	sbcontext &context_;
	uid_t uid_;
	gid_t gid_;
};

struct process_factory {
	process_factory(sbcontext& context);

	void	create_from_filename(
			std::string const &filename,
			boost::function<void (processp)>);
	void	release(processp);
	void	handle_sigchld();

private:
	friend class process_ref;

	sbcontext &context_;

	freelist_t freelist_;
	asio::deadline_timer reap_timer_;

	static int curid_;
	static int nactive_;

	void reap(asio::error_code error);
	void process_released(process_ref const &ref);

	void _do_create_from_filename(
			std::string const &filename,
			boost::function<void (processp)>,
			uid_t uid, gid_t gid);

	waiterlist_t waiters_;
	std::map<uid_t, int> peruser_;

	log4cxx::LoggerPtr logger;
};

#endif
