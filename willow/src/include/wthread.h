/* @(#) $Id: willow.h 17571 2006-11-12 15:44:30Z river $ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wthread: threading primitives.
 */

#ifndef WTHREAD_H
#define WTHREAD_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <pthread.h>
#include "util.h"

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
	mutable pthread_key_t	_key;
	tss() {
		pthread_key_create(&_key, NULL);
	}
	T const& operator* (void) const {
		return *(T *)pthread_getspecific(_key);
	}
	T& operator* (void) {
		return *(T *)pthread_getspecific(_key);
	}
	T const * operator-> (void) const {
		return (T *)pthread_getspecific(_key);
	}
	T *operator-> (void) {
		return (T *)pthread_getspecific(_key);
	}
	tss &operator= (T* n) {
		pthread_setspecific(_key, n);
		return *this;
	}
	operator T* (void) {
		return (T *)pthread_getspecific(_key);
	}
};

#endif
