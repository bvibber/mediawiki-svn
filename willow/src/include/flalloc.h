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
static  tss<T, flalloc_dtor<T> >		 _freelist;

        void *operator new(std::size_t size) {
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
        T       *o = (T *)p;
                o->_freelist_next = _freelist;
                _freelist = o;
        }
};

template<typename T>
tss<T, flalloc_dtor<T> > freelist_allocator<T>::_freelist;
#else	/* !__SUNPRO_CC */
template<typename T>
struct freelist_allocator {
};
#endif	/* __SUNPRO_CC */

#endif
