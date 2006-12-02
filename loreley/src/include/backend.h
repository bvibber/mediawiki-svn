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

#ifndef WBACKEND_H
#define WBACKEND_H

#include <sys/types.h>
#include <netinet/in.h>

#include <string>
#include <vector>
#include <map>
using std::vector;
using std::map;

#include "net.h"
using namespace net;

enum lb_type {
	lb_rr,
	lb_carp,
	lb_carp_hostonly
};

struct backend_list;
struct backend_pool;
struct backend_cb_data;

struct backend : freelist_allocator<backend> {
		backend(string const &, address const &);

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

	template<typename stringT>
	static uint32_t _carp_hosthash(stringT const &str);
};

struct backend_list : freelist_allocator<backend_list> {
	backend_list(	backend_pool const &pool,
			imstring const &url,
			imstring const &host,
			int failgroup,
			lb_type, int start);

	int		 _get_impl	(function<void (backend *, wsocket *)>);
	void		 _backend_read	(wsocket *e, int, backend_cb_data *);
	struct backend 	*_next_backend	(void);
	void		 _carp_recalc	(imstring const &, imstring const &, lb_type);
	static int	 _becarp_cmp	(backend const *a, backend const *b);
	bool		 failed		(void) const {
		return _delegate;
	}

	template<typename stringT>
	static uint32_t _carp_urlhash(stringT const &str) {
		uint32_t h = 0;
		for (typename stringT::const_iterator it = str.begin(), end = str.end(); 
		     it != end; ++it)
			h += rotl(h, 19) + *it;
		return h;
	}


	template<typename T>
	int	get	(T cb) {
		return _get_impl(cb);
	}

	~backend_list() {
		if (_delegate)
			delete _delegate;
	}

private:
	vector<backend *, pt_allocator<backend *> > backends;
	size_t	_cur;
	int	_failgroup;
	struct backend_list *_delegate;
};

template<typename stringT>
uint32_t
backend::_carp_hosthash(stringT const &str)
{
	uint32_t h = backend_list::_carp_urlhash(str) * 0x62531965;
	return rotl(h, 21);
}

struct backend_pool {
	backend_pool(string const &name, lb_type, int failgroup = -1);
	~backend_pool();

	void		 add		(string const &, int, int);
	backend_list	*get_list	(imstring const & url, imstring const &host);

	int		 size		(void) const;
	string const	&name		(void) const;

	void		 add_keptalive	(pair<wsocket *, backend *>);
	pair<wsocket *, backend *>
			 get_keptalive	(void);

private:
	friend class backend_list;

	void		 _carp_calc	(void);

	vector<backend *, pt_allocator<backend *> > backends;
	tss<vector<pair<wsocket *, backend *> > > _keptalive;
	tss<size_t>	 _cur;
	lb_type		 _lbtype;
	string		 _name;
	int		 _failgroup;
};

extern map<int, backend_pool> bpools;
extern map<imstring, int> host_to_bpool;
extern map<string, int> poolnames;
extern int nbpools;

#endif
