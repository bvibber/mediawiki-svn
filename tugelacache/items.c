/* -*- Mode: C; tab-width: 4; c-basic-offset: 4; indent-tabs-mode: nil -*- */
/* $Id$ */

#include <sys/types.h>
#include <sys/stat.h>
#include <sys/time.h>
#include <sys/socket.h>
#include <sys/signal.h>
#include <sys/resource.h>
#include <fcntl.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <netinet/in.h>
#include <errno.h>
#include <time.h>
#include <event.h>
#include <assert.h>

#include "dbcached.h"


/* 
 * NOTE: we assume here for simplicity that slab ids are <=32. That's true in 
 * the powers-of-2 implementation, but if that changes this should be changed too
 */

#define LARGEST_ID 32
static item *heads[LARGEST_ID];
static item *tails[LARGEST_ID];
unsigned int sizes[LARGEST_ID];

void item_init(void)
{
    int i;
    for (i = 0; i < LARGEST_ID; i++) {
	heads[i] = 0;
	tails[i] = 0;
	sizes[i] = 0;
    }
}


item *item_alloc(char *key, int flags, time_t exptime, int nbytes)
{
    int ntotal, len;
    item *it = NULL;

    len = strlen(key) + 1;
    if (len % 4)
	len += 4 - (len % 4);
    ntotal = sizeof(item) + len + nbytes;

    it = realloc(it, ntotal);
    it->refcount = 0;
    it->it_flags = 0;
    it->nkey = len;
    it->nbytes = nbytes;
    strcpy(ITEM_key(it), key);
    it->exptime = exptime;
    it->flags = flags;
    it->time = time(0);
    return it;
}

void item_free(item * it)
{
    assert((it->it_flags & ITEM_LINKED) == 0);
    assert(it->refcount == 0);

    /* so slab size changer can tell later if item is already free or not */
    it->it_flags |= ITEM_SLABBED;
}

void item_unlink(item * it)
{
    free(it);
}

void item_remove(item * it)
{
    free(it);
}

void item_update(item * it)
{
    it->time = time(0);
}

int item_replace(item * it, item * new_it)
{
	return 0;
}

char *item_cachedump(unsigned int slabs_clsid, unsigned int limit,
		     unsigned int *bytes)
{

    int memlimit = 2 * 1024 * 1024;
    char *buffer;
    int bufcurr;

    buffer = malloc(memlimit);
    if (buffer == 0)
	return 0;
    bufcurr = 0;
    strcpy(buffer + bufcurr, "END\r\n");
    bufcurr += 5;

    *bytes = bufcurr;
    return buffer;
}

void item_stats(char *buffer, int buflen)
{
    char *bufcurr = buffer;

    if (buflen < 4096) {
	strcpy(buffer, "SERVER_ERROR out of memory");
	return;
    }

    strcpy(bufcurr, "END");
    return;
}
