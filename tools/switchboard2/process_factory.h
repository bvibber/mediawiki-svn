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
	processp proc;
};

struct by_uid {};
typedef boost::multi_index_container<
	free_process,
	mi::indexed_by<
		mi::sequenced<>,
		mi::ordered_non_unique<mi::tag<by_uid>, 
			mi::member<free_process, uid_t, &free_process::uid> >
	>
> freelist_t;

struct process_factory : boost::noncopyable {
	static process_factory &instance();

	process_factory();
	~process_factory();

	processp get_process(std::map<std::string, std::string> &params);
	void release_process(processp proc);

private:
	int id_;
	freelist_t freelist_;
	pthread_mutex_t lock_;

	std::map<uid_t, int> peruser_;
	int curprocs_;

	//pthread_mutex_t curprocs_mtx;
	pthread_cond_t curprocs_cond;
};

#endif	/* !PROCESS_FACTORY_H */
