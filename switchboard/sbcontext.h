/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef SBCONTEXT_H
#define SBCONTEXT_H

#include	<asio.hpp>

struct process_factory;

struct sbcontext : boost::noncopyable {
	sbcontext();

	asio::io_service &service() {
		return service_;
	}

	process_factory &factory();

private:
	asio::io_service	service_;
	process_factory 	*factory_;
};

#endif
