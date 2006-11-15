/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp: HTTP implementation.
 */

#ifndef WHTTP_H
#define WHTTP_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

static const int whttp_deny_connect = 0x1;

struct fde;
struct event_base;

void http_new(struct fde *);
void whttp_init(void);
void whttp_shutdown(void);
void whttp_reconfigure(void);

extern const char *request_string[];
extern char my_hostname[];
extern char *cache_miss_hdr;
extern char *cache_hit_hdr;
extern char via_hdr[];

enum http_version {
	http10,
	http11
};

#endif
