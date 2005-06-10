/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet: Networking.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include <sys/types.h>
#include <sys/socket.h>

#include "config.h"
#ifdef HAVE_SYS_SENDFILE_H
# include <sys/sendfile.h>
#endif

#include <arpa/inet.h>

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <fcntl.h>
#include <signal.h>
#include <assert.h>
#include <strings.h>
#include <time.h>

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "wlog.h"
#include "whttp.h"

#define RDBUF_INC	8192	/* buffer in 8 KiB incrs		*/

struct wrtbuf {
	/* for buffers only */
const	void	*wb_buf;
	/* for sendfile only */
	off_t	 wb_off;
	int	 wb_source;
	/* for buffers & sendfile */
	size_t	 wb_size;
	int	 wb_done;
	fdwcb	 wb_func;
	void	*wb_udata;
};

char current_time_str[30];
char current_time_short[30];
#ifdef __lint
# pragma error_messages(off, E_GLOBAL_COULD_BE_STATIC)
#endif
time_t current_time;
#ifdef __lint
# pragma error_messages(default, E_GLOBAL_COULD_BE_STATIC)
#endif

static void init_fde(struct fde *);

static void wnet_accept(struct fde *);
static void wnet_write_do(struct fde *);
static void wnet_sendfile_do(struct fde *);

static void readbuf_reset(struct readbuf *);

struct fde *fde_table;
int max_fd;

int wnet_exit;

void
wnet_init(void)
{
	int	 i;

	max_fd = getdtablesize();
	if ((fde_table = wcalloc(max_fd, sizeof(struct fde))) == NULL)
		outofmemory();
				
	wlog(WLOG_NOTICE, "maximum number of open files: %d", max_fd);
	
	(void)signal(SIGPIPE, SIG_IGN);
	wnet_init_select();

	for (i = 0; i < nlisteners; ++i) {
		struct listener	*lns = listeners[i];

		int fd = wnet_open("listener");
		int one = 1;
		if (setsockopt(fd, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one)) == -1) {
			wlog(WLOG_ERROR, "setsockopt: %s: %s\n", lns->name, strerror(errno));
			exit(8);
		}
		if (bind(fd, (struct sockaddr *) &lns->addr, sizeof(lns->addr)) < 0) {
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
#ifdef __hpux
	int		 addrlen;
#else
	socklen_t	 addrlen;
#endif
	int		 newfd, val;
struct	fde		*newe;

	if ((cdata = wcalloc(1, sizeof(*cdata))) == NULL)
		outofmemory();

	addrlen = sizeof(cdata->cdat_addr);

	if ((newfd = accept(e->fde_fd, (struct sockaddr *) &cdata->cdat_addr, &addrlen)) < 0) {
		wlog(WLOG_NOTICE, "accept error: %s", strerror(errno));
		wfree(cdata);
		return;
	}

	if (newfd >= max_fd) {
		wlog(WLOG_NOTICE, "out of file descriptors!");
		wfree(cdata);
		(void)close(newfd);
		return;
	}

	val = fcntl(newfd, F_GETFL, 0);
	if (val == -1 || fcntl(newfd, F_SETFL, val | O_NONBLOCK) == -1) {
		wlog(WLOG_WARNING, "fcntl(%d) failed: %s", newfd, strerror(errno));
		wfree(cdata);
		(void)close(newfd);
		return;
	}

	newe = &fde_table[newfd];
	init_fde(newe);
	newe->fde_flags.open = 1;
	newe->fde_fd = newfd;
	newe->fde_cdata = cdata;
	newe->fde_desc = "accept()ed fd";
	(void)inet_ntop(AF_INET, &cdata->cdat_addr.sin_addr.s_addr, newe->fde_straddr, sizeof(newe->fde_straddr));

	WDEBUG((WLOG_DEBUG, "wnet_accept: new fd %d", newfd));
	http_new(newe);
	return;
}

static void
init_fde(fde)
	struct fde *fde;
{
	fde->fde_fd = 0;
	fde->fde_desc = "<unknown>";
	fde->fde_read_handler = NULL;
	fde->fde_write_handler = NULL;
	fde->fde_cdata = NULL;
	fde->fde_rdata = fde->fde_wdata = NULL;
	(void)strcpy(fde->fde_straddr, "NONE");
	fde->fde_epflags = 0;
	bzero(&fde->fde_readbuf, sizeof(fde->fde_readbuf));
	fde->fde_flags.open = 0;
}

int
wnet_open(desc)
	const char *desc;
{
	int	fd, val;

	if ((fd = socket(PF_INET, SOCK_STREAM, IPPROTO_TCP)) < 0) {
		wlog(WLOG_WARNING, "socket: %s", strerror(errno));
		return -1;
	}

	val = fcntl(fd, F_GETFL, 0);
	if (val == -1 || fcntl(fd, F_SETFL, val | O_NONBLOCK) == -1) {
		wlog(WLOG_WARNING, "fcntl(%d) failed: %s", fd, strerror(errno));
		return -1;
	}

	init_fde(&fde_table[fd]);
	fde_table[fd].fde_fd = fd;
	fde_table[fd].fde_desc = desc;
	fde_table[fd].fde_flags.open = 1;

	return fd;
}

void
wnet_set_blocking(fd)
	int fd;
{
	int	val;

	val = fcntl(fd, F_GETFL, 0);
	if (val == -1 || fcntl(fd, F_SETFL, val & ~O_NONBLOCK) == -1)
		wlog(WLOG_WARNING, "fcntl(%d) failed: %s", fd, strerror(errno));
}

void
wnet_close(fd)
	int fd;
{
struct	fde	*e = &fde_table[fd];

	wnet_register(fd, FDE_READ | FDE_WRITE, NULL, NULL);
	(void)close(e->fde_fd);
	if (e->fde_cdata)
		wfree(e->fde_cdata);
	readbuf_free(&e->fde_readbuf);
	e->fde_flags.open = 0;
	e->fde_read_handler = NULL;
	e->fde_write_handler = NULL;
}

int
wnet_sendfile(fd, source, size, off, cb, data, flags)
	int fd, source, flags;
	size_t size;
	off_t off;
	fdwcb cb;
	void *data;
{
struct	wrtbuf	*wb;
struct	fde	*e = &fde_table[fd];

	WDEBUG((WLOG_DEBUG, "wnet_sendfile: %d (+%ld) bytes from %d to %d [%s]", size, (long)off, source, fd, e->fde_desc));
	
	if ((wb = wcalloc(1, sizeof(*wb))) == NULL) {
		wlog(WLOG_WARNING, "out of memory");
		return -1;
	}
	
	wb->wb_done = 0;
	wb->wb_func = cb;
	wb->wb_udata = data;
	wb->wb_size = size;
	wb->wb_source = source;
	wb->wb_off = off;
	
	e->fde_wdata = wb;
	if (!(flags & WNET_IMMED))
		wnet_register(e->fde_fd, FDE_WRITE, wnet_sendfile_do, e);
	wnet_sendfile_do(e);
	return 0;
}

void
wnet_write(fd, buf, bufsz, cb, data, flags)
	int fd, flags;
	const void *buf;
	size_t bufsz;
	fdwcb cb;
	void *data;
{
struct	wrtbuf	*wb;
struct	fde	*e = &fde_table[fd];

	WDEBUG((WLOG_DEBUG, "wnet_write: %d bytes to %d [%s]", bufsz, e->fde_fd, e->fde_desc));
	
	if ((wb = wmalloc(sizeof(*wb))) == NULL)
		outofmemory();

	wb->wb_buf = buf;
	wb->wb_size = bufsz;
	wb->wb_done = 0;
	wb->wb_func = cb;
	wb->wb_udata = data;

	e->fde_wdata = wb;

	if (!(flags & WNET_IMMED))
		wnet_register(e->fde_fd, FDE_WRITE, wnet_write_do, e);
	wnet_write_do(e);
}

static void
wnet_write_do(e)
	struct fde *e;
{
struct	wrtbuf	*buf;
	int	 i;
#ifdef WILLOW_DEBUG
	char	*p;
#endif
	
	buf = e->fde_wdata;
	while ((i = write(e->fde_fd, (char *)buf->wb_buf + buf->wb_done, buf->wb_size - buf->wb_done)) > -1) {
#ifdef WILLOW_DEBUG
		(void)fprintf(stderr, "write buf: [");
		for (p = ((char *)buf->wb_buf + buf->wb_done); p < ((char *)buf->wb_buf + buf->wb_done + i); ++p)
			(void)fputc(*p, stderr);
		(void)fputs("]\n", stderr);
#endif
		buf->wb_done += i;
		WDEBUG((WLOG_DEBUG, "%d of %d done", buf->wb_done, buf->wb_size));
		if (buf->wb_done == (off_t)buf->wb_size) {
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

static void
wnet_sendfile_do(e)
	struct fde *e;
{
struct	wrtbuf *buf;
	int	i;
	/*LINTED unused variable: freebsd-only*/
	off_t	off, origoff;
	
	(void)off;

	buf = e->fde_wdata;
	origoff = buf->wb_off;
	
	WDEBUG((WLOG_DEBUG, "wnet_sendfile_do: for %d, off=%ld, size=%d", e->fde_fd, (long) buf->wb_off, buf->wb_size));
	/*
	 * On Solaris (sendfilev), FreeBSD, Tru64 UNIX and HP-UX (sendfile), we can write header data
	 * along with the sendfile, which improves performance and reduces syscall usage.
	 * At the moment this isn't supported, though...
	 *
	 * Linux sendfile() doesn't seem to have anything similar.
	 */
#if defined __linux__ || defined __sun
	i = sendfile(e->fde_fd, buf->wb_source, &buf->wb_off, buf->wb_size);
#elif defined __FreeBSD__ 
	i = sendfile(buf->wb_source, e->fde_fd, buf->wb_size, NULL, &off, 0);
	buf->wb_off += off;
	i = off;
#elif defined __hpux || (defined __digital__ && defined __unix__)
	i = sendfile(e->fde_fd, buf->wb_source, buf->wb_off, buf->wb_size, NULL, 0);
	buf->wb_off += i;
#else
# error i dont know how to invoke sendfile on this system
#endif
	buf->wb_size -= (buf->wb_off - origoff);
	
	if (buf->wb_size == 0) {
		wnet_register(e->fde_fd, FDE_WRITE, NULL, NULL);
		buf->wb_func(e, buf->wb_udata, 0);
		wfree(buf);
		return;
	}

	if (i == -1 && errno != EWOULDBLOCK) {
		wnet_register(e->fde_fd, FDE_WRITE, NULL, NULL);
		buf->wb_func(e, buf->wb_udata, -1);
		wfree(buf);
	}

	WDEBUG((WLOG_DEBUG, "wnet_sendfile_do: sendfile failed %s", strerror(errno)));
	
	if (errno == EWOULDBLOCK)
		return;
	
}

void
wnet_set_time(void)
{
struct	tm	*now;
	time_t	 old = current_time;
	size_t	 n;
	
	current_time = time(NULL);
	if (current_time == old)
		return;

	now = gmtime(&current_time);

	n = strftime(current_time_str, sizeof(current_time_str), "%a, %d %b %Y %H:%M:%S GMT", now);
	assert(n);
	n = strftime(current_time_short, sizeof(current_time_short), "%Y-%m-%d %H:%M:%S", now);
	assert(n);
}


int
readbuf_getdata(fde)
	struct fde *fde;
{
	int	i;

	WDEBUG((WLOG_DEBUG, "readbuf_getdata: called"));
	if (readbuf_data_left(&fde->fde_readbuf) == 0)
		readbuf_reset(&fde->fde_readbuf);
	
	if (readbuf_spare_size(&fde->fde_readbuf) == 0) {
		WDEBUG((WLOG_DEBUG, "readbuf_getdata: no space in buffer"));
		fde->fde_readbuf.rb_size += RDBUF_INC;
		fde->fde_readbuf.rb_p = realloc(fde->fde_readbuf.rb_p, fde->fde_readbuf.rb_size);
	}

	if ((i = read(fde->fde_fd, readbuf_spare_start(&fde->fde_readbuf), readbuf_spare_size(&fde->fde_readbuf))) < 1)
		return i;
	fde->fde_readbuf.rb_dsize += i;
	WDEBUG((WLOG_DEBUG, "readbuf_getdata: read %d bytes", i));

	return i;
}

void
readbuf_free(buffer)
	struct readbuf *buffer;
{
	if (buffer->rb_p)
		free(buffer->rb_p);
	bzero(buffer, sizeof(*buffer));
}

static void
readbuf_reset(buffer)
	struct readbuf *buffer;
{
	buffer->rb_dpos = buffer->rb_dsize = 0;
}	
