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

enum lb_type {
	lb_rr,
	lb_carp,
	lb_carp_hostonly,
};

struct backend_list;
struct backend_pool;
struct backend_cb_data;

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

struct backend_list {
	backend_list(	backend_pool const &pool,
			string const &url,
			string const &host,
			lb_type, int start);

	int		 _get_impl	(polycallback<backend *, wsocket *>);
	void		 _backend_read	(wsocket *e, backend_cb_data *);
	struct backend 	*_next_backend	(void);
	void		 _carp_recalc	(string const &, string const &, lb_type);
	static int	 _becarp_cmp	(backend const *a, backend const *b);
	static uint32_t  _carp_urlhash	(string const &);

	template<typename T>
	int	get	(polycaller<backend *, wsocket *, T> cb, T t) {
		return _get_impl(polycallback<backend *, wsocket *>(cb, t));
	}

private:
	vector<backend *> backends;
	int _cur;
};

struct backend_pool {
	backend_pool(lb_type);

	void		 add		(string const &, int, int);
	backend_list	*get_list	(string const & url, string const &host);

	vector<backend *> backends;

	void		 _carp_calc	(void);

private:
	tss<int>	 _cur;
	lb_type		 _lbtype;
};

extern map<int, backend_pool> bpools;
extern map<string, int> host_to_bpool;
extern map<string, int> poolnames;
extern int nbpools;

#endif
