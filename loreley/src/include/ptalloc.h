/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* ptalloc: Fast power-of-two allocator.				*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef PTALLOC_H
#define PTALLOC_H

#include <boost/format.hpp>

#include <string>
#include <sstream>

#include "flalloc.h"
#include "util.h"
#include "thread.h"
#include "autoconf.h"

/*
 * A fast, thread-specific power of two allocator.  Backended by
 * new[].
 */
struct pta_block {
	struct pta_block *next;
	void *addr;
};

static const int lt256[] = 
{
  0, 0, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 3, 3,
  4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4,
  5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5,
  5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5,
  6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
  6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
  6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
  6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
  7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7,
  7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7,
  7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7,
  7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7,
  7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7,
  7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7,
  7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7,
  7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7
};

#if !defined(DISABLE_ALLOCS)
#if 0
extern "C" void pttsswrapdtor(void *);

struct pttsswrap {
	pttsswrap() {
		pthread_key_create(&key, pttsswrapdtor);
	}
	pthread_key_t key;
};

void ptdealloc(void *);

extern tss<vector<pta_block *>, ptdealloc> ptfreelist;
extern pttsswrap pttssw;
#endif

template<typename T>
struct pt_allocator {
	typedef T			 value_type;
	typedef value_type		*pointer;
	typedef value_type const	*const_pointer;
	typedef value_type		&reference;
	typedef value_type const	&const_reference;
	typedef size_t			 size_type;
	typedef ptrdiff_t		 difference_type;

	static lockable			 _lock;
	static vector<pta_block *>	 _ptfreelist;
	static pta_block		*_ptafl;
	pta_block *get_ptb(void) {
	pta_block	 *ret;
#if 0
	pta_block	**ptfl = (pta_block **)pthread_getspecific(pttssw.key);
		if (!ptfl) {
			ptfl = new pta_block *(NULL);
			pthread_setspecific(pttssw.key, ptfl);
		}
		if (*ptfl) {
			ret = *ptfl;
			(*ptfl) = ret->next;
		} else {
			ret = (pta_block *)malloc(sizeof(*ret));
		}
#endif
		if (_ptafl) {
			ret = _ptafl;
			_ptafl = ret->next;
		} else {
			ret = (pta_block *)malloc(sizeof(*ret));
		}
		
		return ret;
	}

	void lose_ptb(pta_block *ptb) {
#if 0
	pta_block	**ptfl = (pta_block **)pthread_getspecific(pttssw.key);
#endif
		ptb->next = _ptafl;
		_ptafl = ptb;
	}

	pointer address (reference x) const {
		return &x;
	}

	const_pointer address(const_reference x) const {
		return &x;
	}

	pointer allocate(size_type n, const_pointer = 0) {
	size_t			 sz = sizeof(T) * n;
	int			 exp = ilog2(sz) + 1;
	void			*ret;
		HOLDING(_lock);

	vector<pta_block *>	&fl = _ptfreelist;

		/* do we have a free block of this size? */
		if (exp < (int) fl.size() && fl[exp]) {
		pta_block	*ptb = fl[exp];
			fl[exp] = ptb->next;
			ret = ptb->addr;
			lose_ptb(ptb);
			return (pointer) ret;
		}
		/* no, need a new block */
		ret = new char[2 << exp];
		return (pointer) ret;		
	}

	void deallocate(pointer p, size_type n) {
	size_t			 sz = sizeof(T) * n;
	int			 exp = ilog2(sz) + 1;
	pta_block		*ptb = get_ptb();
		HOLDING(_lock);

	vector<pta_block *>	&fl = _ptfreelist;

		if ((int)fl.size() <= exp)
			fl.resize(exp + 1);
		ptb->addr = p;
		ptb->next = fl[exp];
		fl[exp] = ptb;
		
	}

	size_type max_size(void) const {
		return static_cast<size_type>(-1) / sizeof(T);
	}

	void construct(pointer p, const value_type &x) {
		new (p) value_type(x);
	}

	void destroy(pointer p) {
		p->~value_type();
	}

	pt_allocator (void) {}
	~pt_allocator (void) {}

	template<typename U>
	pt_allocator (const pt_allocator<U>& other) {}

	template<typename U>
	struct rebind {
		typedef pt_allocator<U> other;
	};

	bool operator== (pt_allocator const &) const {
		return true;
	}

	/* from http://graphics.stanford.edu/~seander/bithacks.html#IntegerLogLookup */
	int ilog2(int i) {
	unsigned r = 0; // r will be lg(v)
	register unsigned int t, tt; // temporaries

		if ((tt = (i >> 16)))
			r = (t = (i >> 24)) ? 24 + lt256[t] : 16 + lt256[tt & 0xFF];
		else 
			r = (t = (i >> 8)) ? 8 + lt256[t] : lt256[i];
		return r;
	}
};

template<typename T>
vector<pta_block *> pt_allocator<T>::_ptfreelist;
template<typename T>
pta_block *pt_allocator<T>::_ptafl;
template<typename T>
lockable pt_allocator<T>::_lock;

template<>
struct pt_allocator<void>
{
	typedef void		 value_type;
	typedef void		*pointer;
	typedef void const	*const_pointer;

	template<typename U>
	struct rebind {
		typedef pt_allocator<U>	other;
	};
};
#else /* !DISABLE_ALLOCS */

template<typename T>
struct pt_allocator : std::allocator<T> {
	pt_allocator (void) {}
	~pt_allocator (void) {}

	template<typename U>
	pt_allocator (const pt_allocator<U>& other) {}

	template<typename U>
	struct rebind {
		typedef pt_allocator<U>	other;
	};
};

#endif /* DISABLE_ALLOCS */

typedef std::basic_string<char, char_traits<char>, pt_allocator<char> > ptstring;
typedef ptstring string;
typedef std::basic_string<u_char, char_traits<u_char>, pt_allocator<u_char> > ustring;
typedef std::basic_stringstream<char, std::char_traits<char>, pt_allocator<char > >
	stringstream;
typedef std::basic_ostringstream<char, std::char_traits<char>, pt_allocator<char > >
	ostringstream;
typedef std::basic_istringstream<char, std::char_traits<char>, pt_allocator<char > >
	istringstream;

typedef boost::archive::iterators::base64_from_binary<
		boost::archive::iterators::transform_width<
			string::const_iterator, 6, 8> > base64_string;

typedef boost::archive::iterators::transform_width<
		boost::archive::iterators::binary_from_base64<
			string::const_iterator, char>, 8, 6> unbase64_string;

typedef boost::basic_format<char, std::char_traits<char>, pt_allocator<char> >
		format;

#endif
