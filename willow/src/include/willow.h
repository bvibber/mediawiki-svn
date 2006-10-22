/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * willow: General utility functions.
 */

#ifndef WILLOW_H
#define WILLOW_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include "config.h"

#include <sstream>
#include <cstddef>
#include <iostream>

#include "wlog.h"

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

template<typename T>
struct freelist_allocator {
        T       *_freelist_next;
static  T       *_freelist;
 
        void *operator new(std::size_t size) {
                if (_freelist) {
                T       *n = _freelist;
			WDEBUG((WLOG_DEBUG, "allocate %s from freelist @ %p", typeid(T).name(), n));
                        _freelist = _freelist->_freelist_next;
			memset(n, 0, sizeof(*n));
                        return n;
                } else {
		void	*ret;
			ret = new char[size];
			WDEBUG((WLOG_DEBUG, "allocate %s from heap @ %p", typeid(T).name(), ret));
			memset(ret, 0, size);
			return ret;
		}
        }
 
        void operator delete (void *p) {
        T       *o = (T *)p;
		WDEBUG((WLOG_DEBUG, "return %s @ %p to freelist", typeid(T).name(), p));
		memset(o, 0, sizeof(*o));
                o->_freelist_next = _freelist;
                _freelist = o;
        }
};
template<typename T>
T *freelist_allocator<T>::_freelist;

# include <stdlib.h>
# define wmalloc malloc
# define wfree free
# define wstrdup strdup
# define wrealloc realloc
# define wcalloc calloc

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
