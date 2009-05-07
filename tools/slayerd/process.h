/* Copyright (c) 2007-2009 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#ifndef PROCESS_H
#define PROCESS_H

#include	<vector>

struct process {
	typedef boost::shared_ptr<process> pointer;

	virtual ~process() {};

	virtual pid_t pid() const = 0;
	virtual std::string command() const = 0;
	virtual std::string cmdline() const = 0;
	virtual std::size_t rss() const = 0;
	virtual std::size_t vsize() const = 0;
	virtual uid_t uid() const = 0;
};

std::vector<process::pointer> enumerate_processes();

#endif	/* !PROCESS_H */
