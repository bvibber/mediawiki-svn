/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* wthread: threading primitives.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id: loreley.h 17571 2006-11-12 15:44:30Z river $ */

#ifndef WTHREAD_H
#define WTHREAD_H

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
	atomic &operator++ (void) {	/* prefix */
		HOLDING(m);
		v++;
		return *this;
	}
	T operator++ (int) {	/* postfix */
		HOLDING(m);
	T	u = v;
		v++;
		return v;
	}
	atomic &operator-- (void) {
		HOLDING(m);
		v--;
		return *this;
	}
	T operator-- (int) {
		HOLDING(m);
	T	u = v;
		v--;
		return v;
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

void tss_null_dtor(void *);

template<typename T, void dtor (void *) = tss_null_dtor>
struct tss {
	mutable pthread_key_t	_key;
	tss() {
		pthread_key_create(&_key, dtor);
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
