/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet: Networking.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <sys/types.h>
#include <sys/socket.h>

#include "config.h"
#ifdef HAVE_SYS_SENDFILE_H
# include <sys/sendfile.h>
#endif

#include <arpa/inet.h>

#include <cstdio>
#include <cstring>
#include <cstdlib>
#include <cerrno>
#include <csignal>
#include <cassert>
#include <ctime>
#include <deque>
using std::deque;

#include <unistd.h>
#include <fcntl.h>

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "wlog.h"
#include "whttp.h"

#define RDBUF_INC	8192	/* buffer in 8 KiB incrs		*/

struct event ev_sigint;
struct event ev_sigterm;
tss<event_base> evb;

static void fde_ev_callback(int, short, void *);
static void sig_exit(int, short, void *);

ioloop_t *ioloop;

struct wrtbuf : freelist_allocator<wrtbuf> {
	/* for buffers only */
const	void	*wb_buf;
	/* for sendfile only */
	off_t	 wb_off;
	int	 wb_source;
	/* for buffers & sendfile */
	size_t	 wb_size;
	int	 wb_done;
	polycallback<fde *, int> wb_func;
	void	*wb_udata;
};

char current_time_str[30];
char current_time_short[30];
time_t current_time;

static void init_fde(struct fde *);

static void wnet_accept(struct fde *);
static void wnet_write_do(struct fde *);
static void wnet_sendfile_do(struct fde *);

static void readbuf_reset(struct readbuf *);
static void secondly_sched(void);

struct fde *fde_table;
int max_fd;

int wnet_exit;
vector<int>	awaks;
int		cawak;

void
wnet_add_accept_wakeup(int s)
{
	awaks.push_back(s);
}

event	secondly_ev;
timeval	secondly_tv;

static void
secondly_update(int, short, void *)
{
	wnet_set_time();
	secondly_sched();
}

static void
secondly_sched(void)
{
	secondly_tv.tv_usec = 0;
	secondly_tv.tv_sec = 1;
	evtimer_set(&secondly_ev, secondly_update, NULL);
	event_base_set(evb, &secondly_ev);
	event_add(&secondly_ev, &secondly_tv);
}

ioloop_t::ioloop_t(void)
{
	prepare();
}

void
ioloop_t::prepare(void)
{
size_t	 i;

	max_fd = getdtablesize();
	if ((fde_table = (fde *)wcalloc(max_fd, sizeof(struct fde))) == NULL)
		outofmemory();
				
	wlog(WLOG_NOTICE, "maximum number of open files: %d", max_fd);
	
	(void)signal(SIGPIPE, SIG_IGN);
	wnet_init_select();

	for (i = 0; i < listeners.size(); ++i) {
		struct listener	*lns = listeners[i];

		int fd = wnet_open("listener", prio_accept, lns->addr.ss_family);
		int one = 1;
		if (setsockopt(fd, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one)) == -1) {
			wlog(WLOG_ERROR, "setsockopt: %s: %s\n", lns->name.c_str(), strerror(errno));
			exit(8);
		}
		if (bind(fd, (struct sockaddr *) &lns->addr, sizeof(lns->addr)) < 0) {
			wlog(WLOG_ERROR, "bind: %s: %s\n", lns->name.c_str(), strerror(errno));
			exit(8);
		}
		if (listen(fd, 10) < 0) {
			wlog(WLOG_ERROR, "listen: %s: %s\n", lns->name.c_str(), strerror(errno));
			exit(8);
		}
		lsn2group[fd] = lns->group;
		readback(fd, polycaller<fde *, int>(*this, &ioloop_t::_accept), 0);
	}
	wlog(WLOG_NOTICE, "wnet: initialised, using libevent %s (%s)",
		event_get_version(), event_get_method());
	secondly_sched();
}

void
ioloop_t::_accept(fde *e, int)
{
struct	client_data	*cdata;
	int		 newfd, val;
struct	fde		*newe;
static time_t		 last_nfile = 0;
	time_t		 now = time(NULL);
	if ((cdata = (client_data *)wcalloc(1, sizeof(*cdata))) == NULL)
		outofmemory();
	cdata->cdat_addrlen = sizeof(cdata->cdat_addr);

	if ((newfd = accept(e->fde_fd, (struct sockaddr *) &cdata->cdat_addr, &cdata->cdat_addrlen)) < 0) {
		if (errno != ENFILE || now - last_nfile > 60) 
			wlog(WLOG_NOTICE, "accept error: %s", strerror(errno));
		if (errno == ENFILE)
			last_nfile = now;
		wfree(cdata);
		return;
	}

	if (newfd >= max_fd) {
		if (errno != ENFILE || now - last_nfile > 60) 
			wlog(WLOG_NOTICE, "out of file descriptors!");
		if (errno == ENFILE)
			last_nfile = now;
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
	newe->fde_flags.read_held = newe->fde_flags.write_held = 1;
	newe->fde_desc = "accept()ed fd";
	if (cdata->cdat_addr.ss_family == AF_INET)
		inet_ntop(AF_INET, &((sockaddr_in *)&cdata->cdat_addr)->sin_addr.s_addr, 
		  newe->fde_straddr, sizeof(newe->fde_straddr));
	else
		inet_ntop(AF_INET6, &((sockaddr_in6 *)&cdata->cdat_addr)->sin6_addr.s6_addr, 
		  newe->fde_straddr, sizeof(newe->fde_straddr));

	WDEBUG((WLOG_DEBUG, "wnet_accept: new fd %d", newfd));
	if (cawak == awaks.size())
		cawak = 0;
int	fds[2] = { newfd, e->fde_fd };
	if (write(awaks[cawak], fds, sizeof(fds)) < 0) {
		wlog(WLOG_ERROR, "writing to thread wakeup socket: %s", strerror(errno));
		exit(1);
	}
	cawak++;
	return;
}

int
wnet_socketpair(int d, int type, int protocol, int sv[2])
{
fde	*e;
	if (socketpair(d, type, protocol, sv) < 0)
		return -1;
	e = &fde_table[sv[0]];
	init_fde(e);
	e->fde_flags.open = 1;
	e->fde_fd = sv[0];

	e = &fde_table[sv[1]];
	init_fde(e);
	e->fde_flags.open = 1;
	e->fde_fd = sv[1];
	return 0;
}	
static void
init_fde(fde *fde)
{
	bzero(fde, sizeof(*fde));
	fde->fde_desc = "<unknown>";
	(void)strcpy(fde->fde_straddr, "NONE");
}

int
wnet_open(const char *desc, sprio p, int aftype, int type)
{
	int	fd, val;
static int	last_nfile = 0;
	time_t	now = time(NULL);
	if ((fd = socket(aftype, type, 0)) < 0) {
		if (errno != ENFILE || now - last_nfile > 60) 
			wlog(WLOG_WARNING, "socket: %s", strerror(errno));
		if (errno == ENFILE)
			last_nfile = now;
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
	fde_table[fd].fde_prio = p;
	fde_table[fd].fde_flags.read_held = fde_table[fd].fde_flags.write_held = 1;
	return fd;
}

void
wnet_set_blocking(int fd)
{
	int	val;

	val = fcntl(fd, F_GETFL, 0);
	if (val == -1 || fcntl(fd, F_SETFL, val & ~O_NONBLOCK) == -1)
		wlog(WLOG_WARNING, "fcntl(%d) failed: %s", fd, strerror(errno));
}

void
wnet_close(int fd)
{
struct	fde	*e = &fde_table[fd];
	assert(e->fde_flags.open);
	WDEBUG((WLOG_DEBUG, "close fd %d [%s]", e->fde_fd, e->fde_desc));
	ioloop->clear_readback(fd);
	ioloop->clear_writeback(fd);
	if (e->fde_cdata)
		wfree(e->fde_cdata);
	readbuf_free(&e->fde_readbuf);
	e->fde_flags.open = 0;

	/*
	 * Do NOT touch the fde after closing this - we do not mutex
	 * fde accesses and it will be immediately reused.
	 */
	(void)close(e->fde_fd);
}

#if 0
int
wnet_sendfile(int fd, int source, size_t size, off_t off, fdwcb cb, void *data, int flags)
{
struct	wrtbuf	*wb;
struct	fde	*e = &fde_table[fd];

	WDEBUG((WLOG_DEBUG, "wnet_sendfile: %d (+%ld) bytes from %d to %d [%s]", 
		size, (long)off, source, fd, e->fde_desc));
	
	if ((wb = (wrtbuf *)wcalloc(1, sizeof(*wb))) == NULL) {
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
	wnet_register(e->fde_fd, FDE_WRITE, wnet_sendfile_do, e);
	wnet_sendfile_do(e);
	return 0;
}

void
wnet_write(int fd, const void *buf, size_t bufsz, fdwcb cb, void *data, int flags)
{
struct	wrtbuf	*wb;
struct	fde	*e = &fde_table[fd];

	WDEBUG((WLOG_DEBUG, "wnet_write: %d bytes to %d [%s]", bufsz, e->fde_fd, e->fde_desc));
	
	wb = new wrtbuf;

	wb->wb_buf = buf;
	wb->wb_size = bufsz;
	wb->wb_done = 0;
	wb->wb_func = cb;
	wb->wb_udata = data;

	e->fde_wdata = wb;

	wnet_register(e->fde_fd, FDE_WRITE, wnet_write_do, e);
	wnet_write_do(e);
}

static void
wnet_write_do(fde *e)
{
struct	wrtbuf	*buf;
	int	 i;
	
	buf = (wrtbuf *)e->fde_wdata;
	while ((i = write(e->fde_fd, (char *)buf->wb_buf + buf->wb_done, buf->wb_size - buf->wb_done)) > -1) {
		buf->wb_done += i;
		WDEBUG((WLOG_DEBUG, "%d of %d done", buf->wb_done, buf->wb_size));
		if (buf->wb_done == (off_t)buf->wb_size) {
			wnet_register(e->fde_fd, FDE_WRITE, NULL, NULL);
			buf->wb_func(e, buf->wb_udata, 0);
			delete buf;
			return;
		}
	}

	if (errno == EWOULDBLOCK) 
		return;
			
	wnet_register(e->fde_fd, FDE_WRITE, NULL, NULL);
	buf->wb_func(e, buf->wb_udata, -1);
	delete buf;
}

static void
wnet_sendfile_do(fde *e)
{
struct	wrtbuf *buf;
	int	i;
	/*LINTED unused variable: freebsd-only*/
	off_t	off, origoff;
	
	(void)off;

	buf = (wrtbuf *)e->fde_wdata;
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
#elif defined __hpux || (defined __digital__ && defined __unix__)
	i = sendfile(e->fde_fd, buf->wb_source, buf->wb_off, buf->wb_size, NULL, 0);
	buf->wb_off += i;
#else
# error i dont know how to invoke sendfile on this system
#endif

#ifdef __linux
	/*
	 * The Linux sendfile() manual page says:
	 *
	 *   When sendfile() returns, this variable will be set to the offset of the byte following the
	 *   last byte that was read.
	 *
	 * However, this is not true on x86-64 when we are compiled as a 32-bit binary; the correct
	 * number of bytes is returned, but off is _not_ updated.  So, we fudge it into working as we
	 * expect.
	 */
	if (i > 0 && buf->wb_off == origoff)
		buf->wb_off += i;
#endif

	buf->wb_size -= (buf->wb_off - origoff);
	WDEBUG((WLOG_DEBUG, "sent %d bytes i=%d", (int)(buf->wb_off - origoff), i));
	
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
#endif

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
readbuf_getdata(fde *fde)
{
	int	i;

	WDEBUG((WLOG_DEBUG, "readbuf_getdata: called"));
	if (readbuf_data_left(&fde->fde_readbuf) == 0)
		readbuf_reset(&fde->fde_readbuf);
	
	if (readbuf_spare_size(&fde->fde_readbuf) == 0) {
		WDEBUG((WLOG_DEBUG, "readbuf_getdata: no space in buffer"));
		fde->fde_readbuf.rb_size += RDBUF_INC;
		fde->fde_readbuf.rb_p = (char *)wrealloc(fde->fde_readbuf.rb_p, fde->fde_readbuf.rb_size);
	}

	if ((i = read(fde->fde_fd, readbuf_spare_start(&fde->fde_readbuf), readbuf_spare_size(&fde->fde_readbuf))) < 1)
		return i;
	fde->fde_readbuf.rb_dsize += i;
	WDEBUG((WLOG_DEBUG, "readbuf_getdata: read %d bytes", i));

	return i;
}

void
readbuf_free(readbuf *buffer)
{
	if (buffer->rb_p)
		free(buffer->rb_p);
	bzero(buffer, sizeof(*buffer));
}

static void
readbuf_reset(readbuf *buffer)
{
	buffer->rb_dpos = buffer->rb_dsize = 0;
}	

namespace wnet {

string
straddr(sockaddr const *addr, socklen_t len)
{
char	res[NI_MAXHOST];
int	i;
	if ((i = getnameinfo(addr, len, res, sizeof(res), NULL, 0, NI_NUMERICHOST)) != 0)
		return ""; /* XXX */
	return res;
}

string
fstraddr(string const &straddr, sockaddr const *addr, socklen_t len)
{
char	host[NI_MAXHOST];
char	port[NI_MAXSERV];
string	res;
int	i;
	if ((i = getnameinfo(addr, len, host, sizeof(host), port, sizeof(port), 
			     NI_NUMERICHOST | NI_NUMERICSERV)) != 0)
		return "";
	return straddr + '[' + host + "]:" + port;
}

} // namespace wnet

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
	WDEBUG((WLOG_DEBUG, "fde_ev_callback: %s%son %d [%s]",
		(ev & EV_READ) ? "read " : "",
		(ev & EV_WRITE) ? "write " : "",
		fd, fde->fde_desc));

	assert(fde->fde_flags.open);

	if (ev & EV_READ)
		fde->fde_read_handler(fde);
	if (ev & EV_WRITE)
		fde->fde_write_handler(fde);
	if (!fde->fde_flags.read_held || !fde->fde_flags.write_held) {
		WDEBUG((WLOG_DEBUG, "fde_ev_callback: rescheduling %d", fd));
		event_add(&fde->fde_ev, NULL);
	}
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

	WDEBUG((WLOG_DEBUG, "_register: %s%son %d [%s]",
		(what & FDE_READ) ? "read " : "",
		(what & FDE_WRITE) ? "write " : "",
		fd, fde->fde_desc));

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
