/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* net_solaris: Solaris-specific networking.				*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
#ifdef __INTEL_COMPILER
# pragma hdrstop
#endif

#ifdef SOLARIS_IO

#include <port.h>

namespace sfun {
	using ::bind;	/* because of conflict with boost::bind from util.h */
};

using std::deque;
using std::signal;
using std::multimap;

#include "loreley.h"
#include "net.h"
#include "config.h"
#include "log.h"
#include "http.h"

enum evtype_t {
	evtype_timer,
	evtype_event
};

struct event_impl {
	void schedule(int64_t);

	int64_t			 ev_when;
	event_queue		*ev_queue;
	function<void (void)>	 ev_func;
};

struct ev_pending {
	ev_pending(::event *ev, int64_t to, evtype_t type)
		: ep_event(ev)
		, ep_timeout(to)
		, ep_type(type) {}

	::event	*ep_event;
	int64_t	 ep_timeout;
	evtype_t ep_type;
};

struct event_queue {
	event_queue(int p) : portfd(p) {}
	int	portfd;
};

tss<event_queue *> ev_queue;

pthread_cond_t iot_ready;
pthread_mutex_t iot_ready_m;

pthread_t io_thread;

void *
io_start(void *)
{
#if 0
	signal_set(&ev_sigusr2, SIGUSR2, usr2_handler, NULL);
	signal_add(&ev_sigusr2, NULL);
#endif
	pthread_mutex_lock(&iot_ready_m);
	pthread_cond_signal(&iot_ready);
	pthread_mutex_unlock(&iot_ready_m);

	ioloop->run();
	return NULL;
}

void
ioloop_t::prepare(void)
{
size_t	 i;

	pthread_mutex_init(&iot_ready_m, NULL);
	pthread_cond_init(&iot_ready, NULL);

	pthread_mutex_lock(&iot_ready_m);
	pthread_create(&io_thread, NULL, io_start, NULL);
	pthread_cond_wait(&iot_ready, &iot_ready_m);
	pthread_mutex_unlock(&iot_ready_m);

	wlog.notice(format("maximum number of open files: %d")
		% getdtablesize());
	
	signal(SIGPIPE, SIG_IGN);

	for (i = 0; i < listeners.size(); ++i) {
	listener	*lns = listeners[i];

		try {
			lns->sock->reuseaddr(true);
			lns->sock->bind();
			lns->sock->listen();
		} catch (socket_error &e) {
			wlog.error(format("creating listener %s: %s")
				% lns->sock->straddr() % e.what());
			exit(8);
		}

		lns->sock->readback(bind(&ioloop_t::_accept, this, _1, _2), -1);
	}
	wlog.notice("net: initialised, using Solaris I/O");
}

namespace net {

event::event(void)
{
	impl = new event_impl;
}

event::~event(void)
{
	delete impl;
};

void
event::schedule(function<void (void)> f, int64_t t)
{
	impl->ev_func = f;
	impl->schedule(t);
}

#if 0
void
timer_callback(int, short, void *d)
{
event_impl	*ev = (event_impl *)d;
	HOLDING(ev_lock);
	ev->ev_queue->add(NULL, ev, 0);
}
#endif

void
socket::_register(int what, int64_t to, socket::call_type handler)
{
	_ev_flags = 0;
	_queue = *(event_queue **)ev_queue;

	WDEBUG(format("_register: %s%son %d (%s), queue %p")
		% ((what & FDE_READ) ? "read " : "")
		% ((what & FDE_WRITE) ? "write " : "")
		% _s % _desc % _queue);

	if (what & FDE_READ)
		_read_handler = handler;
	if (what & FDE_WRITE)
		_write_handler = handler;

	port_associate(_queue->portfd, PORT_SOURCE_FD, _s, 
			(what & FDE_READ ? POLLRDNORM : 0) |
			(what & FDE_WRITE ? POLLWRNORM : 0), this);
}

socket::socket(int s, net::address const &a, char const *desc, sprio p)
	: _addr(a)
	, _desc(desc)
	, _prio(p)
	, _queue(0)
{
	_s = s;
}

socket::socket(net::address const &a, char const *desc, sprio p)
	: _addr(a)
	, _desc(desc)
	, _prio(p)
	, _queue(0)
{
	_s = ::socket(_addr.family(), _addr.socktype(), _addr.protocol());
	if (_s == -1)
		throw socket_error();
}

socket::~socket(void)
{
	WDEBUG("closing socket");
	if (_queue)
		port_dissociate(_queue->portfd, PORT_SOURCE_FD, _s);
	close(_s);
}

void
socket::clearbacks(void)
{
	port_dissociate(_queue->portfd, PORT_SOURCE_FD, _s);
}

} // namespace net

void
make_event_base(void)
{
//static lockable meb_lock;
//	if (evb == NULL) {
//		HOLDING(meb_lock);
//		evb = (event_base *)event_init();
//		event_base_priority_init(evb, prio_max);
//		signal_set(&ev_sigint, SIGINT, sig_exit, NULL);
//		signal_add(&ev_sigint, NULL);
//		signal_set(&ev_sigterm, SIGTERM, sig_exit, NULL);
//		signal_add(&ev_sigterm, NULL);
//	}
	ev_queue = new event_queue * (new event_queue(port_create()));
}

void
sig_exit(int sig, short what, void *d)
{
	wnet_exit = true;
}

void
ioloop_t::run(void)
{
	for (;;)
		sleep(INT_MAX);
#if 0
size_t	 i;
	for (i = 0; i < listeners.size(); ++i)
		delete listeners[i];
#endif
}

void
ioloop_t::thread_run(void)
{
event_queue	*eq = *ev_queue;
port_event_t	 ev;
net::socket	*sk;

	while (port_get(eq->portfd, &ev, NULL) == 0) {
		WDEBUG(format("[%d] thread_run: waiting for event, eq=%p")
			% pthread_self() % eq);

		switch (ev.portev_source) {
		case PORT_SOURCE_FD:
			sk = static_cast<net::socket *>(ev.portev_user);
			WDEBUG(format("[%d] thread_run: got event on %s")
				% pthread_self() % sk->_desc);
			if (ev.portev_events & POLLRDNORM)
				sk->_read_handler(sk, false);
			if (ev.portev_events & POLLWRNORM)
				sk->_write_handler(sk, false);
			break;

#if 0
				if ((*it)->ee_flags & EV_TIMEOUT) {
					if ((*it)->ee_sock->_ev_flags & EV_READ) {
						(*it)->ee_sock->_read_handler((*it)->ee_sock, true);
					} else if ((*it)->ee_sock->_ev_flags & EV_WRITE) {
						(*it)->ee_sock->_write_handler((*it)->ee_sock, true);
					}
				}
#endif
#if 0
				WDEBUG("event thread");
				(*it)->ee_event->ev_func();
#endif
		}
	}
}


void
event_impl::schedule(int64_t when)
{
#if 0
	HOLDING(ev_lock);
	WDEBUG(format("schedule, when=%d") % when);
	this->ev_when = when;
	this->ev_queue = (event_queue *)::ev_queue;
	evtimer_set(&ev_event, timer_callback, this);
	ev_pending_list.insert(make_pair(0, ev_pending(&ev_event, ev_when, evtype_timer)));
#endif
}

#endif
