/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet_poll: poll()-specific networking.
 */

#ifdef __SUNPRO_C
# pragma ident "@(#)$Header$"
#endif

#include <sys/types.h>
#include <sys/socket.h>

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

/* May not have X/Open macros */

#ifndef POLLRDNORM
# define POLLRDNORM POLLIN
#endif

#ifndef POLLWRNORM
# define POLLWRNORM POLLOUT
#endif

static struct pollfd *pfds;
int highest_fd;

void
wnet_init_select(void)
{
	int	 i;

	pfds = wmalloc(sizeof(*pfds) * getdtablesize());
	bzero(pfds, sizeof(*pfds) * getdtablesize());
}

void
wnet_run(void)
{
	int		n = 0, i;

	for (;;) {
		if ((i = poll(pfds, highest_fd + 1, -1)) == -1)
			break;
		wnet_set_time();

		for (n = 0; n < highest_fd + 1; ++n) {
			struct fde *e = &fde_table[pfds[n].fd];

			if ((pfds[n].revents & POLLRDNORM) && e->fde_read_handler) {
				e->fde_read_handler(e);
			}

			if ((pfds[n].revents & POLLWRNORM) && e->fde_write_handler) {
				e->fde_write_handler(e);
			}
		}
	}
	perror("poll");
}

void
wnet_register(fd, what, handler, data)
	int fd, what;
	fdcb handler;
	void *data;
{
struct	fde		*e = &fde_table[fd];
	int		 flags = e->fde_epflags;

	assert(fd < max_fd);
	
	if (handler == NULL) {
		e->fde_epflags = 0;
		pfds[fd].fd = -1;
		pfds[fd].events = 0;
		return;
	}

	e->fde_fd = fd;
	if (what & FDE_READ) {
		e->fde_read_handler = handler;
		e->fde_epflags |= POLLRDNORM;
	} 
	if (what & FDE_WRITE) {
		e->fde_write_handler = handler;
		e->fde_epflags |= POLLWRNORM;
	}

	pfds[fd].fd = fd;
	pfds[fd].events = e->fde_epflags;
	
	if (data)
		e->fde_rdata = data;
}
