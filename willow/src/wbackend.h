/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wbackend: HTTP backend handling.
 */

#ifndef WBACKEND_H
#define WBACKEND_H

#ifdef __SUNPRO_C
# pragma ident "@(#)$Header$"
#endif

#include <sys/types.h>

#include <netinet/in.h>

struct fde;

struct backend {
	char		*be_name;	/* IP as specified in config	*/
	int	 	 be_port;	/* port number			*/
struct	sockaddr_in	 be_addr;	/* socket address		*/
	int	 	 be_okay;	/* 1 if okay, 0 if unavailable	*/
};

typedef void (*backend_cb)(struct backend *, struct fde *, void *);

void add_backend(char *);
void backend_file(char *);

int get_backend(backend_cb, void *);

#endif
