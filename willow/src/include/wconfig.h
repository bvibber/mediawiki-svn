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
using std::string;
using std::vector;
using std::map;

struct listener {
	string			 name;
	string			 host;
	int	 		 port;
	sockaddr_storage	 addr;
	int			 group;
};
extern vector<listener *> listeners;
extern map<int, int> lsn2group;

struct cachedir {
	char	*dir;
	size_t	 maxsize;
};

#define DEFAULT_STATS_INTERVAL	300
#define DEFAULT_STATS_PORT	"4446"

struct radix;
extern struct configuration {
	string		 admin;
	int		 foreground;
	int		 nthreads;
	string		 access_log;
	bool		 udp_log;
	int		 udplog_sample;
	string		 udplog_host;
	int		 udplog_port;
struct	cachedir	*caches;
	int		 ncaches;
	time_t		 cache_expevery;
	int		 cache_expthresh;
	string		 suid, sgid;
	bool		 compress;
	int		 complevel;
	time_t		 backend_retry;
	bool		 cache_private;
	bool		 use_carp;
	bool		 msie_hack;
	enum {
		carp_hash_carp = 0,
		carp_hash_simple
	}		 carp_hash;
	access_list	 access;
	bool		 udp_stats;
	vector<pair<string, string> >		 stats_hosts;
} config;

void wconfig_init(char const *);

int add_listener(string const &, int);
int add_cachedir(string const &, int);

#endif
