/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp: HTTP implementation.
 */

#ifndef WHTTP_H
#define WHTTP_H

#ifdef __SUNPRO_C
# pragma ident "@(#)$Header$"
#endif

struct fde;

void http_new(struct fde *);
void whttp_init(void);
void whttp_shutdown(void);

extern const char *request_string[];

extern struct request_type {
	const char *name;
	int len;
	int type;
} supported_reqtypes[];
#endif
