/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet_poll: poll()-specific networking.
 */

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

struct pollfd *pfds;
int highest_fd;

void
wnet_init_select(void)
{
	int	 i;

	signal(SIGPIPE, SIG_IGN);
	pfds = malloc(sizeof(*pfds) * getdtablesize());
}

void
wnet_run(void)
{
	int		i, n = 0, pn ;

	for (;;) {
		for (i = pn = 0; i < highest_fd + 1; ++i) {
			if (fde_table[i].fde_flags.open) {
				pfds[pn].fd = fde_table[i].fde_fd;
				pfds[pn].events = fde_table[i].fde_epflags;
				++pn;
			}
		}

		if ((i = poll(pfds, pn, -1)) == -1)
			break;

		for (n = 0; n < pn; ++n) {
			struct fde *e = &fde_table[pfds[n].fd];

			e->fde_epflags &= ~pfds[n].revents;

			if ((pfds[n].revents & POLLRDNORM) && e->fde_read_handler) {
				int ret = e->fde_read_handler(e);
				if (ret == 0)
					wnet_register(e->fde_fd, FDE_READ, e->fde_read_handler, NULL);
			}

			if ((pfds[n].revents & POLLWRNORM) && e->fde_write_handler) {
				int ret = e->fde_write_handler(e);
				if (ret == 0)
					wnet_register(e->fde_fd, FDE_WRITE, e->fde_write_handler, NULL);
			}
		}
	}
	perror("poll");
}

void
wnet_register(fd, what, handler, data)
	fdcb handler;
	void *data;
{
struct	fde		*e = &fde_table[fd];
	int		 flags = e->fde_epflags, mod = flags;

	assert(fd < MAX_FD);

	if (handler == NULL) {
		e->fde_epflags = 0;
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

	if (data)
		e->fde_rdata = data;
}
