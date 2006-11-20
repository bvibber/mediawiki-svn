/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * libwillow: general helper library.
 */

#include "ptalloc.h"
#include "wthread.h"

void
pttsswrapdtor(void *p)
{
pta_block	**pt = (pta_block **)p, *n = *pt, *o;
	while ((o = n) != NULL) {
		n = n->next;
		free(o);
	}
	delete pt;
}

void
tss_null_dtor(void *)
{
}

void
ptdealloc(void *p)
{
vector<pta_block *> *v = (vector<pta_block *> *)p;
	for (vector<pta_block *>::iterator it = v->begin(), end = v->end();
	     it != end; ++it) {
	pta_block *n = *it, *o;
		while ((o = n) != NULL) {
			n = n->next;
			delete [] (char *)o->addr;
			free(o);
		}
	}
	delete v;
}

tss<vector<pta_block *>, ptdealloc> ptfreelist;
pttsswrap pttssw;
