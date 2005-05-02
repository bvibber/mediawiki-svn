/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wlogwriter: child process for log writing.
 */

#ifndef WLOGWRITER_H
#define WLOGWRITER_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

void wlogwriter_start(int *);

#endif
