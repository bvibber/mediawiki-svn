/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wbackend: HTTP backend handling.
 */

#ifndef WBACKEND_H
#define WBACKEND_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <sys/types.h>
#include <netinet/in.h>

#include <string>
#include <vector>
#include <map>
using std::string;
using std::vector;
using std::map;

#include "polycaller.h"
#include "wnet.h"
using namespace wnet;

struct backend {
		backend(string const &, string const &, address const &);

	string		 be_name;	/* IP as specified in config	*/
	int		 be_group;	/* group			*/
	int	 	 be_port;	/* port number			*/
	string		 be_straddr;	/* formatted address		*/
	address		 be_addr;	/* address			*/
	int	 	 be_dead;	/* 0 if okay, 1 if unavailable	*/
	time_t		 be_time;	/* If dead, time to retry	*/
	uint32_t	 be_hash;	/* constant carp "host" hash	*/
	uint32_t	 be_carp;	/* carp hash for the last url	*/
	float		 be_load;	/* carp load factor		*/
	float		 be_carplfm;	/* carp LFM after calculation	*/

	static uint32_t 	 _carp_hosthash	(string const &);
};

struct backend_cb_data;
struct backend_pool {
	backend_pool();

	void	add	(string const &, int, int);

	template<typename T>
	int	get	(string const &url, polycaller<backend *, wsocket *, T> cb, T t) {
		return _get_impl(url, polycallback<backend *, wsocket *>(cb, t));
	}

	vector<backend *> backends;

	int		 _get_impl	(string const &, polycallback<backend *, wsocket *>);
	void		 _backend_read	(wsocket *e, backend_cb_data *);
	struct backend 	*_next_backend	(string const &url);
	void		 _carp_recalc	(string const &url);
	void		 _carp_calc	(void);

	static int	 _becarp_cmp	(backend const *a, backend const *b);
	static uint32_t  _carp_urlhash	(string const &);

	tss<int>	 _cur;
};

extern map<int, backend_pool> bpools;
extern map<string, int> poolnames;
extern int nbpools;

#endif
