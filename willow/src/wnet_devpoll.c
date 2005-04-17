/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet_ports: Solaris /dev/poll-specific networking
 */

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/devpoll.h>

#include <arpa/inet.h>

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <assert.h>
#include <fcntl.h>
#include <signal.h>
#include <poll.h>

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "wlog.h"
#include "whttp.h"

static int polldev;
struct dvpoll dvp;
#define GETN 64
struct pollfd pollfds[GETN];

void
wnet_init_select(void)
{
	if ((polldev = open("/dev/poll", O_RDWR)) < 0) {
		perror("/dev/poll");
		exit(8);
	}
}

void
wnet_run(void)
{
	int		i, n;

	for (;;) {
		dvp.dp_fds = pollfds;
		dvp.dp_nfds = 1;
		dvp.dp_timeout = -1;

		if ((n = ioctl(polldev, DP_POLL, &dvp)) < 0)
			break;
		
		for (i = 0; i < n; ++i) {
			struct fde *e = &fde_table[pollfds[i].fd];
			assert(pollfds[i].fd < MAX_FD);

			if ((pollfds[i].revents & POLLRDNORM) && e->fde_read_handler) {
				e->fde_read_handler(e);
			}
			if ((pollfds[i].revents & POLLWRNORM) && e->fde_write_handler) {
				e->fde_write_handler(e);
			}
		}
	}
	perror("/dev/poll");
}

void
wnet_register(fd, what, handler, data)
	fdcb handler;
	void *data;
{
struct	fde		*e = &fde_table[fd];
struct	pollfd		 pfd;

	assert(fd < MAX_FD);

	memset(&pfd, 0, sizeof(pfd));
	pfd.fd = fd;
	/*
	 * Always remove it first, or we just make a no-op when trying to remove flags.
	 */
	pfd.events = POLLREMOVE;
	if (write(polldev, &pfd, sizeof(pfd)) < 0) {
		perror("/dev/poll");
		exit(8);
	}

	pfd.events = e->fde_epflags;

	e->fde_fd = fd;
	if (handler == NULL) {
		if (what & FDE_READ)
			e->fde_epflags &= ~POLLRDNORM;
		if (what & FDE_WRITE)
			e->fde_epflags &= ~POLLWRNORM;
	} else {
		if (what & FDE_READ) {
			e->fde_read_handler = handler;
			e->fde_epflags |= POLLRDNORM;
		} 
		if (what & FDE_WRITE) {
			e->fde_write_handler = handler;
			e->fde_epflags |= POLLWRNORM;
		}
	}

	if (!e->fde_epflags)
		return;

	pfd.events = e->fde_epflags;

	if (data)
		e->fde_rdata = data;
	
	if (write(polldev, &pfd, sizeof(pfd)) < 0) {
		perror("write(/dev/poll)");
		exit(8);
	}
}
