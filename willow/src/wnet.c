/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet: Networking.
 */

#include <sys/types.h>
#include <sys/socket.h>

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <assert.h>
#include <fcntl.h>
#include <poll.h>
#include <port.h>

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "wlog.h"
#include "whttp.h"

static int wnet_accept(struct fde *);

struct fde fde_table[MAX_FD];
static int port;

void
wnet_init(void)
{
	int	 i;

	if ((port = port_create()) < 0) {
		perror("port_create");
		exit(8);
	}

	for (i = 0; i < nlisteners; ++i) {
		struct listener	*lns = listeners[i];

		int fd = wnet_open();
		int one = 1;
		setsockopt(fd, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));
		if (bind(fd, &lns->addr, sizeof(lns->addr)) < 0) {
			wlog(WLOG_ERROR, "bind: %s: %s\n", lns->name, strerror(errno));
			exit(8);
		}
		if (listen(fd, 10) < 0) {
			wlog(WLOG_ERROR, "listen: %s: %s\n", lns->name, strerror(errno));
			exit(8);
		}
		wnet_register(fd, FDE_READ, wnet_accept, NULL);
		wlog(WLOG_NOTICE, "listening on %s", lns->name);
	}
}

void
wnet_run(void)
{
	int		i;
	port_event_t	pe;

	wlog(WLOG_NOTICE, "running...");

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
struct	fde	*e = &fde_table[fd];
	int	 flags = 0;

	assert(fd < MAX_FD);

	memset(e, 0, sizeof(*e));
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
		e->fde_data = data;
	
	if (port_associate(port, PORT_SOURCE_FD, fd, flags, NULL) < 0) {
		perror("port_associate");
		exit(8);
	}
}

int
wnet_accept(e)
	struct fde *e;
{
struct	client_data	*cdata;
	socklen_t	 addrlen;
	int		 newfd;
struct	fde		*newe;

	if ((cdata = wmalloc(sizeof(struct client_data))) == NULL) {
		fputs("out of memory\n", stderr);
		abort();
	}

	memset(cdata, 0, sizeof(*cdata));

	addrlen = sizeof(cdata->cdat_addr);

	if ((newfd = accept(e->fde_fd, &cdata->cdat_addr, &addrlen)) < 0) {
		wlog(WLOG_NOTICE, "accept error: %s", strerror(errno));
		return 0;
	}

	if (newfd >= MAX_FD) {
		wlog(WLOG_NOTICE, "out of file descriptors!");
		wfree(cdata);
		close(newfd);
		return 0;
	}

	newe = &fde_table[newfd];
	memset(newe, 0, sizeof(struct fde));
	newe->fde_fd = newfd;
	newe->fde_cdata = cdata;

	http_new(newe);
	return 0;
}

int
wnet_open(void)
{
	int	fd, val;

	if ((fd = socket(PF_INET, SOCK_STREAM, IPPROTO_TCP)) < 0) {
		perror("socket");
		exit(8);
	}

	val = fcntl(fd, F_GETFL, 0);
	fcntl(fd, F_SETFL, val | O_NONBLOCK);

	memset(&fde_table[fd], 0, sizeof(fde_table[fd]));
	fde_table[fd].fde_fd = fd;

	return fd;
}

void
wnet_close(fd)
{
struct	fde	*e = &fde_table[fd];

	close(e->fde_fd);
	if (e->fde_cdata)
		wfree(e->fde_cdata);
}
