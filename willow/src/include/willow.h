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
#include <typeinfo>
#include <stdexcept>
#include <string>
#include <cmath>
#include <vector>
using std::runtime_error;
using std::basic_string;
using std::char_traits;
using std::vector;

#include "wlog.h"
#include "radix.h"
#include "ptalloc.h"

#ifdef __INTEL_COMPILER
# pragma warning (disable: 869 981 304 383 1418 1469 810)
#endif

typedef unsigned long long w_size_t;

#include <stdlib.h>
#define wmalloc malloc
#define wfree free
#define wstrdup strdup
#define wrealloc realloc
#define wcalloc calloc

	char **wstrvec		(const char *, const char *, int);
	void wstrvecfree	(char **);

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

int str10toint(char const *src, int len);
int str16toint(char const *src, int len);

static inline bool
httpcompare(string const &a, string const &b)
{
	return a.size() == b.size() &&
	       !strncasecmp(a.data(), b.data(), a.size());
}

void outofmemory(void);
#ifdef __SUNPRO_C
# pragma does_not_return(outofmemory)
#endif

#define CHAR_HOST	1

extern int char_table[];

#if defined(__GNUC__) || defined(__INTEL_COMPILER)
# define likely(c) __builtin_expect((c), 1)
# define unlikely(c) __builtin_expect((c), 0)
#else
# define likely(c) c
# define unlikely(c) c
#endif

template<typename T, void (T::*ptmf) (void)>
void ptmf_transform(void *p)
{
T	*o = (T *)p;
	(o->*ptmf)();
}

template<typename T, typename AT1, typename AT2, void (T::*ptmf) (AT1, AT2)>
struct ptmf_transform2 {
	static void call(AT1 a, AT2 b, void *p) {
		(static_cast<T*>(p)->*ptmf)(a, b);
	}
};

extern struct stats_stru : noncopyable {
	atomic<int>	interval;	/* update interval	*/
	access_list	access;

	/*
	 * Absolute values.
	 */
	struct abs_t {
			abs_t() {
				memset(this, 0, sizeof(*this));
			}
		uint64_t	n_httpreq_ok;		/* requests which were sent to a backend		*/
		uint64_t	n_httpreq_fail;		/* requests which did not reach a backend		*/
		uint64_t	n_httpresp_ok;		/* backend responses with status 200			*/
		uint64_t	n_httpresp_fail;	/* backend responses with status other than 200		*/
	} cur, last;
	lockable	cur_lock;
	tss<abs_t>	tcur;

	/*
	 * Averages.
	 */
	atomic<uint32_t>	n_httpreq_oks;		/* httpreq_ok per sec		*/
	atomic<uint32_t>	n_httpreq_fails;	/* httpreq_fail per sec		*/
	atomic<uint32_t>	n_httpresp_oks;		/* httpresp_ok per sec		*/
	atomic<uint32_t>	n_httpresp_fails;	/* httpresp_fail per sec	*/
} stats;

#endif
