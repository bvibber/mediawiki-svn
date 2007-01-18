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
using std::multiset;

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

	uint64_t		 ei_when;	/* milliseconds */
	event_queue		*ei_queue;
	function<void (void)>	 ei_func;
};

struct evtp_cmp {
	bool operator() (event_impl const *a, event_impl const *b) const {
		return a->ei_when < b->ei_when;
	}
};

struct event_queue {
	event_queue(int p) : epfd(p) {}
	int	epfd;
};

tss<event_queue *> ev_queue;
tss<multiset<event_impl *, evtp_cmp> > ev_threadevents;

static event_impl *
next_event(void)
{
multiset<event_impl *, evtp_cmp> &evt = *ev_threadevents;
timeval		tod;
uint64_t	now;
	gettimeofday(&tod, 0);
	now = tod.tv_sec * 1000 + tod.tv_usec / 1000;
	while (!evt.empty()) {
	event_impl	*r;
		r = *evt.begin();
		if (r->ei_when <= now) {
			evt.erase(evt.begin());
			return r;
		}
		break;
	}
	return NULL;
}

static int64_t
next_evt_time(void)
{
	if (ev_threadevents->empty())
		return -1;

uint64_t	now;
timeval		tod;
	gettimeofday(&tod, 0);
	now = tod.tv_sec * 1000 + tod.tv_usec / 1000;
	return (*ev_threadevents->begin())->ei_when - now;
}

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
	, _ev_flags(0)
{
	_s = s;
}

socket::socket(net::address const &a, char const *desc, sprio p)
	: _addr(a)
	, _desc(desc)
	, _prio(p)
	, _queue(0)
	, _ev_flags(0)
{
	_s = ::socket(_addr.family(), _addr.socktype(), _addr.protocol());
	if (_s == -1)
		throw socket_error();
}

socket::~socket(void)
{
	WDEBUG("closing socket");
	if (_queue)
		epoll_ctl(_queue->epfd, EPOLL_CTL_DEL, _s, NULL);
	close(_s);
}

void
socket::clearbacks(void)
{
	if (_queue && (_read_handler || _write_handler)) {
	epoll_event	ev;
		_read_handler = 0;
		_write_handler = 0;
		memset(&ev, 0, sizeof(ev));
		ev.events = 0;
		epoll_ctl(_queue->epfd, EPOLL_CTL_MOD, _s, &ev);
	}
}

} // namespace net

void
make_event_base(void)
{
	assert(!ev_queue);
	ev_queue = new event_queue * (new event_queue(epoll_create(getdtablesize() / config.nthreads)));
	ev_threadevents = new multiset<event_impl *, evtp_cmp>;
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
epoll_event	 ev;
net::socket	*sk;
event_impl	*ei;
function<void (net::socket *, bool)> tmph;

	for (;;) {
	int	r;
	
		WDEBUG(format("[%d] thread_run: waiting for event, eq=%p")
			% pthread_self() % eq);
		r = epoll_wait(eq->epfd, &ev, 1, next_evt_time());
		if (r == -1 && errno != EINTR)
			break;

		if (r == 1) {
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
		}

		while ((ei = next_event()) != 0) {
			ei->ei_func();
		}
	}
}

event_impl::event_impl(void)
{
}

event_impl::~event_impl(void)
{
	ev_threadevents->erase(this);
}

void
event_impl::schedule(int64_t when)
{
timeval	tod;
	gettimeofday(&tod, 0);
	ei_when = (tod.tv_sec * 1000 + tod.tv_usec / 1000) + when;
	ev_threadevents->insert(this);
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
