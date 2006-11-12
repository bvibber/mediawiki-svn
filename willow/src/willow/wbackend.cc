/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wbackend: HTTP backend handling.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

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

#include "willow.h"
#include "wbackend.h"
#include "wnet.h"
#include "wlog.h"
#include "confparse.h"
#include "wconfig.h"
#include "format.h"

#define rotl(i,r) (((i) << (r)) | ((i) >> (sizeof(i)*CHAR_BIT-(r))))

map<int, backend_pool> bpools;
map<string, int> poolnames;
int nbpools;

struct backend_cb_data : freelist_allocator<backend_cb_data> {
struct	backend		*bc_backend;
	polycallback<backend *, wsocket *> bc_func;
	void		*bc_data;
	string		 bc_url;
};

backend::backend(
	string const &name,
	string const &straddr,
	address const &addr)

	: be_name(name)
	, be_straddr(addr.straddr())
	, be_addr(addr)
	, be_dead(false)
	, be_hash(_carp_hosthash(be_straddr))
	, be_load(1.)
{
	WDEBUG((WLOG_DEBUG, format("adding backend with straddr [%s], hash %s")
		% be_straddr % be_hash));
}

backend_pool::backend_pool(lb_type lbt)
	: _lbtype(lbt)
{
}

void
backend_pool::add(string const &addr, int port, int family)
{
addrlist	*list;
	try {
		list = addrlist::resolve(addr, port, st_stream, family);
	} catch (resolution_error &e) {
		wlog(WLOG_ERROR, format("resolving %s: %s")
			% addr % e.what());
		return;
	}

addrlist::iterator	it = list->begin(), end = list->end();

	for (; it != end; ++it) {
		backends.push_back(new backend(addr, it->straddr(), *it));
		wlog(WLOG_NOTICE, format("backend server: %s[%s]:%d") 
		     % addr % it->straddr() % port);
	}

	_carp_calc();
}

int
backend_pool::_get_impl(string const &url, polycallback<backend *, wsocket *> cb)
{
struct	backend_cb_data	*cbd;
	wsocket		*s = NULL;
static	time_t		 last_nfile;
	time_t		 now = time(NULL);

	cbd = new backend_cb_data;
	cbd->bc_func = cb;
	cbd->bc_url = url;
	
	for (;;) {
		cbd->bc_backend = _next_backend(url);

		if (cbd->bc_backend == NULL) {
			delete cbd;
			return -1;
		}

		try {
			s = cbd->bc_backend->be_addr.makesocket(
				"backend connection", prio_backend);
			s->nonblocking(true);
		} catch (socket_error &e) {
			if (e.err() != ENFILE || now - last_nfile > 60) 
				wlog(WLOG_WARNING,
					format("opening backend socket: %s")
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
			wlog(WLOG_WARNING, format("%s: %s; retry in %d seconds")
				% cbd->bc_backend->be_name 
				% e.what() % config.backend_retry);
			cbd->bc_backend->be_dead = 1;
			cbd->bc_backend->be_time = retry;
			delete s;
			continue;
		}

		if (cs == connect_later) {
			s->writeback(
				polycaller<wsocket *, backend_cb_data*>(*this, 
					&backend_pool::_backend_read), cbd);
		} else {
			cb(cbd->bc_backend, s);
			delete cbd;
		}
		return 0;
	}
}

void
backend_pool::_backend_read(wsocket *s, backend_cb_data *cbd)
{
int		 error = s->error();

	if (error && error != EINPROGRESS) {
		time_t retry = time(NULL) + config.backend_retry;
		wlog(WLOG_WARNING, format("%s: %s; retry in %d seconds")
			% cbd->bc_backend->be_name
			% strerror(error)
			% config.backend_retry);
		cbd->bc_backend->be_dead = 1;
		cbd->bc_backend->be_time = retry;
		delete s;
		if (_get_impl(cbd->bc_url, cbd->bc_func) == -1) {
			cbd->bc_func(NULL, NULL);
		}
		delete cbd;
		return;
	}

	cbd->bc_func(cbd->bc_backend, s);
	delete cbd;
}

struct backend *
backend_pool::_next_backend(string const &url)
{
size_t			tried = 0;

	if (!_cur)
		_cur = new int();

	if (_lbtype == lb_carp || _lbtype == lb_carp_hostonly) {
		_carp_recalc(url);
		*_cur = 0;
	}

	while (tried++ <= backends.size()) {
		time_t now = time(NULL);

		WDEBUG((WLOG_DEBUG, format("_next_backend: considering %d %s")
			% *_cur % backends[*_cur]->be_name));

		if (*_cur >= (int) backends.size())
			*_cur = 0;

		if (backends[*_cur]->be_dead && now >= backends[*_cur]->be_time)
			backends[*_cur]->be_dead = 0;

		if (backends[*_cur]->be_dead) {
			(*_cur)++;
			continue;
		}

		if (_lbtype == lb_carp || _lbtype == lb_carp_hostonly)
			*_cur = 0;
		return backends[(*_cur)++];
	}

	return NULL;
}

uint32_t
backend_pool::_carp_urlhash(string const &str)
{
	uint32_t h = 0;
	for (string::const_iterator it = str.begin(), end = str.end(); it != end; ++it)
		h += rotl(h, 19) + *it;
	return h;
}

uint32_t
backend::_carp_hosthash(string const &str)
{
	uint32_t h = backend_pool::_carp_urlhash(str) * 0x62531965;
	return rotl(h, 21);
}

void
backend_pool::_carp_calc(void)
{
struct	backend *be, *prev;
	size_t	 i, j;

	backends[0]->be_carp = (uint32_t) pow((backends.size() * backends[0]->be_load), 1.0 / backends.size());
	backends[0]->be_carplfm = 1.0;
	for (i = 1; i < backends.size(); ++i) {
		float l = 0;
		be = backends[i];
		prev = backends[i - 1];
		be->be_carplfm = 1.0 + ((backends.size()-i+1) * (be->be_load - prev->be_load));
		for (j = 0; j < i; ++j)
			l *= backends[j]->be_carp;
		be->be_carp = (uint32_t) (be->be_carp / l);
		be->be_carp += (uint32_t) pow(prev->be_carp, backends.size()-i+1);
		be->be_carp = (uint32_t) pow(be->be_carp, 1/(backends.size()-i+1));
	}
}

void
backend_pool::_carp_recalc(string const &url)
{
	uint32_t	hash = 0;
	size_t		i;
	for (i = 0; i < backends.size(); ++i) {
	string	s = url;
		if (_lbtype == lb_carp_hostonly && url.size() > 6) {
		string::size_type	i;
			if ((i = url.find('/', 7)) != string::npos)
				s = url.substr(7, i - 7);
		}
		hash = _carp_urlhash(s) ^ backends[i]->be_hash;			
		hash += hash * 0x62531965;
		hash = rotl(hash, 21);
		hash *= (uint32_t) backends[i]->be_carplfm;
		backends[i]->be_carp = hash;
		WDEBUG((WLOG_DEBUG, format("host for CARP: [%s] -> %d, be hash %d") % s % hash % backends[i]->be_hash));
	}
	sort(backends.begin(), backends.end(), _becarp_cmp);
}

int
backend_pool::_becarp_cmp(backend const *a, backend const *b)
{
	return a->be_carp < b->be_carp ? true : false;
}
