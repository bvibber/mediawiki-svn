/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet_ports: Solaris event ports-specific networking
 */

#ifdef __SUNPRO_C
# pragma ident "@(#)$Header$"
#endif

#include <sys/types.h>
#include <sys/socket.h>

#include <stdio.h>
#include <stdlib.h>
#include <assert.h>
#include <fcntl.h>
#include <port.h>
#include <errno.h>
#include <string.h>

#include "wnet.h"
#include "wlog.h"

#define READABLE POLLRDNORM

static int port;
#define GETN 256
static port_event_t pe[GETN];

void
wnet_init_select(void)
{
	if ((port = port_create()) < 0) {
		perror("port_create");
		exit(8);
	}
}

void
wnet_run(void)
{
	int		i;
	uint		nget = 1;

	for (;;) {
		i = port_getn(port, pe, GETN, &nget, NULL);
		
		if (i == -1) {
			if (errno == EINTR) {
				if (wnet_exit)
					return;
				continue;
			}
			break;
		}
		
		wnet_set_time();

		for (i = 0; i < nget; ++i) {
			struct fde *e = &fde_table[pe[i].portev_object];
			int hadwrite, hadread;
			assert(pe[i].portev_object < max_fd);

			hadread = e->fde_epflags & READABLE;
			hadwrite = e->fde_epflags & POLLWRNORM;

			WDEBUG((WLOG_DEBUG, "activity on fd %d [%s]", e->fde_fd, e->fde_desc));

			/*
			 * Immediately re-associate.  If the caller doesn't want it,
			 * they'll dissociate it themselves.  This could be optimised
			 * a little to save 2 syscalls in some cases...
			 */
			if ((pe[i].portev_events & READABLE) && e->fde_read_handler) {
				WDEBUG((WLOG_DEBUG, "\tread", e->fde_fd));
				e->fde_read_handler(e);
				if (hadread && (e->fde_epflags & READABLE))
					if (port_associate(port, PORT_SOURCE_FD, e->fde_fd, READABLE, NULL) == -1) {
						wlog(WLOG_ERROR, "port_associate: %s", strerror(errno));
						exit(8);
					}
			}
			if ((pe[i].portev_events & (POLLWRNORM | POLLERR)) && e->fde_write_handler) {
				WDEBUG((WLOG_DEBUG, "\twrite", e->fde_fd));
				e->fde_write_handler(e);
				if (hadwrite && (e->fde_epflags & POLLWRNORM))
					if (port_associate(port, PORT_SOURCE_FD, e->fde_fd, POLLWRNORM, NULL) == -1) {
						wlog(WLOG_ERROR, "port_associate: %s", strerror(errno));
						exit(8);
					}
			}
		}
		nget = 1;
	}
}

void
wnet_register(fd, what, handler, data)
	int fd, what;
	fdcb handler;
	void *data;
{
struct	fde		*e = &fde_table[fd];
	int		 oldflags = e->fde_epflags;

	WDEBUG((WLOG_DEBUG, "wnet_register: %d [%s] for %d %p", fd, e->fde_desc, what, handler));

	e->fde_fd = fd;

	if (what & FDE_READ) {
		e->fde_read_handler = handler;
		if (handler)
			e->fde_epflags |= READABLE;
		else
			e->fde_epflags &= ~READABLE;
	} 
	if (what & FDE_WRITE) {
		e->fde_write_handler = handler;
		if (handler)
			e->fde_epflags |= POLLWRNORM;
		else
			e->fde_epflags &= ~POLLWRNORM;
	}

	if (data)
		e->fde_rdata = data;
	
	if (oldflags == e->fde_epflags)
		/* no change */
		return;

	if (e->fde_epflags) {
		if (port_associate(port, PORT_SOURCE_FD, fd, e->fde_epflags, NULL) < 0) {
			perror("port_associate");
			abort();
		}
	} else {
		(void)port_dissociate(port, PORT_SOURCE_FD, fd);
	}
}
