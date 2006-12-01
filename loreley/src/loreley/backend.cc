/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* backend: HTTP backend handling.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include <sys/types.h>
#include <sys/socket.h>

#include <arpa/inet.h>

#include <cstdlib>
#include <cstdio>
#include <cstring>
#include <cerrno>
#include <climits>
#include <cmath>
#include <ctime>
#include <algorithm>
using std::sort;
using std::pow;
using std::rotate;

#include "loreley.h"
#include "backend.h"
#include "net.h"
#include "log.h"
#include "confparse.h"
#include "config.h"
#include "format.h"

map<imstring, int> host_to_bpool;
map<int, backend_pool> bpools;
map<string, int> poolnames;
int nbpools = 1;

struct backend_cb_data : freelist_allocator<backend_cb_data> {
struct	backend		*bc_backend;
	polycallback<backend *, wsocket *> bc_func;
	void		*bc_data;
};

backend::backend(
	string const &name,
	address const &addr)

	: be_name(name)
	, be_straddr(addr.straddr())
	, be_addr(addr)
	, be_dead(false)
	, be_hash(_carp_hosthash(be_straddr))
	, be_load(1.)
{
	WDEBUG(format("adding backend with straddr [%s], hash %s")
		% be_straddr % be_hash);
}

backend_pool::backend_pool(string const &name_, lb_type lbt, int failgroup)
	: _lbtype(lbt)
	, _name(name_)
	, _failgroup(failgroup)
{
	WDEBUG(format("creating backend_pool, lbt=%d") % (int) lbt);
}

void
backend_pool::add(string const &addr, int port, int family)
{
addrlist	*list;
	try {
		list = addrlist::resolve(addr, port, st_stream, family);
	} catch (resolution_error &e) {
		wlog.error(format("resolving %s: %s") % addr % e.what());
		return;
	}

addrlist::iterator	it = list->begin(), end = list->end();

	for (; it != end; ++it) {
		backends.push_back(new backend(addr, *it));
		wlog.notice(format("backend server: %s%s") 
		     % addr % it->straddr());
	}

	delete list;
	_carp_calc();
}

int
backend_list::_get_impl(polycallback<backend *, wsocket *> cb)
{
struct	backend_cb_data	*cbd;
	wsocket		*s = NULL;
static	time_t		 last_nfile;
	time_t		 now = time(NULL);

	/*
	 * If we're delegating (for failover), pass this request off.
	 */
	if (_delegate)
		return _delegate->_get_impl(cb);

	cbd = new backend_cb_data;
	cbd->bc_func = cb;
	
	for (;;) {
		cbd->bc_backend = _next_backend();

		if (cbd->bc_backend == NULL) {
			/*
			 * All out of backends.  See if we have a failover
			 * group to try.
			 */
			delete cbd;
			if (_failgroup != -1) {
				_delegate = bpools.find(_failgroup)->second.get_list("", "");
				return _delegate->_get_impl(cb);
			}
			return -1;
		}

		try {
			s = cbd->bc_backend->be_addr.makesocket(
				"backend connection", prio_backend);
			s->nonblocking(true);
		} catch (socket_error &e) {
			if (e.err() != ENFILE || now - last_nfile > 60) 
				wlog.warn(format("opening backend socket: %s")
					% e.what());
			if (e.err() == ENFILE)
				last_nfile = now;
			delete cbd;
			delete s;
			return -1;
		}

	connect_status	cs;
		try {
			cs = s->connect();
		} catch (socket_error &e) {
			time_t retry = time(NULL) + config.backend_retry;
			wlog.warn(format("%s: %s; retry in %d seconds")
				% cbd->bc_backend->be_name 
				% e.what() % config.backend_retry);
			cbd->bc_backend->be_dead = 1;
			cbd->bc_backend->be_time = retry;
			delete s;
			continue;
		}

		if (cs == connect_later) {
			s->writeback(bind(&backend_list::_backend_read,
					  this, _1, _2, cbd),
				     config.backend_timeo * 1000);
		} else {
			cb(cbd->bc_backend, s);
			delete cbd;
		}
		return 0;
	}
}

void
backend_list::_backend_read(wsocket *s, int flags, backend_cb_data *cbd)
{
int		 error = s->error();

	if (flags & EV_TIMEOUT)
		error = ETIMEDOUT;

	if (error && error != EINPROGRESS) {
		time_t retry = time(NULL) + config.backend_retry;
		wlog.warn(format("%s: %s; retry in %d seconds")
			% cbd->bc_backend->be_name
			% strerror(error)
			% config.backend_retry);
		cbd->bc_backend->be_dead = 1;
		cbd->bc_backend->be_time = retry;
		delete s;
		if (_get_impl(cbd->bc_func) == -1) {
			cbd->bc_func(NULL, NULL);
		}
		delete cbd;
		return;
	}

	cbd->bc_func(cbd->bc_backend, s);
	delete cbd;
}

backend_list::backend_list(
	backend_pool const &bp, 
	imstring const &url,
	imstring const &host, 
	int failgroup,
	lb_type lbt,
	int cur)

	: backends(bp.backends)
	, _cur(0)
	, _failgroup(failgroup)
	, _delegate(NULL)
{
	WDEBUG(format("lbt = %d") % (int)lbt);
	rotate(backends.begin(), backends.begin() + cur, backends.end());
	if (lbt == lb_carp || lbt == lb_carp_hostonly)
		_carp_recalc(url, host, lbt);
}

backend_pool::~backend_pool(void)
{
	for (size_t i = 0; i < backends.size(); ++i)
		delete backends[i];
}

backend_list *
backend_pool::get_list(imstring const &url, imstring const &host)
{
	if (_cur == 0)
		_cur = new size_t();
	if (*_cur >= backends.size())
		*_cur = 0;

	return new backend_list(*this, url, host, _failgroup, _lbtype, (*_cur)++);
}

struct backend *
backend_list::_next_backend(void)
{
size_t			tried = 0;

	while (tried++ <= backends.size()) {
		time_t now = time(NULL);

		if (_cur >= backends.size())
			_cur = 0;

		if (backends[_cur]->be_dead && now >= backends[_cur]->be_time)
			backends[_cur]->be_dead = 0;

		if (backends[_cur]->be_dead) {
			_cur++;
			continue;
		}

		return backends[_cur++];
	}

	return NULL;
}

void
backend_pool::_carp_calc(void)
{
struct	backend *be, *prev;
	size_t	 i, j;

	backends[0]->be_carp = (uint32_t) pow((double) (backends.size() * backends[0]->be_load), 1.0 / backends.size());
	backends[0]->be_carplfm = 1.0;
	for (i = 1; i < backends.size(); ++i) {
		float l = 0;
		be = backends[i];
		prev = backends[i - 1];
		be->be_carplfm = 1.0 + ((backends.size()-i+1) * (be->be_load - prev->be_load));
		for (j = 0; j < i; ++j)
			l *= backends[j]->be_carp;
		be->be_carp = (uint32_t) (be->be_carp / l);
		be->be_carp += (uint32_t) pow(prev->be_carp, (double) backends.size()-i+1);
		be->be_carp = (uint32_t) pow(be->be_carp, (double) 1/(backends.size()-i+1));
	}
}

int
backend_pool::size(void) const
{
	return backends.size();
}

string const &
backend_pool::name(void) const
{
	return _name;
}

void
backend_pool::add_keptalive(pair<wsocket *, backend *>s)
{
	if (!config.backend_keepalive)
		return;

	if (!_keptalive)
		_keptalive = new vector<pair<wsocket *, backend *> >;
	else while (config.keepalive_max && (_keptalive->size() >= (size_t)config.keepalive_max)) {
		delete _keptalive->begin()->first;
		_keptalive->erase(_keptalive->begin());
	}

	_keptalive->push_back(s);
}

pair<wsocket *, backend *>
backend_pool::get_keptalive(void)
{
	if (!config.backend_keepalive)
		return pair<wsocket *, backend *>(0, 0);

	if (!_keptalive)
		_keptalive = new vector<pair<wsocket *, backend *> >;
	if (_keptalive->empty())
		return pair<wsocket *, backend *>(0, 0);
pair<wsocket *, backend *> ret = *_keptalive->rbegin();
	_keptalive->pop_back();
	return ret;
}

void
backend_list::_carp_recalc(imstring const &url, imstring const &host, lb_type lbtype)
{
	uint32_t	hash = 0;
	size_t		i;
	for (i = 0; i < backends.size(); ++i) {
	imstring	s = url;
		if (lbtype == lb_carp_hostonly)
			s = host;
		hash = _carp_urlhash(s) ^ backends[i]->be_hash;			
		hash += hash * 0x62531965;
		hash = rotl(hash, 21);
		hash *= (uint32_t) backends[i]->be_carplfm;
		backends[i]->be_carp = hash;
		WDEBUG(format("host for CARP: [%s] -> %d, be hash %d") 
			% s % hash % backends[i]->be_hash);
	}
	sort(backends.begin(), backends.end(), _becarp_cmp);
}

int
backend_list::_becarp_cmp(backend const *a, backend const *b)
{
	return a->be_carp < b->be_carp ? true : false;
}
