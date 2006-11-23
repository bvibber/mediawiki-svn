/* Willow: Lightweight HTTP reverse-proxy.                              */
/* http: HTTP implementation.						*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef WHTTP_H
#define WHTTP_H

static const int whttp_deny_connect = 0x1;

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
