/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 */

#include <sys/mman.h>

#include <stdio.h>
#include <stdlib.h>
#include <signal.h>
#include <stdarg.h>
#include <string.h>
#include <unistd.h>
#include <errno.h>

#include "wlog.h"
#include "wnet.h"
#include "wconfig.h"
#include "willow.h"
#include "whttp.h"
#include "wcache.h"

#ifdef WDEBUG_ALLOC
static void ae_checkleaks(void);
#endif

#define min(x,y) ((x) < (y) ? (x) : (y))

/*ARGSUSED*/
static void 
sig_exit(s)
	int s;
{
	wnet_exit = 1;
}

#ifdef WDEBUG_ALLOC
static void segv_action(int, siginfo_t *, void *);
#endif

int 
main(argc, argv)
	char *argv[];
	int argc;
{
	int	i;
	int	zflag = 0;
	
#ifdef WDEBUG_ALLOC
struct	sigaction	segv_act;
	memset(&segv_act, 0, sizeof(segv_act));
	segv_act.sa_sigaction = segv_action;
	segv_act.sa_flags = SA_SIGINFO;
	
	sigaction(SIGSEGV, &segv_act, NULL);
#endif
	while ((i = getopt(argc, argv, "fz")) != -1) {
		switch (i) {
			case 'z':
				zflag++;
			case 'f':
				config.foreground = 1;
				break;
			default:
				exit(8);
		}
	}

	argv += optind;
	argc -= optind;

	wnet_set_time();

	wconfig_init(NULL);
	wlog_init();
	if (zflag) {
		wcache_setupfs();
		exit(0);
	}
	wcache_init();
		
	/*
	 * HTTP should be initialised before the network so that
	 * the wlogwriter exits cleanly.
	 */
	whttp_init();
	wnet_init();

	signal(SIGINT, sig_exit);
	signal(SIGTERM, sig_exit);
	
	wlog(WLOG_NOTICE, "running");

	if (!config.foreground)
		daemon(0, 0);

	wnet_run();
	wlog_close();
	wcache_shutdown();
	whttp_shutdown();
	
#ifdef WDEBUG_ALLOC
	ae_checkleaks();
#endif
	return EXIT_SUCCESS;
}

#ifdef WDEBUG_ALLOC
struct alloc_entry {
	char		*ae_addr;
	char		*ae_mapping;
	size_t		 ae_mapsize;
	size_t		 ae_size;
	int		 ae_freed;
	const char	*ae_freed_file;
	int		 ae_freed_line;
	const char	*ae_alloced_file;
	int		 ae_alloced_line;
struct	alloc_entry	*ae_next;
};

static struct alloc_entry allocs;
static int pgsize;

static void
segv_action(sig, si, data)
	int sig;
	siginfo_t *si;
	void *data;
{
struct	alloc_entry	*ae;

	fprintf(stderr, "SEGV at %p%s (pid %d)\n", si->si_addr, si->si_code == SI_NOINFO ? " [SI_NOINFO]" : "",
			(int) getpid());
	for (ae = allocs.ae_next; ae; ae = ae->ae_next)
		if (!ae->ae_freed && (char *)si->si_addr > ae->ae_mapping && 
				(char *)si->si_addr < ae->ae_mapping + ae->ae_mapsize) {
			fprintf(stderr, "\t%p [map @ %p size %d] from %s:%d\n", ae->ae_addr, ae->ae_mapping,
					ae->ae_mapsize, ae->ae_alloced_file, ae->ae_alloced_line);
			break;
		}
	if (ae == NULL)
		fprintf(stderr, "\tunknown address\n");
	abort();
	_exit(1);
}		
	
static void
ae_checkleaks(void)
{
struct	alloc_entry	*ae;

	for (ae = allocs.ae_next; ae; ae = ae->ae_next)
		if (!ae->ae_freed)
			fprintf(stderr, "%p @ %s:%d\n", ae->ae_addr, ae->ae_alloced_file, ae->ae_alloced_line);
}

void *
internal_wmalloc(size, file, line)
	size_t size;
	const char *file;
	int line;
{
	void		*p;
struct	alloc_entry	*ae;
	size_t		 mapsize;
	
	if (pgsize == 0)
		pgsize = sysconf(_SC_PAGESIZE);
	
	mapsize = (size/pgsize + 2) * pgsize;
	if ((p = mmap(NULL, mapsize, PROT_READ|PROT_WRITE, MAP_PRIVATE | MAP_ANON, -1, 0)) == (void *)-1) {
		fprintf(stderr, "mmap: %s\n", strerror(errno));
		return NULL;
	}

	for (ae = &allocs; ae->ae_next; ae = ae->ae_next)
		if (ae->ae_next->ae_mapping == p)
			break;

	if (!ae->ae_next) {
		if ((ae->ae_next = malloc(sizeof(struct alloc_entry))) == NULL) {
			fputs("out of memory\n", stderr);
			abort();
		}
		memset(ae->ae_next, 0, sizeof(struct alloc_entry));
	}

	ae = ae->ae_next;
	ae->ae_addr = ((char *)p + (mapsize - pgsize)) - size;
	ae->ae_mapping = p;
	ae->ae_mapsize = mapsize;
	ae->ae_size = size;
	ae->ae_freed = 0;
	ae->ae_alloced_file = file;
	ae->ae_alloced_line = line;
	fprintf(stderr, "alloc %d @ %p [map @ %p:%p, size %d] at %s:%d\n", size, ae->ae_addr,
			ae->ae_mapping, ae->ae_mapping + ae->ae_mapsize, ae->ae_mapsize, file, line);
	if (mprotect(ae->ae_addr + size, pgsize, PROT_NONE) < 0) {
		fprintf(stderr, "mprotect(0x%p, %d, PROT_NONE): %s\n", ae->ae_addr + size, pgsize, strerror(errno));
		exit(8);
	}
	return ae->ae_addr;
}

void
internal_wfree(p, file, line)
	void *p;
	const char *file;
	int line;
{
struct	alloc_entry	*ae;

	fprintf(stderr, "free %p @ %s:%d\n", p, file, line);
	
	for (ae = allocs.ae_next; ae; ae = ae->ae_next) {
		if (ae->ae_addr == p) {
			if (ae->ae_freed) {
				fprintf(stderr, "wfree: ptr %p already freed @ %s:%d! [alloced at %s:%d]\n", 
						p, ae->ae_freed_file, ae->ae_freed_line,
						ae->ae_alloced_file, ae->ae_alloced_line);
				ae_checkleaks();
				abort();
			}
			ae->ae_freed = 1;
			ae->ae_freed_file = file;
			ae->ae_freed_line = line;
			if (mprotect(ae->ae_addr + ae->ae_size, pgsize, PROT_READ | PROT_WRITE) < 0) {
				fprintf(stderr, "mprotect(0x%p, %d, PROT_READ | PROT_WRITE): %s\n", 
						ae->ae_addr + ae->ae_size, pgsize, strerror(errno));
				exit(8);
			}
			munmap(ae->ae_mapping, ae->ae_mapsize);
			return;
		}
	}

	fprintf(stderr, "wfree: ptr %p never malloced!\n", p);
	ae_checkleaks();
	abort();
}

char *
internal_wstrdup(s, file, line)
	const char *s, *file;
	int line;
{
	char *ret = internal_wmalloc(strlen(s) + 1, file, line);
	strcpy(ret, s);
	return ret;
}

void *
internal_wrealloc(p, size, file, line)
	void *p;
	const char *file;
	int line;
	size_t size;
{
	void 		*new;
struct	alloc_entry	*ae;
	size_t		 osize = 0;
		
	if (!p)
		return internal_wmalloc(size, file, line);
	
	for (ae = allocs.ae_next; ae; ae = ae->ae_next)
		if (ae->ae_addr == p) {
			osize = ae->ae_size;
			break;
		}
		
	if (osize == 0) {
		fprintf(stderr, "wrealloc: ptr %p never malloced!\n", p);
		ae_checkleaks();
		abort();
	}
	
	new = internal_wmalloc(size, file, line);
	memcpy(new, p, min(osize, size));
	internal_wfree(p, file, line);
	return new;
}
#endif
