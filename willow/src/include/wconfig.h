/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wconfig: configuration.
 */

#ifndef WCONFIG_H
#define WCONFIG_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include <sys/types.h>
#include <netinet/in.h>

struct listener {
	char		*name;
	char		*host;
	int 		 port;
struct	sockaddr_in	addr;
};
extern int nlisteners;
extern struct listener **listeners;

struct cachedir {
	char	*dir;
	size_t	 maxsize;
};

extern struct configuration {
	int		 foreground;
const	char		*access_log;
struct	cachedir	*caches;
	int		 ncaches;
	time_t		 cache_expevery;
	int		 cache_expthresh;
	char		*suid, *sgid;
} config;

void wconfig_init(const char *);

int add_listener(const char *, int);
int add_cachedir(const char *, int);

#endif
