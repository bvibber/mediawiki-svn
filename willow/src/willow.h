/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * willow: General utility functions.
 */

#ifndef WILLOW_H
#define WILLOW_H

#define WDEBUG_ALLOC

#ifdef WDEBUG_ALLOC
void *wmalloc(size_t);
void wfree(void *);
#else
#define wmalloc malloc
#define wfree free
#endif

#endif
