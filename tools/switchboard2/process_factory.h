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

#include	<map>
#include	<string>
#include	<ctime>

#include	<pthread.h>

#include	<boost/noncopyable.hpp>
#include	<boost/multi_index_container.hpp>
#include	<boost/multi_index/indexed_by.hpp>
#include	<boost/multi_index/member.hpp>
#include	<boost/multi_index/ordered_index.hpp>
#include	<boost/multi_index/tag.hpp>
#include	<boost/multi_index/sequenced_index.hpp>

#include	"process.h"

namespace mi = boost::multi_index;

struct free_process {
	uid_t uid;
	std::time_t released;
	processp proc;
};

struct by_uid {};
struct by_released {};

typedef boost::multi_index_container<
	free_process,
	mi::indexed_by<
		mi::sequenced<>,
		mi::ordered_non_unique<mi::tag<by_uid>, 
			mi::member<free_process, uid_t, &free_process::uid> >,
		mi::ordered_non_unique<mi::tag<by_released>,
			mi::member<free_process, std::time_t, &free_process::released> >
	>
> freelist_t;

struct process_factory : boost::noncopyable {
	static process_factory &instance();

	process_factory();
	~process_factory();

	processp get_process(std::map<std::string, std::string> &params);
	void release_process(processp proc);
	void destroy_process(processp proc);

	void cleanup_thread();

private:
	int id_;
	freelist_t freelist_;
	pthread_mutex_t lock_;

	std::map<uid_t, int> peruser_;
	std::map<uid_t, int> peruser_waiting_;
	int curprocs_;

	pthread_cond_t curprocs_cond;
};

struct process_releaser : boost::noncopyable {
	process_releaser(processp const &proc)
		: proc_(proc) {}

	void release() {
		if (proc_)
			process_factory::instance().release_process(proc_);
		proc_.reset();
	}

	~process_releaser() {
		if (proc_)
			process_factory::instance().destroy_process(proc_);
	}

private:
	processp proc_;
};

#endif	/* !PROCESS_FACTORY_H */
