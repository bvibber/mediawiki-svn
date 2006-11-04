/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet: Networking.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#define _XOPEN_SOURCE 600
#define _XOPEN_SOURCE_EXTENDED
#define __EXTENSIONS__
#ifndef _GNU_SOURCE
# define _GNU_SOURCE
#endif

#include <sys/types.h>
#include <sys/socket.h>
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
#include <event.h>

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "wlog.h"
#include "whttp.h"

struct event ev_sigint;
struct event ev_sigterm;
tss<event_base> evb;

static void fde_ev_callback(int, short, void *);

static void
sig_exit(int, short, void *);

void
make_event_base(void)
{
	if (evb == NULL) {
		evb = (event_base *)event_init();
		event_base_priority_init(evb, prio_max);
		signal_set(&ev_sigint, SIGINT, sig_exit, NULL);
		signal_add(&ev_sigint, NULL);
		signal_set(&ev_sigterm, SIGTERM, sig_exit, NULL);
		signal_add(&ev_sigterm, NULL);
	}
}

void
sig_exit(int sig, short what, void *d)
{
	exit(0);
}

void
wnet_init_select(void)
{
	signal(SIGPIPE, SIG_IGN);
	make_event_base();
}

void
wnet_run(void)
{
	make_event_base();
	event_base_loop(evb, 0);
	perror("event_base_loop");
}

static void
fde_ev_callback(int fd, short ev, void *d)
{
struct	fde	*fde = &fde_table[fd];

	assert(fde->fde_flags.open);

	if (ev & EV_READ)
		fde->fde_read_handler(fde);
	if (ev & EV_WRITE)
		fde->fde_write_handler(fde);
	if (!fde->fde_flags.read_held || !fde->fde_flags.write_held)
		event_add(&fde->fde_ev, NULL);
}

void
ioloop_t::clear_readback(int fd)
{
struct fde	*fde = &fde_table[fd];
	fde->fde_flags.read_held = 1;
}

void
ioloop_t::clear_writeback(int fd)
{
struct fde	*fde = &fde_table[fd];
	fde->fde_flags.write_held = 1;
}

void
ioloop_t::_register(int fd, int what, polycallback<fde *> handler)
{
struct	fde	*fde = &fde_table[fd];
	int	 ev_flags = 0;

	make_event_base();

	if (event_pending(&fde->fde_ev, EV_READ | EV_WRITE, NULL))
		event_del(&fde->fde_ev);

	assert(fde->fde_flags.open);

	if (what & FDE_READ) {
		fde->fde_read_handler = handler;
		fde->fde_flags.read_held = 0;
		ev_flags |= EV_READ;
	}
	if (what & FDE_WRITE) {
		ev_flags |= EV_WRITE;
		fde->fde_flags.write_held = 0;
		fde->fde_write_handler = handler;
	}

	//ev_flags |= EV_PERSIST;
	event_set(&fde->fde_ev, fde->fde_fd, ev_flags, fde_ev_callback, fde);
	event_base_set(evb, &fde->fde_ev);
	event_priority_set(&fde->fde_ev, (int) fde->fde_prio);
	event_add(&fde->fde_ev, NULL);
	fde->fde_flags.pend = 1;
}
