/**
 * Alternate version of malloc and related functions which abort the program 
 * on an allocation failure, instead of returning NULL. Suitable for LD_PRELOAD 
 * or static linking.
 */

#include <stdio.h>
#include <stdlib.h>

void * __libc_malloc(size_t size);
void * __libc_realloc(void * ptr, size_t newsize);
void * __libc_calloc(size_t count, size_t eltsize);
extern char *__progname;

void malloc_fail_abort()
{
	if (__progname) {
		fputs(__progname, stderr);
		fputs(": ", stderr);
	}
	fputs("Out of memory, aborting\n", stderr);
	fflush(stderr);
	abort();
}

void * malloc(size_t size)
{
	void *ret = __libc_malloc(size);
	if (!ret) {
		malloc_fail_abort();
	}
	return ret;
}

void * realloc(void * ptr, size_t newsize)
{
	void *ret = __libc_realloc(ptr, newsize);
	if (!ret) {
		malloc_fail_abort();
	}
	return ret;
}

void * calloc(size_t count, size_t eltsize)
{
	void *ret = __libc_calloc(count, eltsize);
	if (!ret) {
		malloc_fail_abort();
	}
	return ret;
}
