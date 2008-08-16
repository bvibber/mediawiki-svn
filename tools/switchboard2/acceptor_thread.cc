/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<sys/types.h>
#include	<sys/socket.h>

#include	"acceptor_thread.h"
#include	"request_thread.h"

namespace {

extern "C" void *
start_acceptor(void *arg)
{
	acceptor_thread *thr = static_cast<acceptor_thread *>(arg);
	thr->start();
	return NULL;
}

} // anonymous namespace

acceptor_thread::acceptor_thread(int fd)
	: fd_(fd)
	, logger(log4cxx::Logger::getLogger("acceptor"))
{
}

acceptor_thread::~acceptor_thread()
{
	close(fd_);
}

void
acceptor_thread::run()
{
	pthread_t tid;
	pthread_create(&tid, NULL, start_acceptor, this);
}

void
acceptor_thread::start()
{
	int newfd;
	struct sockaddr addr;
	socklen_t addrlen = sizeof(addr);

	while ((newfd = accept(fd_, &addr, &addrlen)) != -1) {
		request_thread *req = new request_thread(newfd);
		req->start();
	}

	LOG4CXX_ERROR(logger, boost::format("accept failed: %s") % std::strerror(errno));
}
