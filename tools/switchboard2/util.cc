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

#include	<cerrno>
#include	<cstdlib>
#include	<cstring>

#include	"util.h"

ssize_t
timed_read(int fd, void *buf, std::size_t nbytes, int timeout)
{
	using std::memset;
	if (timeout > -1) {
		struct timeval tv;
		tv.tv_sec = timeout;
		tv.tv_usec = 0;
		fd_set set;
		FD_ZERO(&set);
		FD_SET(fd, &set);
		int i = select(fd + 1, &set, NULL, NULL, &tv);
		if (i == -1)
			return -1;
		if (i == 0) {
			errno = ETIMEDOUT;
			return -1;
		}
	}

	return read(fd, buf, nbytes);
}

ssize_t
timed_write(int fd, void const *buf, std::size_t nbytes, int timeout)
{
	using std::memset;
	if (timeout > -1) {
		struct timeval tv;
		tv.tv_sec = timeout;
		tv.tv_usec = 0;
		fd_set set;
		FD_ZERO(&set);
		FD_SET(fd, &set);
		int i = select(fd + 1, NULL, &set, NULL, &tv);
		if (i == -1)
			return -1;
		if (i == 0) {
			errno = ETIMEDOUT;
			return -1;
		}
	}

	return write(fd, buf, nbytes);
}
