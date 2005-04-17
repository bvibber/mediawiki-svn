/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet: Networking.
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
#include <sys/epoll.h>

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "wlog.h"
#include "whttp.h"

static int epfd;

void
wnet_init_select(void)
{
	int	 i;

	signal(SIGPIPE, SIG_IGN);

	if ((epfd = epoll_create(MAX_FD)) < 0) {
		perror("epoll_create");
		exit(8);
	}
}

void
wnet_run(void)
{
	int		i, n;
struct	epoll_event	events[256];

	while ((i = epoll_wait(epfd, events, 256, -1)) != -1) {
		wnet_set_time();

		for (n = 0; n < i; ++n) {
			struct fde *e = &fde_table[events[n].data.fd];
			struct epoll_event ev;
			assert(events[n].data.fd < MAX_FD);

			e->fde_epflags &= ~events[n].events;
			ev.events = e->fde_epflags;
			ev.data.fd = e->fde_fd;
			if (e->fde_epflags == 0) {
				if (epoll_ctl(epfd, EPOLL_CTL_DEL, e->fde_fd, NULL) < 0) {
					perror("epoll_ctl(DEL)");
					exit(8);
				}
			} else {
				if (epoll_ctl(epfd, EPOLL_CTL_MOD, e->fde_fd, &ev) < 0) {
					perror("epoll_ctl(MOD)");
					exit(8);
				}
			}

			if ((events[n].events & EPOLLIN) && e->fde_read_handler) {
				int ret = e->fde_read_handler(e);
				if (ret == 0)
					wnet_register(e->fde_fd, FDE_READ, e->fde_read_handler, NULL);
			}

			if ((events[n].events & EPOLLOUT) && e->fde_write_handler) {
				int ret = e->fde_write_handler(e);
				if (ret == 0)
					wnet_register(e->fde_fd, FDE_WRITE, e->fde_write_handler, NULL);
			}
		}
	}
	perror("epoll_wait");
}

void
wnet_register(fd, what, handler, data)
	fdcb handler;
	void *data;
{
struct	fde		*e = &fde_table[fd];
	int		 flags = e->fde_epflags, mod = flags;
struct	epoll_event	 ev;

	assert(fd < MAX_FD);

	if (handler == NULL) {
		epoll_ctl(epfd, EPOLL_CTL_DEL, fd, NULL);
		return;
	}

	e->fde_fd = fd;
	if (what & FDE_READ) {
		e->fde_read_handler = handler;
		e->fde_epflags |= EPOLLIN;
	} 
	if (what & FDE_WRITE) {
		e->fde_write_handler = handler;
		e->fde_epflags |= EPOLLOUT;
	}

	if (data)
		e->fde_rdata = data;

	memset(&ev, 0, sizeof(ev));
	ev.events = e->fde_epflags;
	ev.data.fd = fd;
	if (mod) {
		if (epoll_ctl(epfd, EPOLL_CTL_MOD, fd, &ev) < 0) {
			perror("epoll_ctl");
			exit(8);
		} 
	} else {
		if (epoll_ctl(epfd, EPOLL_CTL_ADD, fd, &ev) < 0) {
			perror("epoll_ctl");
			exit(8);
		}
	}
}
