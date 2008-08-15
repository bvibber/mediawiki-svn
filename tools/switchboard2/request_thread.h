/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef REQUEST_THREAD_H
#define REQUEST_THREAD_H

#include	<stdexcept>

#include	<pthread.h>

#include	"fcgi.h"
#include	"process.h"

struct request_exception : std::runtime_error {
	request_exception(char const *what)
		: std::runtime_error(what) {}
};

struct request_thread {
	request_thread(int fd);
	~request_thread();

	void start();
	void start_request();

private:
	void handle_normal_request(fcgi::record &);
	void handle_get_values(fcgi::record &);

	int fd_;
	int cfd_;
	int rid_;
	pthread_t tid_;

	processp process_;
};

#endif	/* !REQUEST_THREAD_H */
