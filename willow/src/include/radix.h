/*
 *
 * This file implements a 128 bit deep Patricia Trie, to facilitate very
 * fast best-match route lookup. Parts from this (and its accompanying C
 * file) were taken from the Merit MRTd project:
   This product includes software developed by the University of Michigan,
   Merit Network, Inc., and their contributors. 
 * 
 * Copyright (c) 2005, IPng, Pim van Pelt <pim@ipng.nl>
 */
/* From: Id: radix.h,v 1.1.1.1 2005/11/07 20:17:44 pim Exp */
/* $Id$ */

#ifndef _RADIX_H
#define _RADIX_H

#include <sys/types.h>
#include <inttypes.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <netdb.h>
#include <assert.h>
#include <stdio.h>

#define	RFL_ALLOW	0x01
#define RFL_DENY	0x02

typedef void (*void_fn_t)();

#define RADIX_MAXBITS 128
#define BIT_TEST(f, b)  ((f) & (b))

struct prefix {
		prefix();
		prefix (const char *);

	char *	tostring(void);

	uint16_t family;
	uint16_t prefixlen;
	uint32_t ref_count;
	union {
		struct in_addr 	sin4;
		struct in6_addr sin6;
	} add;
};


struct radix_node {
	uint32_t		 bit;
	struct prefix 		*prefix;
	struct radix_node 	*l, *r;
	struct radix_node 	*parent;
	void 			*data;
	int			 flags;
};

struct radix {
		radix();

	radix_node 	*head;
	uint32_t	 maxbits;
	uint32_t	 num_active_node;
};

struct prefix *prefix_fromstring (const char *string, prefix *);

struct radix_node *radix_add (radix *radix, const char *prefixstr);
struct radix_node *radix_search (const radix *radix, const char *prefixstr);
struct radix_node *radix_search_exact (const radix *radix, const char *prefixstr);
int radix_del (struct radix *radix, const char *prefixstr);
void radix_destroy (struct radix **radix, void_fn_t func);
void radix_doall (struct radix *radix, void_fn_t func);

#define RADIX_WALK(Xhead, Xnode) \
    do { \
        struct radix_node *Xstack[RADIX_MAXBITS+1]; \
        struct radix_node **Xsp = Xstack; \
        struct radix_node *Xrn = (Xhead); \
        while ((Xnode = Xrn)) { \
            if (Xnode->prefix)

#define RADIX_WALK_ALL(Xhead, Xnode) \
do { \
        struct radix_node *Xstack[RADIX_MAXBITS+1]; \
        struct radix_node **Xsp = Xstack; \
        struct radix_node *Xrn = (Xhead); \
        while ((Xnode = Xrn)) { \
            if (1)

#define RADIX_WALK_BREAK { \
            if (Xsp != Xstack) { \
                Xrn = *(--Xsp); \
             } else { \
                Xrn = (struct radix_node *) 0; \
            } \
            continue; }

#define RADIX_WALK_END \
            if (Xrn->l) { \
                if (Xrn->r) { \
                    *Xsp++ = Xrn->r; \
                } \
                Xrn = Xrn->l; \
            } else if (Xrn->r) { \
                Xrn = Xrn->r; \
            } else if (Xsp != Xstack) { \
                Xrn = *(--Xsp); \
            } else { \
                Xrn = (struct radix_node *) 0; \
            } \
        } \
    } while (0)

#endif /* _RADIX_H */
