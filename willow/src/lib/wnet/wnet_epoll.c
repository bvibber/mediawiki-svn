/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet: Networking.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#define _XOPEN_SOURCE 600

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
#include <sys/epoll.h>
#include <pthread.h>

#include "willow.h"
#include "wnet.h"
#include "wconfig.h"
#include "wlog.h"
#include "whttp.h"

static void run_event(struct fde *, int);

static int epfd;
pthread_cond_t t_cond = PTHREAD_COND_INITIALIZER;
pthread_mutex_t t_mtx = PTHREAD_MUTEX_INITIALIZER;
static void *thread_event_wait(void *data);

#define NTHREADS 20
static pthread_t thread_ids[NTHREADS];
struct {
        int      fd;
        int      flags;
} t_event;

void
wnet_init_select(void)
{
	int	 i;

	signal(SIGPIPE, SIG_IGN);

	if ((epfd = epoll_create(1024)) < 0) {
		perror("epoll_create");
		exit(8);
	}

#ifdef THREADED_IO
        WDEBUG((WLOG_DEBUG, "wnet_init_select: thread startup"));

        (void) pthread_mutex_lock(&t_mtx);

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

#ifdef THREADED_IO
#if 0
struct cb_args {
	struct fde *fde;
	fdcb cb;
};

void *
wnet_run_pthread(d)
	void *d;
{
struct	epoll_event	 ev;
struct	cb_args		*args = d;

	args->cb(args->fde);
	FDE_LOCK(args->fde);

	if (args->fde->fde_epflags) {
		bzero(&ev, sizeof(ev));
		ev.events = args->fde->fde_epflags | (EPOLLET | EPOLLONESHOT);
		ev.data.fd = args->fde->fde_fd;

		epoll_ctl(epfd, EPOLL_CTL_ADD, args->fde->fde_fd, &ev);
	}

	args->fde->fde_flags.held = 0;
	FDE_UNLOCK(args->fde);
	wfree(args);
	return NULL;
}
#endif

static void *
thread_event_wait(data)
        void *data;
{
        int              fd, flags;
struct	epoll_event	 ev;

        WDEBUG((WLOG_DEBUG, "[%u] starting", pthread_self()));

        for (;;) {
                int i;

                WDEBUG((WLOG_DEBUG, "[%u] waiting", pthread_self()));

                i = epoll_wait(epfd, &ev, 1, -1);
                if (i == -1) {
                        wlog(WLOG_WARNING, "epoll_wait: %s", strerror(errno));
                        break;
                }

                WDEBUG((WLOG_DEBUG, "[%u] activity on %d", pthread_self(), (int)ev.data.fd));
                wnet_set_time();

                fd = ev.data.fd;
                FDE_LOCK(&fde_table[fd]);
                run_event(&fde_table[fd], ev.events);
                FDE_UNLOCK(&fde_table[fd]);
        }
        /*NOTREACHED*/
}

static void
run_event(fde, events)
        struct fde *fde;
        int events;
{
        int      hadread, hadwrite;

        hadread = fde->fde_epflags & EPOLLIN;
        hadwrite = fde->fde_epflags & EPOLLOUT;

        /*
         * Immediately re-associate.  If the caller doesn't want it,
         * they'll dissociate it themselves.  This could be optimised
         * a little to save 2 syscalls in some cases...
         */

        if ((events & EPOLLIN) && fde->fde_read_handler) {
                WDEBUG((WLOG_DEBUG, "\tread"));
                fde->fde_read_handler(fde);
                if (hadread && (fde->fde_epflags & EPOLLIN)) {
			struct epoll_event fv;
			fv.data.fd = fde->fde_fd;
			fv.events = fde->fde_epflags | EPOLLET | EPOLLONESHOT;
                        if (epoll_ctl(epfd, EPOLL_CTL_MOD, fde->fde_fd, &fv) == -1) {
                                wlog(WLOG_ERROR, "epoll_mod: %s", strerror(errno));
                                exit(8);
                        }
		}
        }

        if ((events & EPOLLOUT) && fde->fde_write_handler) {
                WDEBUG((WLOG_DEBUG, "\twrite"));
                fde->fde_write_handler(fde);
                if (hadwrite && (fde->fde_epflags & EPOLLOUT)) {
			struct epoll_event fv;
			fv.data.fd = fde->fde_fd;
			fv.events = fde->fde_epflags | EPOLLET | EPOLLONESHOT;
                        if (epoll_ctl(epfd, EPOLL_CTL_MOD, fde->fde_fd, &fv) == -1) {
                                wlog(WLOG_ERROR, "epoll_mod: %s", strerror(errno));
                                exit(8);
                        }
		}
        }
}
#endif


void
wnet_run(void)
{
	int		i, n;
struct	epoll_event	events[256];

#ifdef THREADED_IO
	thread_event_wait(NULL);
#else
	for (;;) {
		i = epoll_wait(epfd, events, 1, -1);
		if (wnet_exit)
			break;
		if (i == -1 && errno == EINTR)
			continue;
		if (i == -1)
			break;
		wnet_set_time();

		for (n = 0; n < i; ++n) {
			struct fde *e = &fde_table[events[n].data.fd];
			if ((events[n].events & EPOLLIN) && e->fde_read_handler) {
				e->fde_read_handler(e);
			}

			if ((events[n].events & EPOLLOUT) && e->fde_write_handler) {
				e->fde_write_handler(e);
			}
		}
	}
#endif
	perror("epoll_wait");
}

void
wnet_register(fd, what, handler, data)
	fdcb handler;
	void *data;
{
struct	fde		*e = &fde_table[fd];
	int		 flags = e->fde_epflags, mod = flags;
struct	epoll_event	 ev;

	FDE_LOCK(e);

	if (handler == NULL) {
		e->fde_epflags = 0;
		epoll_ctl(epfd, EPOLL_CTL_DEL, fd, NULL);
		FDE_UNLOCK(e);
		return;
	}

	e->fde_fd = fd;
	if (what & FDE_READ) {
		e->fde_read_handler = handler;
		e->fde_epflags |= EPOLLIN;
	} else e->fde_read_handler = NULL;
	if (what & FDE_WRITE) {
		e->fde_write_handler = handler;
		e->fde_epflags |= EPOLLOUT;
	} else e->fde_write_handler = NULL;

	if (data)
		e->fde_rdata = data;

	bzero(&ev, sizeof(ev));
	ev.events = e->fde_epflags | (EPOLLET | EPOLLONESHOT);
	ev.data.fd = fd;
	if (e->fde_flags.held) {
		FDE_UNLOCK(e);
		return;
	}
	if (mod) {
		if (epoll_ctl(epfd, EPOLL_CTL_MOD, fd, &ev) < 0) {
			if (epoll_ctl(epfd, EPOLL_CTL_ADD, fd, &ev) < 0) {
				perror("epoll_ctl(MOD)");
				exit(8);
			}
		} 
	} else {
		if (epoll_ctl(epfd, EPOLL_CTL_ADD, fd, &ev) < 0) {
			perror("epoll_ctl(ADD)");
			exit(8);
		}
	}
	FDE_UNLOCK(e);
}
