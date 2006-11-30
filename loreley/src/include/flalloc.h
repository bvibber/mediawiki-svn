/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* flalloc: Fast freelist allocator.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef FLALLOC_H
#define FLALLOC_H

#include <cstdlib>
using std::memset;

#include "thread.h"

#ifndef __SUNPRO_CC
template<typename T>
void
flalloc_dtor(void *p)
{
T	*n = (T *)p, *o;
	while ((o = n) != NULL) {
		n = n->_freelist_next;
		::operator delete(o);
	}
}

template<typename T>
struct freelist_allocator {
	T		*_freelist_next;
#if 0
static  tss<T, flalloc_dtor<T> >		 _freelist;
#endif
	static T	*_freelist;
	static lockable	 _lock;

        void *operator new(std::size_t size) {
		HOLDING(_lock);
                if (_freelist) {
                T       *n = _freelist;
                        _freelist = _freelist->_freelist_next;
                        return n;
                } else {
		void	*ret;
			ret = ::operator new(size);
			return ret;
		}
        }

 	void *operator new (std::size_t, T *pos) {
		return pos;
	}

	void operator delete (void *, T *) {
	}

        void operator delete (void *p) {
		HOLDING(_lock);
        T       *o = (T *)p;
                o->_freelist_next = _freelist;
                _freelist = o;
        }
};

#if 0
template<typename T>
tss<T, flalloc_dtor<T> > freelist_allocator<T>::_freelist;
#endif
template<typename T>
T *freelist_allocator<T>::_freelist;
template<typename T>
lockable freelist_allocator<T>::_lock;

#else	/* !__SUNPRO_CC */
template<typename T>
struct freelist_allocator {
};
#endif	/* __SUNPRO_CC */

#endif
