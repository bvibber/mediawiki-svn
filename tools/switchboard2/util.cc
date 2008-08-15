/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<sys/types.h>
#include	<sys/time.h>
#include	<unistd.h>
#include	<poll.h>

#include	<cerrno>
#include	<cstdlib>
#include	<cstring>

#include	"util.h"

ssize_t
timed_read(int fd, void *buf, std::size_t nbytes, int timeout)
{
	if (timeout > -1) {
		struct pollfd pfd;
		std::memset(&pfd, 0, sizeof(pfd));
		pfd.fd = fd;
		pfd.events = POLLIN;
		int i = poll(&pfd, 1, timeout * 1000);
		if (i == -1)
			return -1;
		if (i == 0 || !(pfd.revents & POLLIN)) {
			errno = ETIMEDOUT;
			return -1;
		}
	}

	return read(fd, buf, nbytes);
}

ssize_t
timed_write(int fd, void const *buf, std::size_t nbytes, int timeout)
{
	if (timeout > -1) {
		struct pollfd pfd;
		std::memset(&pfd, 0, sizeof(pfd));
		pfd.fd = fd;
		pfd.events = POLLOUT;
		int i = poll(&pfd, 1, timeout * 1000);
		if (i == -1)
			return -1;
		if (i == 0 || !(pfd.revents & POLLOUT)) {
			errno = ETIMEDOUT;
			return -1;
		}
	}

	return write(fd, buf, nbytes);
}
