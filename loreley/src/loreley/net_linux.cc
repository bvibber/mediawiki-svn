/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* net_linux: Linux-specific networking.				*/
/* Copyright (c) 2005-2007 River Tarnell <river@attenuate.org>.		*/
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

#ifdef LINUX_IO

#include <sys/epoll.h>

using std::deque;
using std::signal;
using std::multimap;

#include "loreley.h"
#include "net.h"
#include "config.h"
#include "log.h"
#include "http.h"

static void sig_exit(int);

struct event_impl {
	event_impl();
	~event_impl();

	void schedule(int64_t);

	event_queue		*ei_queue;
	function<void (void)>	 ei_func;
};

struct event_queue {
	event_queue(int p) : epfd(p) {}
	int	epfd;
};

tss<event_queue *> ev_queue;

void
ioloop_t::prepare(void)
{
size_t	 i;

	wlog.notice(format("maximum number of open files: %d")
		% getdtablesize());
	
	signal(SIGPIPE, SIG_IGN);
	signal(SIGINT, sig_exit);
	signal(SIGTERM, sig_exit);

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
	wlog.notice("net: initialised, using Linux I/O");
}

namespace net {

void
socket::_register(int what, int64_t to, socket::call_type handler)
{
bool		add = !_ev_flags;
epoll_event	ev;
	_ev_flags = EPOLLONESHOT;
	_queue = *(event_queue **)ev_queue;

	WDEBUG(format("_register: %s%son %d (%s), queue %p")
		% ((what & FDE_READ) ? "read " : "")
		% ((what & FDE_WRITE) ? "write " : "")
		% _s % _desc % _queue);

	if (what & FDE_READ) {
		_ev_flags |= EPOLLIN;
		_read_handler = handler;
	}
	if (what & FDE_WRITE) {
		_ev_flags |= EPOLLOUT;
		_write_handler = handler;
	}

	memset(&ev, 0, sizeof(ev));
	ev.events = _ev_flags;
	ev.data.ptr = this;
	if (add)
		epoll_ctl(_queue->epfd, EPOLL_CTL_ADD, _s, &ev);
	else
		epoll_ctl(_queue->epfd, EPOLL_CTL_MOD, _s, &ev);
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
	clearbacks();
	if (_queue)
		epoll_ctl(_queue->epfd, EPOLL_CTL_DEL, _s, NULL);
	close(_s);
}

void
socket::clearbacks(void)
{
	if (_queue && (_read_handler || _write_handler)) {
		_read_handler = 0;
		_write_handler = 0;
		epoll_ctl(_queue->epfd, EPOLL_CTL_DEL, _s, NULL);
	}
}

} // namespace net

void
make_event_base(void)
{
	ev_queue = new event_queue * (new event_queue(epoll_create(getdtablesize() / config.nthreads)));
}

static void
sig_exit(int)
{
	wnet_exit = true;
	std::exit(0);
}

void
ioloop_t::thread_run(void)
{
event_queue	*eq = *ev_queue;
#if 0
event_impl	*ei;
#endif
epoll_event	 ev;
net::socket	*sk;
function<void (net::socket *, bool)> tmph;

	while (epoll_wait(eq->epfd, &ev, 1, -1) == 0) {
		WDEBUG(format("[%d] thread_run: waiting for event, eq=%p")
			% pthread_self() % eq);

		sk = static_cast<net::socket *>(ev.data.ptr);
		WDEBUG(format("[%d] thread_run: got event on %s")
			% pthread_self() % sk->_desc);
		if (ev.events & EPOLLIN) {
			tmph = sk->_read_handler;
			sk->_read_handler = 0;
			tmph(sk, false);
		}

		if (ev.events & EPOLLOUT) {
			tmph = sk->_write_handler;
			sk->_write_handler = 0;
			tmph(sk, false);
		}
		break;
#if 0
		case PORT_SOURCE_TIMER:
			WDEBUG("timer fires");
			ei = static_cast<event_impl *>(ev.portev_user);
			ei->ei_func();
			break;
#endif
	}
}

event_impl::event_impl(void)
{
#if 0
sigevent	se;
port_notify_t	pn;
	ei_queue = *(event_queue **)ev_queue;
	memset(&pn, 0, sizeof(pn));
	memset(&se, 0, sizeof(se));
	pn.portnfy_port = ei_queue->portfd;
	pn.portnfy_user = this;
	se.sigev_notify = SIGEV_PORT;
	se.sigev_value.sival_ptr = &pn;
	
	timer_create(CLOCK_REALTIME, &se, &ei_tmr);
#endif
}

event_impl::~event_impl(void)
{
#if 0
	timer_delete(ei_tmr);
#endif
}

void
event_impl::schedule(int64_t when)
{
#if 0
	WDEBUG(format("schedule, when=%d") % when);
	memset(&ei_when, 0, sizeof(ei_when));
	ei_when.it_value.tv_sec = (when / 1000);
	ei_when.it_value.tv_nsec = (when % 1000) * 1000000;
	timer_settime(ei_tmr, 0, &ei_when, NULL);
#endif
}

namespace net {

event::event(void)
	: impl(0)
{
}

event::~event(void)
{
	delete impl;
}

void
event::schedule(function<void (void)> f, int64_t t)
{
	if (!impl)
		impl = new event_impl;
	impl->ei_func = f;
	impl->schedule(t);
}

} // namespace net

#endif	/* LINUX_IO */
