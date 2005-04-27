/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * willow: General utility functions.
 */

#ifndef WILLOW_H
#define WILLOW_H

#include "config.h"

#ifdef WDEBUG_ALLOC
void *internal_wmalloc(size_t, const char *, int);
void internal_wfree(void *, const char *, int);
char *internal_wstrdup(const char *, const char *, int);
void *internal_wrealloc(void *, size_t, const char *, int);
# define wmalloc(s) internal_wmalloc(s, __FILE__, __LINE__)
# define wfree(p) internal_wfree(p, __FILE__, __LINE__)
# define wstrdup(p) internal_wstrdup(p, __FILE__, __LINE__)
# define wrealloc(p,s) internal_wrealloc(p, s, __FILE__, __LINE__)
#else
# define wmalloc malloc
# define wfree free
# define wstrdup strdup
# define wrealloc realloc
#endif

#ifndef HAVE_DAEMON
int daemon(int, int);
#endif

#endif
