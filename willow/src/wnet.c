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

static void wnet_accept(struct fde *);
static void wnet_write_do(struct fde *);

struct fde fde_table[MAX_FD];

void
wnet_init(void)
{
	int	 i;

	signal(SIGPIPE, SIG_IGN);
	wnet_init_select();

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
		wfree(cdata);
		return;
	}

	if (newfd >= MAX_FD) {
		wlog(WLOG_NOTICE, "out of file descriptors!");
		wfree(cdata);
		close(newfd);
		return;
	}

	val = fcntl(newfd, F_GETFL, 0);
	fcntl(newfd, F_SETFL, val | O_NONBLOCK);

	newe = &fde_table[newfd];
	memset(newe, 0, sizeof(struct fde));
	newe->fde_flags.open = 1;
#ifdef USE_POLL
	if (newfd > highest_fd)
		highest_fd = newfd;
#endif
	newe->fde_fd = newfd;
	newe->fde_cdata = cdata;
	newe->fde_desc = "accept()ed fd";
	inet_ntop(AF_INET, &cdata->cdat_addr.sin_addr.s_addr, newe->fde_straddr, sizeof(newe->fde_straddr));

	http_new(newe);
	return;
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
	fde_table[fd].fde_flags.open = 1;
#ifdef USE_POLL
	if (fd > highest_fd)
		highest_fd = fd;
#endif

	return fd;
}

void
wnet_close(fd)
{
struct	fde	*e = &fde_table[fd];

	wnet_register(fd, FDE_READ | FDE_WRITE, NULL, NULL);
	close(e->fde_fd);
	if (e->fde_cdata)
		wfree(e->fde_cdata);
	e->fde_flags.open = 0;
#ifdef USE_POLL
	if (fd == highest_fd)
		--highest_fd;
#endif
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

static void
wnet_write_do(e)
	struct fde *e;
{
struct	wrtbuf	*buf;
	int	 i;

	buf = e->fde_wdata;
	while ((i = write(e->fde_fd, buf->wb_buf + buf->wb_done, buf->wb_size - buf->wb_done)) > -1) {
		buf->wb_done += i;
		if (buf->wb_done == buf->wb_size) {
			wnet_register(e->fde_fd, FDE_WRITE, NULL, NULL);
			buf->wb_func(e, buf->wb_udata, 0);
			wfree(buf);
			return;
		}
	}

	if (errno == EWOULDBLOCK) 
		return;
			
	wnet_register(e->fde_fd, FDE_WRITE, NULL, NULL);
	buf->wb_func(e, buf->wb_udata, -1);
	wfree(buf);
}
