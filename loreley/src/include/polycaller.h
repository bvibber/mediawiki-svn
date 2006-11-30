/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* polycaller: a polymorphic callback proxy				*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef POLYCALLER_H
#define POLYCALLER_H

#include <stdexcept>
#include <typeinfo>
using std::type_info;

#include "loreley.h"

struct polycaller_type_mismatch : std::logic_error {
        polycaller_type_mismatch() : std::logic_error("polycaller called type does not match stored type") {}
};

struct polycaller_object_missing : std::logic_error {
	polycaller_object_missing() : std::logic_error("polycaller called with no object") {}
};

template<typename arg1, typename arg2, typename arg3>
struct polycaller_impl_base3 {
        virtual ~polycaller_impl_base3(void) {}
	virtual void call(arg1 a, arg2 b, arg3 c) const = 0;
	virtual polycaller_impl_base3 *clone(void) const = 0;
};

template<typename T, typename arg1, typename arg2, typename arg3>
struct polycaller_impl3 : polycaller_impl_base3<arg1,arg2,arg3>,
		freelist_allocator<polycaller_impl3<T,arg1,arg2,arg3> > {
	typedef void (T::*ftype) (arg1, arg2, arg3);
	ftype	 f;
	T	*o;

        polycaller_impl3 (T *o_, ftype f_) : f(f_), o(o_) {}

        void call(arg1 a, arg2 b, arg3 c) const { (o->*f)(a, b, c); }

	polycaller_impl_base3<arg1,arg2,arg3> *clone(void) const {
		return new polycaller_impl3<T,arg1,arg2,arg3>(o,f);
	}
};

template<typename arg1 = void, typename arg2 = void, typename arg3 = void>
struct polycaller {
	type_info const *tid;
	polycaller_impl_base3<arg1, arg2, arg3> *impl;

        polycaller(void) : tid(NULL), impl(NULL) {}
	~polycaller() {
		delete impl;
	}

	template<typename T>
	polycaller(T &o, void (T::*f) (arg1, arg2, arg3)) 
		: tid(NULL)
		, impl(NULL) {
		assign(o, f);
	}

	polycaller& operator= (polycaller<arg1,arg2,arg3> const &other) {
		delete impl;
		impl = NULL;
		tid = other.tid;
		if (other.impl)
			impl = other.impl->clone();
		return *this;
	}

	polycaller(polycaller<arg1,arg2,arg3> const &other)
		: tid(other.tid)
		, impl(NULL) {
		if (other.impl)
			impl = other.impl->clone();
	}

        template<typename T>
        polycaller& assign (T &o, void (T::*f) (arg1, arg2, arg3)) {
                delete impl;
                impl = new polycaller_impl3<T,arg1,arg2,arg3>(&o, f);
                tid = &typeid(T);
                return *this;
        }

        void operator() (arg1 a, arg2 b, arg3 c) const {
                impl->call(a, b, c);
        }
};

template<typename arg1, typename arg2>
struct polycaller_impl_base2 {
        virtual ~polycaller_impl_base2(void) {}
	virtual void call(arg1 a, arg2 b) const = 0;
	virtual polycaller_impl_base2 *clone(void) const = 0;
};

template<typename T, typename arg1, typename arg2>
struct polycaller_impl2 : polycaller_impl_base2<arg1,arg2>,
		freelist_allocator<polycaller_impl2<T,arg1,arg2> > {
	typedef void (T::*ftype) (arg1, arg2);
	ftype	 f;
	T	*o;

        polycaller_impl2 (T *o_, ftype f_) : f(f_), o(o_) {}

        void call(arg1 a, arg2 b) const { (o->*f)(a, b); }

	polycaller_impl_base2<arg1,arg2> *clone(void) const {
		return new polycaller_impl2<T,arg1,arg2>(o,f);
	}
};

template<typename arg1, typename arg2>
struct polycaller<arg1,arg2,void> {
	type_info const *tid;
	polycaller_impl_base2<arg1, arg2> *impl;

        polycaller(void) : tid(NULL), impl(NULL) {}
	~polycaller() {
		delete impl;
	}

	template<typename T>
	polycaller(T &o, void (T::*f) (arg1, arg2)) 
		: tid(NULL)
		, impl(NULL) {
		assign(o, f);
	}

	polycaller& operator= (polycaller<arg1,arg2> const &other) {
		delete impl;
		tid = other.tid;
		impl = NULL;
		if (other.impl)
			impl = other.impl->clone();
		return *this;
	}

	polycaller(polycaller<arg1,arg2> const &other)
		: tid(other.tid)
		, impl(NULL) {
		if (other.impl)
			impl = other.impl->clone();
	}

        template<typename T>
        polycaller& assign (T &o, void (T::*f) (arg1, arg2)) {
                delete this->impl;
                this->impl = new polycaller_impl2<T,arg1,arg2>(&o, f);
                this->tid = &typeid(T);
                return *this;
        }

        void operator() (arg1 a, arg2 b) const {
                this->impl->call(a, b);
        }
};

template<typename arg1>
struct polycaller_impl_base1 {
        virtual ~polycaller_impl_base1(void) {}
	virtual void call(arg1 a) const = 0;
	virtual polycaller_impl_base1 *clone(void) const = 0;
};

template<typename T, typename arg1>
struct polycaller_impl1 : polycaller_impl_base1<arg1>,
		freelist_allocator<polycaller_impl1<T,arg1> > {
	typedef void (T::*ftype) (arg1);
	ftype	 f;
	T	*o;

        polycaller_impl1 (T *o_, ftype f_) : f(f_), o(o_) {}

        void call(arg1 a) const { (o->*f)(a); }

	polycaller_impl_base1<arg1> *clone(void) const {
		return new polycaller_impl1<T,arg1>(*this);
	}
};

template<typename arg1>
struct polycaller<arg1,void,void> {
	type_info const *tid;
	polycaller_impl_base1<arg1> *impl;

        polycaller(void) : tid(NULL), impl(NULL) {}
	~polycaller() {
		delete impl;
	}

	template<typename T>
	polycaller(T &o, void (T::*f) (arg1)) {
		assign(o, f);
	}
	polycaller(polycaller<arg1> const &other)
		: tid(other.tid)
		, impl(NULL) {
		if (other.impl)
			this->impl = other.impl->clone();
	}

	polycaller& operator= (polycaller<arg1> const &other) {
		delete impl;
		impl = NULL;
		if (other.impl)
			impl = other.impl->clone();
		return *this;
	}

        template<typename T>
        polycaller& assign (T &o, void (T::*f) (arg1)) {
                delete impl;
		impl = new polycaller_impl1<T,arg1>(&o, f);
		tid = &typeid(T);
		return *this;
        }

        void operator() (arg1 a) const {
                impl->call(a);
        }
};

struct polycaller_impl_base0 {
        virtual ~polycaller_impl_base0(void) {}
	virtual void call(void) const = 0;
	virtual polycaller_impl_base0 *clone(void) const = 0;
};

template<typename T>
struct polycaller_impl0 : polycaller_impl_base0,
		freelist_allocator<polycaller_impl0<T> > {
	typedef void (T::*ftype) (void);
	ftype	 f;
	T	*o;

        polycaller_impl0<T> (T *o_, ftype f_) : f(f_), o(o_) {}

        void call() const { (o->*f)(); }

	polycaller_impl_base0 *clone(void) const {
		return new polycaller_impl0<T>(*this);
	}
};

template<>
struct polycaller<void,void,void> {
	type_info const *tid;
	polycaller_impl_base0 *impl;

        polycaller(void)
		: tid(NULL)
		, impl(NULL) {
	}

	~polycaller() {
		delete impl;
	}

	template<typename T>
	polycaller(T &o, void (T::*f) (void))
		: tid(NULL)
		, impl(NULL) {
		assign(o, f);
	}
	polycaller& operator= (polycaller<void,void,void> const &other) {
		tid = other.tid;
		if (other.impl)
			other.impl->clone();
		return *this;
	}
	polycaller(polycaller<void> const &other)
		: tid(other.tid)
		, impl(NULL) {
		if (other.impl)
			impl = other.impl->clone();
	}

        template<typename T>
        polycaller& assign (T &o, void (T::*f) (void)) {
                delete impl;
                impl = new polycaller_impl0<T>(&o, f);
                tid = &typeid(T);
                return *this;
        }

        void operator() (void) const {
                impl->call();
        }
};

struct polycallback_null : std::logic_error {
	polycallback_null() : std::logic_error("null polycallback called") {}
};

template<typename arg1, typename arg2>
struct polycallback_binder_base2 {
	virtual ~polycallback_binder_base2() {}
        virtual void call(arg1, arg2) const = 0;
	virtual polycallback_binder_base2<arg1,arg2> *clone(void) const = 0;
};

template<typename Ftype, typename T, typename arg1, typename arg2>
struct polycallback_binder2 : polycallback_binder_base2<arg1,arg2>,
		freelist_allocator<polycallback_binder2<Ftype,T,arg1,arg2> > {
        Ftype   f;
        T       arg;
        polycallback_binder2 (Ftype f_, T arg_) : f(f_), arg(arg_) {}
        void call(arg1 a, arg2 b) const {
                f(a, b, arg);
        }
	polycallback_binder_base2<arg1,arg2> *clone(void) const {
		return new polycallback_binder2(*this);
	}
};

template<typename arg1 = void, typename arg2 = void>
struct polycallback {
        polycallback_binder_base2<arg1,arg2>    *binder;
	bool _null;

	polycallback() : binder(NULL), _null(true) {}
	polycallback(polycallback<arg1,arg2> const &other)
		: _null(other._null) {
		if (other.binder)
			binder = other.binder->clone();
		else	binder = NULL;
	} 

        template<typename Ftype, typename userT>
        polycallback(Ftype f, userT t) : binder(NULL), _null(false) {
                binder = new polycallback_binder2<Ftype,userT,arg1,arg2>(f,t);
        }
	~polycallback() {
		delete binder;
	}

        template<typename Ftype, typename userT>
        polycallback &assign(Ftype f, userT t) {
		delete binder;
                binder = new polycallback_binder2<Ftype,userT,arg1,arg2>(f,t);
		_null = false;
                return *this;
        }

	polycallback &operator= (polycallback<arg1, arg2> const &other) {
		delete binder;
		_null = other._null;
		if (other.binder)
			binder = other.binder->clone();
		else	binder = NULL;
		return *this;
	}

        void operator() (arg1 a, arg2 b) const {
		if (_null)
			throw polycallback_null();
                binder->call(a,b);
        }
};

template<typename arg1>
struct polycallback_binder_base1 {
	virtual ~polycallback_binder_base1() {}
        virtual void call(arg1) const = 0;
	virtual polycallback_binder_base1 *clone(void) const = 0;
};

template<typename Ftype, typename T, typename arg1>
struct polycallback_binder1 : polycallback_binder_base1<arg1>,
		freelist_allocator<polycallback_binder1<Ftype,T,arg1> > {
        Ftype   f;
        T       arg;
        polycallback_binder1 (Ftype f_, T arg_) : f(f_), arg(arg_) {}
        void call(arg1 a) const {
                f(a, arg);
        }
	polycallback_binder_base1<arg1> *clone(void) const {
		return new polycallback_binder1<Ftype,T,arg1>(*this);
	}
};

template<typename arg1>
struct polycallback<arg1,void> {
        polycallback_binder_base1<arg1> *binder;
	bool	_null;

	operator void const *(void) const {
		return _null ? NULL : this;
	}

	polycallback() : binder(NULL), _null(true) {}
	~polycallback() {
		delete binder;
	}

	polycallback(polycallback<arg1> const &other)
		: _null(other._null) {
		if (other.binder)
			binder = other.binder->clone();
		else	binder = NULL;
	}

	template<typename Ftype, typename userT>
	polycallback (Ftype f, userT t) : binder(NULL) {
		assign(f, t);
	}

        template<typename Ftype, typename userT>
        polycallback &assign(Ftype f, userT t) {
		delete binder;
                binder = new polycallback_binder1<Ftype,userT,arg1>(f,t);
		_null = false;
                return *this;
        }

	polycallback &operator= (polycallback<arg1> const &other) {
		delete binder;
		_null = other._null;
		if (other.binder)
			binder = other.binder->clone();
		else	binder = NULL;
		return *this;
	}

        void operator() (arg1 a) {
		if (_null)
			throw polycallback_null();
                binder->call(a);
        }
};

struct polycallback_binder_base0 {
	virtual ~polycallback_binder_base0();
        virtual void call() const = 0;
	virtual polycallback_binder_base0 *clone(void) const = 0;
};

template<typename Ftype, typename T>
struct polycallback_binder0 : polycallback_binder_base0,
		freelist_allocator<polycallback_binder0<Ftype,T> > {
        Ftype   f;
        T       arg;
        polycallback_binder0 (Ftype f_, T arg_) : f(f_), arg(arg_) {}
        void call() const {
                f(arg);
        }
	polycallback_binder_base0 *clone(void) const {
		return new polycallback_binder0<Ftype,T>(*this);
	}
};

template<>
struct polycallback<void,void> {
        polycallback_binder_base0       *binder;

	bool	_null;
	polycallback& operator=(int i) {
		_null = i;
		return *this;
	}
	operator void * (void) {
		return _null ? NULL : this;
	}

	polycallback() : binder(NULL), _null(true) {}
	polycallback(polycallback<> const &other) 
		: binder(NULL)
		, _null(other._null) {
		if (other.binder)
			binder = other.binder->clone();
	}

	polycallback& operator=(polycallback<> const &other) {
		delete binder;
		_null = other._null; 
		if (other.binder)
			binder = other.binder->clone();
		else	binder = NULL;
		return *this;
	}

	template<typename Ftype, typename userT>
	polycallback (Ftype f, userT t) : binder(NULL), _null(false) {
		assign(f, t);
	}
	~polycallback() {
		delete binder;
	}

        template<typename Ftype, typename userT>
        polycallback &assign(Ftype f, userT t) {
		delete binder;
                binder = new polycallback_binder0<Ftype,userT>(f,t);
		_null = false;
                return *this;
        }

        void operator() () {
		if (_null)
			throw polycallback_null();
                binder->call();
        }
};

#endif
