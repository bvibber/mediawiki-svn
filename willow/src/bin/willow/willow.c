/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include <sys/mman.h>

#include <stdio.h>
#include <stdlib.h>
#include <signal.h>
#include <stdarg.h>
#include <string.h>
#include <unistd.h>
#include <errno.h>
#include <pwd.h>
#include <grp.h>

#include "wlog.h"
#include "wnet.h"
#include "wconfig.h"
#include "willow.h"
#include "whttp.h"
#include "wcache.h"

#ifdef WDEBUG_ALLOC
static void ae_checkleaks(void);
static void segv_action(int, siginfo_t *, void *);
#endif

static const char *progname;

#define min(x,y) ((x) < (y) ? (x) : (y))

/*ARGSUSED*/
static void 
sig_exit(s)
	int s;
{
	wnet_exit = 1;
}

static void
usage(void)
{
	(void)fprintf(stderr, "usage: %s [-fzv]\n"
			"\t-f\trun in foreground (don't detach)\n"
			"\t-z\tcreate cache directory structure and exit\n"
			"\t-v\tprint version number and exit\n"
			, progname);
}

#ifdef __lint
# pragma error_messages(off, E_H_C_CHECK2)
#endif

int 
main(argc, argv)
	char *argv[];
	int argc;
{
	int	 i;
	int	 zflag = 0;
	char	*cfg = NULL;
	
#ifdef WDEBUG_ALLOC
struct	sigaction	segv_act;
	bzero(&segv_act, sizeof(segv_act));
	segv_act.sa_sigaction = segv_action;
	segv_act.sa_flags = SA_SIGINFO;
	
	sigaction(SIGSEGV, &segv_act, NULL);
#endif
	
	progname = argv[0];
	
	while ((i = getopt(argc, argv, "fzvc:")) != -1) {
		switch (i) {
			case 'z':
				zflag++;
			/*FALLTHRU*/
			case 'f':
				config.foreground = 1;
				break;
			case 'v':
				(void)fprintf(stderr, "%s\n", PACKAGE_VERSION);
				exit(0);
				/*NOTREACHED*/
			case 'c':
				cfg = optarg;
				break;
			default:
				usage();
				exit(8);
		}
	}

	argv += optind;
	argc -= optind;

	if (argc) {
		(void)fprintf(stderr, "%s: too many argments\n", progname);
		usage();
		exit(8);
	}
	
	wnet_set_time();

	wconfig_init(cfg);

	if (config.sgid) {
		struct group *group = getgrnam(config.sgid);
		if (!group) {
			fprintf(stderr, "group %s does not exist", config.sgid);
			exit(8);
		}
		if (setgid(group->gr_gid) < 0) {
			perror("setgid");
			exit(8);
		}
	}

	if (config.suid) {
		struct passwd *user = getpwnam(config.suid);
		if (!user) {
			fprintf(stderr, "user %s does not exist", config.suid);
			exit(8);
		}
		if (setuid(user->pw_uid) < 0) {
			perror("setuid");
			exit(8);
		}
	}

	wlog_init();
	if (zflag) {
		wcache_setupfs();
		exit(0);
	}
	wcache_init(1);
		
	/*
	 * HTTP should be initialised before the network so that
	 * the wlogwriter exits cleanly.
	 */
	whttp_init();
	wnet_init();

	(void)signal(SIGINT, sig_exit);
	(void)signal(SIGTERM, sig_exit);
	
	wlog(WLOG_NOTICE, "running");

#ifdef WDEBUG_ALLOC
	(void)fprintf(stderr, "debug allocator enabled, assuming -f\n");
	config.foreground = 1;
#endif
	
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

#ifdef __lint
# pragma error_messages(default, E_H_C_CHECK2)
#endif

void
outofmemory(void)
{
	static int count;
	
	if (count++)
		abort();
	
	wlog(WLOG_ERROR, "fatal: out of memory. exiting.");
	exit(8);
}

void
realloc_addchar(sp, c)
	char **sp;
	int c;
{
	char	*p;
	int	 len;
	
	if (*sp)
		len = strlen(*sp);
	else
		len = 0;
	
	if ((*sp = wrealloc(*sp, len + 2)) == NULL)
		outofmemory();
	p = *sp + len;
	*p++ = (char) c;
	*p++ = '\0';
}

void
realloc_strcat(sp, s)
	char **sp;
	const char *s;
{
	int	 len;
	
	if (*sp)
		len = strlen(*sp);
	else
		len = 1;
	if ((*sp = wrealloc(*sp, len + strlen(s) + 1)) == NULL)
		outofmemory();
	(void)strcat(*sp, s);
}

int char_table[256] = {
	/* 0   */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 8   */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 16  */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 24  */ 0, 0, 0, 0, 0, 0, 0, 0, 
	/* 32  */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 40  */ 0, 0, 0, 0, 0, 0, CHAR_HOST, 0,
	/* 48  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST, 
	/* 52  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 56  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, 0,
	/* 60  */ 0, 0, 0, 0,
	/* 64  */ 0, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 68  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 72  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 76  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 80  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 84  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 88  */ CHAR_HOST, CHAR_HOST, CHAR_HOST, 0,
	/* 92  */ 0, 0, 0, 0,
	/* 96  */ 0, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 100 */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 104 */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 108 */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 112 */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 116 */ CHAR_HOST, CHAR_HOST, CHAR_HOST, CHAR_HOST,
	/* 120 */ CHAR_HOST, CHAR_HOST, 0, 0,
	/* 124 */ 0, 0, 0, 0,
	/* 136 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 144 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 152 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 160 */ 0, 0, 0, 0, 0, 0, 0, 0, 
	/* 168 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 176 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 184 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 192 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 200 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 208 */ 0, 0, 0, 0, 0, 0, 0, 0, 
	/* 216 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 224 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 232 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 240 */ 0, 0, 0, 0, 0, 0, 0, 0,
	/* 248 */ 0, 0, 0, 0, 0, 0, 0, 0,
};
	
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

	/*
	 * This is mostly non-standard, unportable and unreliable, but if the debug allocator
	 * is enabled, it's more important to produce useful errors than conform to the letter
	 * of the law.
	 */
	(void)fprintf(stderr, "SEGV at %p%s (pid %d)\n", si->si_addr, si->si_code == SI_NOINFO ? " [SI_NOINFO]" : "",
			(int) getpid());
	for (ae = allocs.ae_next; ae; ae = ae->ae_next)
		if (!ae->ae_freed && (char *)si->si_addr > ae->ae_mapping && 
				(char *)si->si_addr < ae->ae_mapping + ae->ae_mapsize) {
			(void)fprintf(stderr, "\t%p [map @ %p size %d] from %s:%d\n", ae->ae_addr, ae->ae_mapping,
					ae->ae_mapsize, ae->ae_alloced_file, ae->ae_alloced_line);
			break;
		}
	if (ae == NULL)
		(void)fprintf(stderr, "\tunknown address\n");
	abort();
	_exit(1);
}		
	
static void
ae_checkleaks(void)
{
struct	alloc_entry	*ae;

	for (ae = allocs.ae_next; ae; ae = ae->ae_next)
		if (!ae->ae_freed)
			(void)fprintf(stderr, "%p @ %s:%d\n", ae->ae_addr, ae->ae_alloced_file, ae->ae_alloced_line);
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
		(void)fprintf(stderr, "mmap: %s\n", strerror(errno));
		return NULL;
	}

	for (ae = &allocs; ae->ae_next; ae = ae->ae_next)
		if (ae->ae_next->ae_mapping == p)
			break;

	if (!ae->ae_next) {
		if ((ae->ae_next = malloc(sizeof(struct alloc_entry))) == NULL) {
			(void)fputs("out of memory\n", stderr);
			abort();
		}
		bzero(ae->ae_next, sizeof(struct alloc_entry));
	}

	ae = ae->ae_next;
	ae->ae_addr = ((char *)p + (mapsize - pgsize)) - size;
	ae->ae_mapping = p;
	ae->ae_mapsize = mapsize;
	ae->ae_size = size;
	ae->ae_freed = 0;
	ae->ae_alloced_file = file;
	ae->ae_alloced_line = line;
	(void)fprintf(stderr, "alloc %d @ %p [map @ %p:%p, size %d] at %s:%d\n", size, ae->ae_addr,
			ae->ae_mapping, ae->ae_mapping + ae->ae_mapsize, ae->ae_mapsize, file, line);
	if (mprotect(ae->ae_addr + size, pgsize, PROT_NONE) < 0) {
		(void)fprintf(stderr, "mprotect(0x%p, %d, PROT_NONE): %s\n", ae->ae_addr + size, pgsize, strerror(errno));
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

	(void)fprintf(stderr, "free %p @ %s:%d\n", p, file, line);
	
	for (ae = allocs.ae_next; ae; ae = ae->ae_next) {
		if (ae->ae_addr == p) {
			if (ae->ae_freed) {
				(void)fprintf(stderr, "wfree: ptr %p already freed @ %s:%d! [alloced at %s:%d]\n", 
						p, ae->ae_freed_file, ae->ae_freed_line,
						ae->ae_alloced_file, ae->ae_alloced_line);
				ae_checkleaks();
				abort();
			}
			ae->ae_freed = 1;
			ae->ae_freed_file = file;
			ae->ae_freed_line = line;
			if (mprotect(ae->ae_addr + ae->ae_size, pgsize, PROT_READ | PROT_WRITE) < 0) {
				(void)fprintf(stderr, "mprotect(0x%p, %d, PROT_READ | PROT_WRITE): %s\n", 
						ae->ae_addr + ae->ae_size, pgsize, strerror(errno));
				exit(8);
			}
			munmap(ae->ae_mapping, ae->ae_mapsize);
			return;
		}
	}

	(void)fprintf(stderr, "wfree: ptr %p never malloced!\n", p);
	ae_checkleaks();
	abort();
}

char *
internal_wstrdup(s, file, line)
	const char *s, *file;
	int line;
{
	char *ret = internal_wmalloc(strlen(s) + 1, file, line);
	(void)strcpy(ret, s);
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
		(void)fprintf(stderr, "wrealloc: ptr %p never malloced!\n", p);
		ae_checkleaks();
		abort();
	}

	new = internal_wmalloc(size, file, line);
	bcopy(p, new, min(osize, size));
	internal_wfree(p, file, line);
	
	return new;
}

void *
internal_wcalloc(num, size, file, line)
	size_t num, size;
	const char *file;
	int line;
{
	size_t	 t = size * num;
	void	*p;
	
	if ((p = internal_wmalloc(t)) == NULL)
		return NULL;
	bzero(p, t);
	return p;
}
		
#endif
