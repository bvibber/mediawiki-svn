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
#include <pthread.h>

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

struct noncopyable {
	noncopyable() {};
private:
	noncopyable(noncopyable const &);	/* no implementation */
};

#define HOLDING(l) locker _l(l)

struct lockable : noncopyable {
	mutable pthread_mutex_t	m;
	lockable() {
		pthread_mutex_init(&m, NULL);
	}
	void _lock() const {
		pthread_mutex_lock(&m);
	}
	void _unlock() const {
		pthread_mutex_unlock(&m);
	}
};

struct locker : noncopyable {
	lockable const	&m;
	locker(lockable const &m_)
		: m(m_) {
		m._lock();
	}
	~locker() {
		m._unlock();
	}
};

template<typename T>
struct atomic {
	T 		v;
	lockable	m;

	atomic () : v(T()) {}
	atomic (T v_) : v(v_) {}
	atomic (atomic const& o) {
		HOLDING(o.m);
		v = o.v;
	}
	template<typename U> atomic (atomic<U> const &o) {
		HOLDING(o.m);
		v = o.v;
	}

	operator T (void) const {
		HOLDING(m);
		return v;
	}
	template<typename U> atomic &operator = (U o) {
		HOLDING(m);
		v = o;
		return *this;
	}
	template<typename U> atomic &operator += (U o) {
		HOLDING(m);
		v += o;
		return *this;
	}
	template<typename U> atomic &operator -= (U o) {
		HOLDING(m);
		v -= o;
		return *this;
	}
	template<typename U> atomic &operator *= (U o) {
		HOLDING(m);
		v *= o;
		return *this;
	}
	template<typename U> atomic &operator /= (U o) {
		HOLDING(m);
		v /= o;
		return *this;
	}
	template<typename U> atomic &operator %= (U o) {
		HOLDING(m);
		v %= o;
		return *this;
	}
	template<typename U> atomic &operator <<= (U o) {
		HOLDING(m);
		v <<= o;
		return *this;
	}
	template<typename U> atomic &operator >>= (U o) {
		HOLDING(m);
		v >>= o;
		return *this;
	}
	template<typename U> atomic &operator &= (U o) {
		HOLDING(m);
		v &= o;
		return *this;
	}
	template<typename U> atomic &operator |= (U o) {
		HOLDING(m);
		v |= o;
		return *this;
	}
	template<typename U> atomic &operator ^= (U o) {
		HOLDING(m);
		v ^= o;
		return *this;
	}
	atomic &operator++ (void) {
		HOLDING(m);
		v++;
		return *this;
	}
	T operator++ (int) {
	atomic	u (*this);
		u.v++;
		return u;
	}
};

template<typename T1, typename T2>
T1 operator + (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v + v2;
}
template<typename T1, typename T2>
T1 operator - (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v - v2;
}
template<typename T1, typename T2>
T1 operator * (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v * v2;
}
template<typename T1, typename T2>
T1 operator / (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v / v2;
}
template<typename T1, typename T2>
T1 operator ^ (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v ^ v2;
}
template<typename T1, typename T2>
T1 operator & (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v & v2;
}
template<typename T1, typename T2>
T1 operator | (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v | v2;
}
template<typename T1, typename T2>
T1 operator && (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v && v2;
}
template<typename T1, typename T2>
T1 operator || (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v || v2;
}
template<typename T1, typename T2>
T1 operator == (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v == v2;
}
template<typename T1, typename T2>
T1 operator != (atomic<T1> const &v1, T2 v2) {
	return !(v1 == v2);
}
template<typename T1, typename T2>
T1 operator < (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v < v2;
}
template<typename T1, typename T2>
T1 operator > (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v > v2;
}
template<typename T1, typename T2>
T1 operator <= (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v <= v2;
}
template<typename T1, typename T2>
T1 operator >= (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v >= v2;
}
template<typename T1, typename T2>
T1 operator << (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v << v2;
}
template<typename T1, typename T2>
T1 operator >> (atomic<T1> const &v1, T2 v2) {
	HOLDING(v1.m);
	return v1.v >> v2;
}
template<typename T1>
T1 operator ! (atomic<T1> const &v1) {
	HOLDING(v1.m);
	return !v1.v;
}
template<typename T1>
T1 operator ~ (atomic<T1> const &v1) {
	HOLDING(v1.m);
	return ~v1.v;
}

template<typename T>
struct tss {
	mutable pthread_key_t	key;
	tss() {
		pthread_key_create(&key, NULL);
	}
	T const& operator* (void) const {
		return *(T *)pthread_getspecific(key);
	}
	T& operator* (void) {
		return *(T *)pthread_getspecific(key);
	}
	T const * operator-> (void) const {
		return (T *)pthread_getspecific(key);
	}
	T *operator-> (void) {
		return (T *)pthread_getspecific(key);
	}
	tss &operator= (T* n) {
		pthread_setspecific(key, n);
		return *this;
	}
	operator T* (void) {
		return (T *)pthread_getspecific(key);
	}
};

template<typename T>
struct freelist_allocator {
	T		*_freelist_next;
static  tss<T>		 _freelist;

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
tss<T> freelist_allocator<T>::_freelist;

struct radix;
extern struct stats_stru : noncopyable {
	atomic<int>	interval;	/* update interval	*/
	radix	*v4_access, *v6_access;
	/*
	 * Absolute values.
	 */
	struct abs_t {
		atomic<uint64_t>	n_httpreq_ok;		/* requests which were sent to a backend		*/
		atomic<uint64_t>	n_httpreq_fail;		/* requests which did not reach a backend		*/
		atomic<uint64_t>	n_httpresp_ok;		/* backend responses with status 200			*/
		atomic<uint64_t>	n_httpresp_fail;	/* backend responses with status other than 200		*/
	} cur, last;
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
