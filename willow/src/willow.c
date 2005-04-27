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
#include <unistd.h>

#include "wlog.h"
#include "wnet.h"
#include "wconfig.h"
#include "willow.h"
#include "whttp.h"
#include "wcache.h"

/*ARGSUSED*/
static void 
sig_exit(s)
	int s;
{
	wnet_exit = 1;
	exit(0);
}

int 
main(argc, argv)
	char *argv[];
	int argc;
{
	int	i;
	int	zflag = 0;
	
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
	
	return EXIT_SUCCESS;
}

#ifdef WDEBUG_ALLOC
struct alloc_entry {
	void		*ae_addr;
	int		 ae_freed;
	const char	*ae_freed_file;
	int		 ae_freed_line;
struct	alloc_entry	*ae_next;
};

static struct alloc_entry allocs;

void *
wmalloc(size)
	size_t size;
{
	void		*p;
struct	alloc_entry	*ae;

	if (allocs.ae_next == NULL) {
		int i;
		ae = &allocs;
		for (i = 0; i < 50; ++i) {
			ae->ae_next = malloc(sizeof(struct alloc_entry));
			memset(ae->ae_next, 0, sizeof(struct alloc_entry));
			ae = ae->ae_next;
		}
	}

	if ((p = malloc(size)) == NULL)
		return NULL;

	for (ae = &allocs; ae->ae_next && ae->ae_next->ae_addr; ae = ae->ae_next)
		;

	if ((ae->ae_next = malloc(sizeof(struct alloc_entry))) == NULL) {
		fputs("out of memory\n", stderr);
		abort();
	}

	ae = ae->ae_next;
	memset(ae, 0, sizeof(struct alloc_entry));
	ae->ae_addr = p;
	return p;
}

void
internal_wfree(p, file, line)
	void *p;
	const char *file;
{
struct	alloc_entry	*ae;

	for (ae = allocs.ae_next; ae; ae = ae->ae_next) {
		if (ae->ae_addr == p) {
			if (ae->ae_freed) {
				fprintf(stderr, "wfree: ptr %p already freed @ %s:%d!\n", p, ae->ae_freed_file, ae->ae_freed_line);
				abort();
			}
			ae->ae_freed = 1;
			ae->ae_freed_file = file;
			ae->ae_freed_line = line;
			return;
		}
	}

	fprintf(stderr, "wfree: ptr %p never malloced!\n", p);
	abort();
}
#endif
