/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef ACCEPTOR_THREAD_H
#define ACCEPTOR_THREAD_H

#include	<log4cxx/logger.h>

struct acceptor_thread {
	acceptor_thread(int);
	~acceptor_thread();

	void start();
	void run();

private:
	int fd_;
	log4cxx::LoggerPtr logger;
};

#endif	/* !ACCEPTOR_THREAD_H */
