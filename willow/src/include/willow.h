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

#include <sstream>

#ifdef __INTEL_COMPILER
# pragma warning (disable: 869 981 304 383 1418 1469 810)
#endif

template<typename To, typename From>
To lexical_cast(From const &f)
{
std::stringstream	strm;
To			t;
	strm << f;
	strm >> t;
	return t;
}

typedef unsigned long long w_size_t;

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
# define wcalloc(n,s) internal_wcalloc(n, s, __FILE__, __LINE__)
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

char **wstrvec(const char *, const char *, int);
void wstrvecfree(char **);

#ifndef HAVE_DAEMON
int daemon(int, int);
#endif

#ifndef HAVE_SOCKLEN_T
typedef int socklen_t;
#endif

#ifndef HAVE_STRLCAT
extern "C" size_t strlcat(char *dst, const char *src, size_t siz);
#endif
#ifndef HAVE_STRLCPY
extern "C" size_t strlcpy(char *dst, const char *src, size_t siz);
#endif

void outofmemory(void);
#ifdef __SUNPRO_C
# pragma does_not_return(outofmemory)
#endif

#define CHAR_HOST	1

extern int char_table[];

#if defined(__GNUC__) || defined(__INTEL_COMPILER)
# define likely(c) __builtin_expect((c), 1)
# define unlikely(c) __buildin_expect((c), 0)
#else
# define likely(c) c
# define unlikely(c) c
#endif

#endif
