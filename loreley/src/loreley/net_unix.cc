/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* net_unix: Unix (other than Solaris)-specific networking.		*/
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

#ifndef SOLARIS_IO

namespace sfun {
	using ::bind;	/* because of conflict with boost::bind from util.h */
};

/*
 * libevent needs these
 */
#ifndef HAVE_U_INT8_T
typedef uint8_t u_int8_t;
#endif

#ifndef HAVE_U_INT16_T
typedef uint16_t u_int16_t;
#endif

#ifndef HAVE_U_INT32_T
typedef uint32_t u_int32_t;
#endif

#ifndef HAVE_U_INT64_T
typedef uint64_t u_int64_t;
#endif

using std::deque;
using std::signal;
using std::multimap;

#include <event.h>

#include "loreley.h"
#include "net.h"
#include "config.h"
#include "log.h"
#include "http.h"

/* see ifname_to_address.cc */
int ifname_to_address(int, sockaddr_in *, char const *);
unsigned int if_nametoindex_wrap(const char *);

::event ev_sigint;
::event ev_sigterm;
//tss<event_base> evb;
lockable ev_lock;

enum evtype_t {
	evtype_timer,
	evtype_event
};

struct event_impl {
	void schedule(int64_t);

	int64_t			 ev_when;
	event_queue		*ev_queue;
	function<void (void)>	 ev_func;
	::event			 ev_event;
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

multimap<int, ev_pending> ev_pending_list;

pthread_t io_loop_thread;

struct eq_entry {
	eq_entry (net::socket *s, event_impl *ev, int flags)
		: ee_sock(s)
		, ee_event(ev)
		, ee_flags(flags) {}

	net::socket	*ee_sock;
	event_impl	*ee_event;
	int		 ee_flags;
};

struct event_queue {
	event_queue() {
		pthread_mutex_init(&eq_lock, NULL);
		pthread_cond_init(&eq_cond, NULL);
	}

	void add(net::socket *sock, event_impl *event, int flags) {
		struct lvars {
			lvars(pthread_mutex_t *l) 
				: lock(l) {
				pthread_mutex_lock(lock);
			}
			~lvars() {
				pthread_mutex_unlock(lock);
			}
			pthread_mutex_t *lock;
		} v(&eq_lock);
		WDEBUG("event_queue::add: adding event");
		eq_events.push_back(new eq_entry(sock, event, flags));
		pthread_cond_signal(&eq_cond);
	}

	pthread_mutex_t	eq_lock;
	pthread_cond_t	eq_cond;
	deque<eq_entry *>	eq_events;
};

tss<event_queue> ev_queue;

static void sig_exit(int, short, void *);

pthread_cond_t iot_ready;
pthread_mutex_t iot_ready_m;

pthread_t io_thread;
void
usr2_handler(int, short, void *)
{
	WDEBUG("got USR2");
	event_loopexit(NULL);
}

::event ev_sigusr2;

void *
io_start(void *)
{
	signal_set(&ev_sigusr2, SIGUSR2, usr2_handler, NULL);
	signal_add(&ev_sigusr2, NULL);

	io_loop_thread = pthread_self();
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

	event_init();
	signal(SIGUSR2, SIG_IGN);
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
	wlog.notice(format("net: initialised, using libevent %s (%s)")
		% event_get_version() % event_get_method());
}

namespace net {

void
socket_callback(int fd, short ev, void *d)
{
wsocket	*s = (wsocket *)d;

	HOLDING(ev_lock);
	WDEBUG(format("[%d] _ev_callback: %s%son %d (%s) queue %p")
		% pthread_self()
		% ((ev & EV_READ) ? "read " : "")
		% ((ev & EV_WRITE) ? "write " : "")
		% fd % s->_desc
		% s->_queue);

	s->_queue->add(s, NULL, ev);
}

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

void
timer_callback(int, short, void *d)
{
event_impl	*ev = (event_impl *)d;
	HOLDING(ev_lock);
	ev->ev_queue->add(NULL, ev, 0);
}

void
socket::_register(int what, int64_t to, socket::call_type handler)
{
	_ev_flags = 0;
	_queue = (event_queue *)ev_queue;

	WDEBUG(format("_register: %s%son %d (%s), queue %p")
		% ((what & FDE_READ) ? "read " : "")
		% ((what & FDE_WRITE) ? "write " : "")
		% _s % _desc % _queue);

	HOLDING(ev_lock);

	if (what & FDE_READ) {
		_read_handler = handler;
		_ev_flags |= EV_READ;
	}
	if (what & FDE_WRITE) {
		_write_handler = handler;
		_ev_flags |= EV_WRITE;
	}

	event_set(ev, _s, _ev_flags, socket_callback, this);
	event_priority_set(ev, (int) _prio);

	WDEBUG(format("timeout = %d") % to);

	ev_pending_list.insert(make_pair(_s, ev_pending(ev, to, evtype_event)));
	pthread_kill(io_loop_thread, SIGUSR2);
}

socket::socket(int s, net::address const &a, char const *desc, sprio p)
	: _addr(a)
	, _desc(desc)
	, _prio(p)
	, _queue(0)
{
	ev = new ::event;
	memset(ev, 0, sizeof(*ev));
	_s = s;
}

socket::socket(net::address const &a, char const *desc, sprio p)
	: _addr(a)
	, _desc(desc)
	, _prio(p)
	, _queue(0)
{
	ev = new ::event;
	memset(ev, 0, sizeof(*ev));
	_s = ::socket(_addr.family(), _addr.socktype(), _addr.protocol());
	if (_s == -1)
		throw socket_error();
}

socket::~socket(void)
{
	WDEBUG("closing socket");
	HOLDING(ev_lock);
	multimap<int, ev_pending>::iterator it;
	it = ev_pending_list.find(_s);
	if (it != ev_pending_list.end())
		ev_pending_list.erase(it);

	event_del(ev);
	delete ev;
	close(_s);
}

void
socket::clearbacks(void)
{
	event_del(ev);
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
	io_loop_thread = pthread_self();
	ev_queue = new event_queue;
}

void
sig_exit(int sig, short what, void *d)
{
	wnet_exit = true;
}

void
ioloop_t::run(void)
{
	WDEBUG(format("[%d] ioloop run: running") % pthread_self());
	while (!wnet_exit) {
		event_loop(EVLOOP_ONCE);
		WDEBUG("ioloop thread: got event");

	{

		HOLDING(ev_lock);
	multimap<int, ev_pending>::iterator  it = ev_pending_list.begin(),
						end = ev_pending_list.end();
		for (; it != end; ++it) {
			WDEBUG("ioloop thread: processing new event");
			if (event_pending(it->second.ep_event, EV_READ | EV_WRITE, NULL))
				event_del(it->second.ep_event);

			if (it->second.ep_type == evtype_event) {
				if (it->second.ep_timeout == -1) {
					event_add(it->second.ep_event, NULL);
				} else {
				timeval	tv;
				int64_t	usec = it->second.ep_timeout * 1000;
					tv.tv_sec = usec / 1000000;
					tv.tv_usec = usec % 1000000;
					WDEBUG(format("timeout: %d %d") % tv.tv_sec % tv.tv_usec);
					event_add(it->second.ep_event, &tv);
				}
			} else if (it->second.ep_type == evtype_timer) {
				timeval	tv;
				int64_t	usec = it->second.ep_timeout * 1000;
					tv.tv_sec = usec / 1000000;
					tv.tv_usec = usec % 1000000;
					WDEBUG(format("timeout: %d %d") % tv.tv_sec % tv.tv_usec);
					evtimer_add(it->second.ep_event, &tv);
			} else
				abort();
		}
		ev_pending_list.clear();
	}
	}

size_t	 i;
	for (i = 0; i < listeners.size(); ++i)
		delete listeners[i];
}

void
ioloop_t::thread_run(void)
{
	/*
	 * ioloop for a single thread.  uses a condition variable and a queue.
	 */
event_queue	*eq = (event_queue *)ev_queue;

	for (;;) {
	deque<eq_entry *> evs;
		WDEBUG(format("[%d] thread_run: waiting for event, eq=%p")
			% pthread_self() % eq);
		pthread_mutex_lock(&eq->eq_lock);
		if (eq->eq_events.empty())
			pthread_cond_wait(&eq->eq_cond, &eq->eq_lock);
		WDEBUG(format("[%d] thread_run: got event") % pthread_self());
		evs = eq->eq_events;
		eq->eq_events.clear();
		pthread_mutex_unlock(&eq->eq_lock);
		for (deque<eq_entry *>::iterator
		     it = evs.begin(),
		     end = evs.end(); it != end; ++it) {
			if ((*it)->ee_sock) {
				WDEBUG(format("[%d] thread_run: got event on %s")
					% pthread_self() % (*it)->ee_sock->_desc);
				if ((*it)->ee_flags & EV_READ)
					(*it)->ee_sock->_read_handler((*it)->ee_sock, false);
				if ((*it)->ee_flags & EV_WRITE)
					(*it)->ee_sock->_write_handler((*it)->ee_sock, false);
				if ((*it)->ee_flags & EV_TIMEOUT) {
					if ((*it)->ee_sock->_ev_flags & EV_READ) {
						(*it)->ee_sock->_read_handler((*it)->ee_sock, true);
					} else if ((*it)->ee_sock->_ev_flags & EV_WRITE) {
						(*it)->ee_sock->_write_handler((*it)->ee_sock, true);
					}
				}
			} else {
				WDEBUG("event thread");
				(*it)->ee_event->ev_func();
			}
			delete *it;
		}
	}
}


void
event_impl::schedule(int64_t when)
{
	HOLDING(ev_lock);
	WDEBUG(format("schedule, when=%d") % when);
	this->ev_when = when;
	this->ev_queue = (event_queue *)::ev_queue;
	evtimer_set(&ev_event, timer_callback, this);
	ev_pending_list.insert(make_pair(0, ev_pending(&ev_event, ev_when, evtype_timer)));
	pthread_kill(io_loop_thread, SIGUSR2);
}

#endif	/* !SOLARIS_IO */
