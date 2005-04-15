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

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "wlog.h"
#include "whttp.h"

struct wrtbuf {
	void	*wb_buf;
	int	 wb_size;
	int	 wb_done;
	fdwcb	 wb_func;
	void	*wb_udata;
};

static int wnet_accept(struct fde *);
static int wnet_write_do(struct fde *);

struct fde fde_table[MAX_FD];

#if defined(USE_PORTS) || defined(USE_EPOLL)
static int port;
#endif

void
wnet_init(void)
{
	int	 i;

	signal(SIGPIPE, SIG_IGN);

#if defined(USE_PORTS)
	if ((port = port_create()) < 0) {
		perror("port_create");
		exit(8);
	}
#elif defined(USE_EPOLL)
	if ((port = epoll_create(MAX_FD)) < 0) {
		perror("epoll_create");
		exit(8);
	}
#endif

	for (i = 0; i < nlisteners; ++i) {
		struct listener	*lns = listeners[i];

		int fd = wnet_open("listener");
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
	int		i, n;
#if defined(USE_PORTS)
	port_event_t	pe;
#elif defined(USE_EPOLL)
struct	epoll_event	events[256];
#endif

	wlog(WLOG_NOTICE, "running...");

#if defined(USE_PORTS)
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
#elif defined(USE_EPOLL)
	while ((i = epoll_wait(port, events, 256, -1)) != -1) {
		for (n = 0; n < i; ++n) {
			struct fde *e = &fde_table[events[n].data.fd];
			struct epoll_event ev;
			assert(events[n].data.fd < MAX_FD);

			e->fde_epflags &= ~events[n].events;
			ev.events = e->fde_epflags;
			ev.data.fd = e->fde_fd;
			if (e->fde_epflags == 0) {
				if (epoll_ctl(port, EPOLL_CTL_DEL, e->fde_fd, NULL) < 0) {
					perror("epoll_ctl(DEL)");
					exit(8);
				}
			} else {
				if (epoll_ctl(port, EPOLL_CTL_MOD, e->fde_fd, &ev) < 0) {
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
#endif
}

void
wnet_register(fd, what, handler, data)
	fdcb handler;
	void *data;
{
struct	fde		*e = &fde_table[fd];
#if defined(USE_EPOLL)
	int		 flags = e->fde_epflags, mod = flags;
struct	epoll_event	 ev;
#else
	int		 flags = 0;
#endif

	assert(fd < MAX_FD);

	e->fde_fd = fd;
	if (what & FDE_READ) {
		e->fde_read_handler = handler;
#if defined(USE_PORTS)
		flags |= POLLRDNORM;
#elif defined(USE_EPOLL)
		e->fde_epflags |= EPOLLIN;
#endif
	} 
	if (what & FDE_WRITE) {
		e->fde_write_handler = handler;
#if defined(USE_PORTS)
		flags |= POLLWRNORM;
#elif defined(USE_EPOLL)
		e->fde_epflags |= EPOLLOUT;
#endif
	}

	if (data)
		e->fde_rdata = data;
	
#if defined(USE_PORTS)
	if (port_associate(port, PORT_SOURCE_FD, fd, flags, NULL) < 0) {
		perror("port_associate");
		exit(8);
	}
#elif defined(USE_EPOLL)
	memset(&ev, 0, sizeof(ev));
	ev.events = e->fde_epflags;
	ev.data.fd = fd;
	if (mod) {
		if (epoll_ctl(port, EPOLL_CTL_MOD, fd, &ev) < 0) {
			perror("epoll_ctl");
			exit(8);
		} 
	} else {
		if (epoll_ctl(port, EPOLL_CTL_ADD, fd, &ev) < 0) {
			perror("epoll_ctl");
			exit(8);
		}
	}
#endif
}

int
wnet_accept(e)
	struct fde *e;
{
struct	client_data	*cdata;
	socklen_t	 addrlen;
	int		 newfd, val;
struct	fde		*newe;

	if ((cdata = wmalloc(sizeof(*cdata))) == NULL) {
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

	val = fcntl(newfd, F_GETFL, 0);
	fcntl(newfd, F_SETFL, val | O_NONBLOCK);

	newe = &fde_table[newfd];
	memset(newe, 0, sizeof(struct fde));
	newe->fde_fd = newfd;
	newe->fde_cdata = cdata;
	newe->fde_desc = "accept()ed fd";
	inet_ntop(AF_INET, &cdata->cdat_addr.sin_addr.s_addr, newe->fde_straddr, sizeof(newe->fde_straddr));

	http_new(newe);
	return 0;
}

int
wnet_open(desc)
	const char *desc;
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
	fde_table[fd].fde_desc = desc;

	return fd;
}

void
wnet_close(fd)
{
struct	fde	*e = &fde_table[fd];

#if defined(USE_PORTS)
	port_dissociate(port, PORT_SOURCE_FD, e->fde_fd);
#endif
	close(e->fde_fd);
	if (e->fde_cdata)
		wfree(e->fde_cdata);
}

void
wnet_write(fd, buf, bufsz, cb, data)
	void *buf;
	size_t bufsz;
	fdwcb cb;
	void *data;
{
struct	wrtbuf	*wb;
struct	fde	*e = &fde_table[fd];

	if ((wb = wmalloc(sizeof(*wb))) == NULL) {
		fputs("out of memory\n", stderr);
		abort();
	}

	wb->wb_buf = buf;
	wb->wb_size = bufsz;
	wb->wb_done = 0;
	wb->wb_func = cb;
	wb->wb_udata = data;

	e->fde_wdata = wb;
	wnet_register(fd, FDE_WRITE, wnet_write_do, NULL);
}

static int
wnet_write_do(e)
	struct fde *e;
{
struct	wrtbuf	*buf;
	int	 i;

	buf = e->fde_wdata;

	while ((i = write(e->fde_fd, buf->wb_buf + buf->wb_done, buf->wb_size - buf->wb_done)) > -1) {
		buf->wb_done += i;
		if (buf->wb_done == buf->wb_size) {
			buf->wb_func(e, buf->wb_udata, 0);
			return 1;
		}

	}

	if (errno == EWOULDBLOCK) 
		return 0;
			
	buf->wb_func(e, buf->wb_udata, -1);
	return 1;
}
