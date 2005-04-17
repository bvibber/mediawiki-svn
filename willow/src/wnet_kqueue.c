/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet_kqueue: FreeBSD kqueue-specific networking.
 */

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/event.h>
#include <sys/time.h>

#include <arpa/inet.h>

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <assert.h>
#include <fcntl.h>
#include <signal.h>

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "wlog.h"
#include "whttp.h"

static void kq_update_event(int, int, u_int);
static void kq_update_immediate(int, int, u_int);

static int kq;
struct timespec zero_timespec;
#define GETN 256
static struct kevent kqlst[GETN], kqchg[GETN];
static int kqoff;

void
wnet_init_select(void)
{
	if ((kq = kqueue()) < 0) {
		perror("kqueue");
		exit(8);
	}
}

void
wnet_run(void)
{
	int		i;
	int		nget;

	while ((nget = kevent(kq, kqchg, kqoff, kqlst, GETN, NULL)) > -1) {
		wnet_set_time();

		kqoff = 0;
		for (i = 0; i < nget; ++i) {
			struct fde *e = &fde_table[kqlst[i].ident];
			assert(kqlst[i].ident < MAX_FD);
			
			if (kqlst[i].flags & EV_ERROR) {
				fprintf(stderr, "error for %d (%s): %s\n", 
						kqlst[i].ident, e->fde_desc, strerror(kqlst[i].data));
				exit(8);
			}

			if ((kqlst[i].filter == EVFILT_READ) && e->fde_read_handler) {
				int ret = e->fde_read_handler(e);
				if (ret == 0)
					wnet_register(kqlst[i].ident, FDE_READ, e->fde_read_handler, NULL);
			}
			if ((kqlst[i].filter == EVFILT_WRITE) && e->fde_write_handler) {
				int ret = e->fde_write_handler(e);
				if (ret == 0)
					wnet_register(kqlst[i].ident, FDE_WRITE, e->fde_write_handler, NULL);
			}
		}
	}
	perror("wnet_run/kevent");
}

void
wnet_register(fd, what, handler, data)
	fdcb handler;
	void *data;
{
struct	fde		*e = &fde_table[fd];
	u_int		 flags;

	assert(fd < MAX_FD);

	if (handler == NULL) {
		kq_update_immediate(fd, EVFILT_READ, EV_DELETE);
		kq_update_immediate(fd, EVFILT_WRITE, EV_DELETE);
		return;
	} 

	flags = EV_ADD | EV_ONESHOT;

	e->fde_fd = fd;
	if (what & FDE_READ) {
		e->fde_read_handler = handler;
		kq_update_event(fd, EVFILT_READ, flags);
	} 
	if (what & FDE_WRITE) {
		e->fde_write_handler = handler;
		kq_update_event(fd, EVFILT_WRITE, flags);
	}

	if (data)
		e->fde_rdata = data;
}

static void
kq_update_event(fd, filter, flags)
	u_int flags;
{
	EV_SET(&kqchg[kqoff], fd, filter, flags, 0, 0, NULL);
	if (++kqoff == GETN) {
		if (kevent(kq, kqchg, kqoff, NULL, 0, &zero_timespec) < 0) {
			perror("kevent");
			exit(8);
		}
		kqoff = 0;
	}
}

static void
kq_update_immediate(fd, filter, flags)
	u_int flags;
{
struct	kevent	ke;

	EV_SET(&ke, fd, filter, flags, 0, 0, NULL);
	kevent(kq, &ke, 1, NULL, 0, &zero_timespec);
}


