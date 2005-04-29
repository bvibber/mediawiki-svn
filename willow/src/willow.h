/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * willow: General utility functions.
 */

#ifndef WILLOW_H
#define WILLOW_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include "config.h"

#ifdef WDEBUG_ALLOC
void *internal_wmalloc(size_t, const char *, int);
void internal_wfree(void *, const char *, int);
char *internal_wstrdup(const char *, const char *, int);
void *internal_wrealloc(void *, size_t, const char *, int);
void *internal_wcalloc(size_t, size_t, const char *, int);
# define wmalloc(s) internal_wmalloc(s, __FILE__, __LINE__)
# define wfree(p) internal_wfree(p, __FILE__, __LINE__)
# define wstrdup(p) internal_wstrdup(p, __FILE__, __LINE__)
# define wrealloc(p,s) internal_wrealloc(p, s, __FILE__, __LINE__)
# define wcalloc(n,s) internal_wcalloc(n, s, __FILE__, __LINE))
#else
# include <stdlib.h>

# define wmalloc malloc
# define wfree free
# define wstrdup strdup
# define wrealloc realloc
# define wcalloc calloc
#endif

void realloc_strcat(char **, const char *);
void realloc_addchar(char **, int);

#ifndef HAVE_DAEMON
int daemon(int, int);
#endif

#ifndef HAVE_SOCKLEN_T
typedef int socklen_t;
#endif

void outofmemory(void);
#ifdef __SUNPRO_C
# pragma does_not_return(outofmemory)
#endif

#define safe_snprintf(n,a) if (snprintf a > (n - 1)) abort();

#endif
