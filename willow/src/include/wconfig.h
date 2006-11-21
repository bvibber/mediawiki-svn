/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wconfig: configuration.
 */

#ifndef WCONFIG_H
#define WCONFIG_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <sys/types.h>
#include <netinet/in.h>

#include <string>
#include <vector>
#include <utility>
#include <map>
using std::pair;
using std::vector;
using std::map;

#include "wnet.h"
using namespace wnet;

struct listener {
	string			 name;
	string			 host;
	int			 port;
	int			 group;
	wnet::socket		*sock;
	atomic<uint64_t>	 nconns;

	~listener() {
		delete sock;
	}
};
extern vector<listener *> listeners;
extern map<wsocket *, listener *> sock2lsn;

struct cachedir {
	cachedir(string const &dir_)
	: dir(dir_)
	{}
	
	string	dir;
};

#define DEFAULT_STATS_INTERVAL	300
#define DEFAULT_STATS_PORT	"4446"
#define DEFAULT_HTCP_PORT	"4827"

struct radix;
extern struct configuration : noncopyable {
	string		 admin;
	int		 foreground;
	int		 nthreads;
	int		 log_sample;
	string		 access_log;
	bool		 udp_log;
	string		 udplog_host;
	int		 udplog_port;
	string		 suid, sgid;
	bool		 compress;
	int		 complevel;
	time_t		 backend_retry;
	bool		 cache_private;
	bool		 msie_hack;
	string		 default_host;
	access_list	 access;
	access_list	 force_backend;
	bool		 udp_stats;
	bool		 backend_keepalive;
	bool		 client_keepalive;
	int		 keepalive_max;
	bool		 x_follow;
	int		 max_redirects;
	bool		 use_dio;
	long		 cache_memory;
	long		 max_entity_size;
	string		 cache_master;
	bool		 htcp_sigrequired;

	vector<cachedir>			 cachedirs;
	vector<pair<string, string> >		 stats_hosts;
	vector<pair<string, string> >		 htcp_hosts;
	vector<pair<string, string> >		 htcp_maddrs;
	map<string, ustring>			 htcp_keys;
} config;

void wconfig_init(char const *);

int add_listener(string const &, int);
int add_cachedir(string const &, int);

#endif
