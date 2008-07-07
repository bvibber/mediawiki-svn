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

struct process_factory {
	process_factory(sbcontext& context);

	processp	create_from_filename(std::string const &filename);
	void		release(processp);
	void		handle_sigchld();

private:
	sbcontext &context_;

	freelist_t freelist_;
	boost::asio::deadline_timer reap_timer_;

	static std::set<int> ids_;
	static int curid_;
	void reap(const boost::system::error_code &error);

	log4cxx::LoggerPtr logger;
};

#endif
