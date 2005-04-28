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
#include <unistd.h>

#include "config.h"

#ifdef THREADED_IO
# include <pthread.h>
#endif

#include "wnet.h"
#include "wlog.h"

#define READABLE POLLRDNORM

static void run_event(struct fde *, int);

static int port;
#define GETN 256
static port_event_t pe[GETN];

#ifdef THREADED_IO
# define NTHREADS 20
static pthread_t thread_ids[NTHREADS];
struct {
	int	 fd;
	int	 flags;
} t_event;

pthread_cond_t t_cond = PTHREAD_COND_INITIALIZER;
pthread_mutex_t t_mtx = PTHREAD_MUTEX_INITIALIZER;

static void *
thread_event_wait(data)
	void *data;
{
	int		 fd, flags;
	port_event_t	 pe;
	
	WDEBUG((WLOG_DEBUG, "[%u] starting", pthread_self()));
	
	for (;;) {
		int i;
		
		WDEBUG((WLOG_DEBUG, "[%u] waiting", pthread_self()));
		
		i = port_get(port, &pe, NULL);
		if (i == -1) {
			wlog(WLOG_WARNING, "port_get: %s", strerror(errno));
			break;
		}

		WDEBUG((WLOG_DEBUG, "[%u] activity on %d", pthread_self(), (int)pe.portev_object));
		wnet_set_time();
		
		fd = pe.portev_object;
		FDE_LOCK(&fde_table[fd]);
		run_event(&fde_table[fd], pe.portev_events);
		FDE_UNLOCK(&fde_table[fd]);
	}
	/*NOTREACHED*/
}
#endif

static void
run_event(fde, events)
	struct fde *fde;
	int events;
{
	int	 hadread, hadwrite;

	hadread = fde->fde_epflags & READABLE;
	hadwrite = fde->fde_epflags & POLLWRNORM;

	/*
	 * Immediately re-associate.  If the caller doesn't want it,
	 * they'll dissociate it themselves.  This could be optimised
	 * a little to save 2 syscalls in some cases...
	 */

	if ((events & READABLE) && fde->fde_read_handler) {
		WDEBUG((WLOG_DEBUG, "\tread", fde->fde_fd));
		fde->fde_read_handler(fde);
		if (hadread && (fde->fde_epflags & READABLE))
			if (port_associate(port, PORT_SOURCE_FD, fde->fde_fd, READABLE, NULL) == -1) {
				wlog(WLOG_ERROR, "port_associate: %s", strerror(errno));
				exit(8);
			}
	}
	
	if ((events & (POLLWRNORM | POLLERR)) && fde->fde_write_handler) {
		WDEBUG((WLOG_DEBUG, "\twrite", fde->fde_fd));
		fde->fde_write_handler(fde);
		if (hadwrite && (fde->fde_epflags & POLLWRNORM))
			if (port_associate(port, PORT_SOURCE_FD, fde->fde_fd, POLLWRNORM, NULL) == -1) {
				wlog(WLOG_ERROR, "port_associate: %s", strerror(errno));
				exit(8);
			}
	}
}

void
wnet_init_select(void)
{
	int	 i;
	
	if ((port = port_create()) < 0) {
		perror("port_create");
		exit(8);
	}
	
#ifdef THREADED_IO
	WDEBUG((WLOG_DEBUG, "wnet_init_select: thread startup"));
	
	pthread_mutex_lock(&t_mtx);
	
	for (i = 0; i < NTHREADS; ++i) {
		pthread_attr_t attr;
		pthread_attr_init(&attr);
		//pthread_attr_setscope(&attr, PTHREAD_SCOPE_SYSTEM);
		if (pthread_create(&thread_ids[i], &attr, thread_event_wait, NULL) != 0) {
			perror("pthread_create");
			exit(8);
		}
	}
	for (i = 0; i < getdtablesize(); ++i) {
		pthread_mutexattr_t attr;
		if (pthread_mutexattr_init(&attr) != 0) {
			perror("pthread_mutexattr_init");
			exit(8);
		}
		if (pthread_mutexattr_settype(&attr, PTHREAD_MUTEX_RECURSIVE) != 0) {
			perror("pthread_mutexattr_settype");
			exit(8);
		}
		if (pthread_mutex_init(&fde_table[i].fde_mtx, &attr) != 0) {
			perror("pthread_mutex_init");
			exit(8);
		}
	}
#endif
}

void
wnet_run(void)
{
	int		i;
	uint		nget = 1;

#ifdef THREADED_IO
	thread_event_wait(NULL);
#else
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

			WDEBUG((WLOG_DEBUG, "activity on fd %d [%s]", e->fde_fd, e->fde_desc));

			run_event(e, pe[i].portev_events);
		}
		nget = 1;
	}
#endif
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

	FDE_LOCK(e);
	
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
	
	if (oldflags == e->fde_epflags) {
		/* no change */
		FDE_UNLOCK(e);
		return;
	}
		
	if (e->fde_epflags) {
		if (port_associate(port, PORT_SOURCE_FD, fd, e->fde_epflags, NULL) < 0) {
			perror("port_associate");
			abort();
		}
	} else {
		(void)port_dissociate(port, PORT_SOURCE_FD, fd);
	}
	
	FDE_UNLOCK(e);
}
