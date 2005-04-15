/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet_ports: Solaris event ports-specific networking
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
#include <ports.h>
#include <poll.h>

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "wlog.h"
#include "whttp.h"

static int port;

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
	int		i, n;
	port_event_t	pe;

	while ((i = port_get(port, &pe, NULL)) != -1) {
		struct fde *e = &fde_table[pe.portev_object];
		assert(pe.portev_object < MAX_FD);

		if ((pe.portev_events & POLLRDNORM) && e->fde_read_handler) {
			int ret = e->fde_read_handler(e);
			if (ret == 0)
				port_associate(port, PORT_SOURCE_FD, e->fde_fd, POLLRDNORM, NULL);
		}
		if ((pe.portev_events & POLLWRNORM) && e->fde_write_handler) {
			int ret = e->fde_write_handler(e);
			if (ret == 0)
				port_associate(port, PORT_SOURCE_FD, e->fde_fd, POLLWRNORM, NULL);
		}
	}
	perror("port_get");
}

void
wnet_register(fd, what, handler, data)
	fdcb handler;
	void *data;
{
struct	fde		*e = &fde_table[fd];
	int		 flags = 0;

	assert(fd < MAX_FD);

	e->fde_fd = fd;
	if (what & FDE_READ) {
		e->fde_read_handler = handler;
		flags |= POLLRDNORM;
	} 
	if (what & FDE_WRITE) {
		e->fde_write_handler = handler;
		flags |= POLLWRNORM;
	}

	if (data)
		e->fde_rdata = data;
	
	if (port_associate(port, PORT_SOURCE_FD, fd, flags, NULL) < 0) {
		perror("port_associate");
		exit(8);
	}
}
