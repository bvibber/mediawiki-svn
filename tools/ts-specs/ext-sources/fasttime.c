/*
 *
 * Copyright 2004 Sun Microsystems, Inc.
 * 4150 Network Circle, Santa Clara, CA 95054
 * All Rights Reserved.
 *
 * This software is the proprietary information of Sun Microsystems, Inc.
 * This code is provided by Sun "as is" and "with all faults." Sun 
 * makes no representations or warranties concerning the quality, safety 
 * or suitability of the code, either express or implied, including 
 * without limitation any implied warranties of merchantability, fitness 
 * for a particular purpose, or non-infringement. In no event will Sun 
 * be liable for any direct, indirect, punitive, special, incidental 
 * or consequential damages arising from the use of this code. By 
 * downloading or otherwise utilizing this codes, you agree that you 
 * have read, understood, and agreed to these terms.
 *
 */
#include <sys/types.h>
#include <time.h>
#include <stdio.h>
#include <dlfcn.h>

/* time in nanoseconds to cache the time system call */
#define DELTA 10000000   /* 10 milliseconds */

static time_t (*time_real) (time_t *);

time_t
time(time_t *tloc)
{
	static time_t global = 0;
	static hrtime_t old = 0;

	hrtime_t new = gethrtime();
	if (global == 0 || (new - old > DELTA)) {
		global = time_real(tloc);
		old = new;
	}

	if (tloc != NULL)
		*tloc = global;
	return global;
}

#pragma init (fasttime_init)
void
fasttime_init()
{
	time_real = (time_t (*) (time_t *)) dlsym (RTLD_NEXT, "time");
	if (!time_real)
		fprintf(stderr, "Error initializing fasttime library\n");
}
