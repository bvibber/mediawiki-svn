/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * willow: General utility functions.
 */

#ifndef WILLOW_H
#define WILLOW_H

#undef WDEBUG_ALLOC

#ifdef WDEBUG_ALLOC
void *wmalloc(size_t);
void internal_wfree(void *, const char *, int);
#define wfree(p) internal_wfree(p, __FILE__, __LINE__)
#else
#define wmalloc malloc
#define wfree free
#endif

#endif
