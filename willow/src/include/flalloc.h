/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * flalloc: Fast freelist allocator.
 */

#ifndef FLALLOC_H
#define FLALLOC_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <cstdlib>
using std::memset;

#include "wthread.h"

template<typename T>
struct freelist_allocator {
	T		*_freelist_next;
static  tss<T>		 _freelist;

        void *operator new(std::size_t size) {
                if (_freelist) {
                T       *n = _freelist;
                        _freelist = _freelist->_freelist_next;
			memset(n, 0, sizeof(*n));
                        return n;
                } else {
		void	*ret;
			ret = new char[size];
			memset(ret, 0, size);
			return ret;
		}
        }

 	void *operator new (std::size_t, T *pos) {
		return pos;
	}

        void operator delete (void *p) {
        T       *o = (T *)p;
		memset(o, 0, sizeof(*o));
                o->_freelist_next = _freelist;
                _freelist = o;
        }
};

template<typename T>
tss<T> freelist_allocator<T>::_freelist;

#endif
