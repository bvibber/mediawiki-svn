/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* net_unix: General Unix networking.					*/
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

#if !defined(LINUX_IO) && !defined(SOLARIS_IO)

#include <poll.h>

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

struct poll_entry {
	poll_entry() : pe_fd(-1), pe_events(0) {}
	int		 pe_events;
	int		 pe_fd;
	net::socket	*pe_sk;
};

struct event_queue {
	vector<poll_entry>	pfds;
};

tss<event_queue> ev_queue;
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
	wlog.notice("net: initialised, using Unix I/O");
}

namespace net {

void
socket::_register(int what, int64_t to, socket::call_type handler)
{
	_queue = ev_queue;

	WDEBUG(format("_register: %s%son %d (%s), queue %p")
		% ((what & FDE_READ) ? "read " : "")
		% ((what & FDE_WRITE) ? "write " : "")
		% _s % _desc % _queue);

	if (_queue->pfds.size() <= _s)
		_queue->pfds.resize(_s + 1);
	if (_queue->pfds[_s].pe_fd == -1) {
		_queue->pfds[_s].pe_fd = _s;
		_queue->pfds[_s].pe_events = 0;
		_queue->pfds[_s].pe_sk = this;
	}

	if (what & FDE_READ) {
		_queue->pfds[_s].pe_events |= POLLIN;
		_read_handler = handler;
	}
	if (what & FDE_WRITE) {
		_queue->pfds[_s].pe_events |= POLLOUT;
		_write_handler = handler;
	}
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
		if (_queue->pfds.size() > _s)
			_queue->pfds[_s].pe_fd = -1;
	close(_s);
}

void
socket::clearbacks(void)
{
	if (_queue && (_read_handler || _write_handler)) {
		if (_queue->pfds.size() > _s)
			_queue->pfds[_s].pe_fd = -1;
		_read_handler = 0;
		_write_handler = 0;
	}
}

} // namespace net

void
make_event_base(void)
{
	ev_queue = new event_queue;
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
vector<pollfd>	pfds;
net::socket	*sk;
event_impl	*ei;

	for (;;) {
	int	r;
		pfds.clear();
		pfds.reserve(ev_queue->pfds.size());
		for (vector<poll_entry>::iterator it = ev_queue->pfds.begin(),
						  end = ev_queue->pfds.end();
		     it != end; ++it)
		{
		pollfd	pfd;
			if (it->pe_fd == -1)
				continue;
			WDEBUG(format("register %d with %d") % it->pe_fd % it->pe_events);
			pfd.fd = it->pe_fd;
			pfd.events = it->pe_events;
			pfds.push_back(pfd);
		}

	
		//WDEBUG(format("[%d] thread_run: waiting for event, eq=%p")
		//	% pthread_self() % ev_queue);
		r = poll(&pfds[0], pfds.size(), next_evt_time());
		if (r == -1 && errno != EINTR)
			break;

		if (r > 0) {
			for (int i = 0; i < pfds.size(); ++i) {
				if (pfds[i].revents == 0)
					continue;

				sk = ev_queue->pfds[pfds[i].fd].pe_sk;

				WDEBUG(format("[%d] thread_run: got event on %s")
					% pthread_self() % sk->_desc);
				if (pfds[i].revents & POLLIN) {
				function<void (net::socket *, int)> tmph;
					ev_queue->pfds[pfds[i].fd].pe_events &= ~POLLIN;
					tmph.swap(sk->_read_handler);
					tmph(sk, false);
				}

				if (pfds[i].revents & POLLOUT) {
				function<void (net::socket *, int)> tmph;
					ev_queue->pfds[pfds[i].fd].pe_events &= ~POLLOUT;
					swap(tmph, sk->_write_handler);
					tmph(sk, false);
				}

				if (--r == 0)
					break;
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

#endif	/* !LINUX_IO && !SOLARIS_IO */
