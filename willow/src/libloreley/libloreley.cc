/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* libloreley: general helper library.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "ptalloc.h"
#include "thread.h"

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
