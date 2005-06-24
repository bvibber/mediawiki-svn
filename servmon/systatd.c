/* $Header$ */
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/statvfs.h>

#include <netinet/in.h>

#include <arpa/inet.h>

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "bconf/headers.h"

#ifdef T_PATHS
# include <paths.h>
#endif

#ifdef T_MNTENT
# include <mntent.h>
#endif

#ifdef T_SYS_MNTTAB
# include <sys/mnttab.h>
#endif

#include <unistd.h>
#include <errno.h>

#ifdef _PATH_MOUNTED /* BSD, glibc */
# define PATH_MTAB _PATH_MOUNTED
#else
# ifdef MNTTAB /* SVr4 */
#  define PATH_MTAB MNTTAB
# else
#  define PATH_MTAB "/etc/mtab" /* Others */
# endif
#endif

/*
 * SVr4 doesn't provide daemon() itself, but libresolv includes it
 * so we win anyway.
 */
#if defined(HAVE_DAEMON) && defined(__svr4__)
	int daemon(int, int);
#endif 

#define PORT 8576

static void print_mntents(FILE *where);

int 
main(void)
{
	int                sfd, cfd, one = 1;
	socklen_t          clilen;
	struct sockaddr_in servaddr, cliaddr;
	
	if ((sfd = socket(AF_INET, SOCK_STREAM, 0)) < 0) {
		perror("socket");
		exit(8);
	}
	
	memset(&servaddr, 0, sizeof(servaddr));
	servaddr.sin_family = AF_INET;
	servaddr.sin_addr.s_addr = htonl(INADDR_ANY);
	servaddr.sin_port = htons(PORT);
	setsockopt(sfd, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));
	if (bind(sfd, (struct sockaddr *) &servaddr, sizeof(servaddr)) < 0) {
		perror("bind");
		exit(8);
	}
	if (listen(sfd, 5) < 0) {
		perror("listen");
		exit(8);
	}

	if (daemon(0, 0) < 0) {
		perror("daemon");
		exit(8);
	}

	clilen = sizeof(cliaddr);
	while ((cfd = accept(sfd, (struct sockaddr *) &cliaddr, &clilen)) > 0) {
		FILE *f;
		
		if ((f = fdopen(cfd, "w")) == NULL) {
			perror("fdopen");
			close(cfd);
			continue;
		}
		print_mntents(f);
		fclose(f);
		close(cfd);
	}
	return 0;
}

/*
 * There are two different *mntent APIs.
 *
 * BSD:
 *   setmntent() and endmntent() are available.
 *   getmntent() returns a struct mntent*
 *   the name of the mount point is mnt_dir.
 *
 * SVr4:
 *   setmntent() and endmntent() are not available; getmntent() 
 *     expects us to fopen() and fclose() ourselves.
 *   getmntent() takes the address of a struct mntent as an argment
 *     and return !0 on error.
 *   the name of the mount point is mnt_mountp.
 *
 * We emulate the BSD set/endmntent(), but the SVr4 getmntent(),
 * because it makes memory allocation easier (and it's also thread-safe
 * on SVr4, but we aren't threaded here anyway...).
 */
#ifndef __svr4__
# define mnt_mountp mnt_dir
# define mnttab mntent
#endif

#ifndef HAVE_SETMNTENT
static FILE*
setmntent(path, mode)
	char const *path, *mode;
{
	return fopen(path, mode);
}
#endif

#ifndef HAVE_SETMNTENT
static int
endmntent(mtab)
	FILE *mtab;
{
	return fclose(mtab);
}
#endif

static int
my_getmntent(mtab, ent)
	FILE *mtab;
	struct mnttab *ent;
{
#ifdef __svr4__
	return getmntent(mtab, ent) == 0 ? 0 : -1;
#else /* BSD */
	struct mnttab 	*tent;

	if ((tent = getmntent(mtab)) == NULL)
		return -1;

	memcpy(ent, tent, sizeof(*ent));
	return 0;
#endif /* SVr4 */
}
	
static void
print_mntents(where)
	FILE *where;
{
	struct statvfs  sbuf;
        FILE           *mtab;
	fsblkcnt_t      cnt;
	struct mnttab	ent;

	if ((mtab = setmntent(PATH_MTAB, "r")) == NULL) {
		perror(PATH_MTAB);
		return;
	}

	while (my_getmntent(mtab, &ent) == 0) {
		if (statvfs(ent.mnt_mountp, &sbuf) < 0) {
			perror("statvfs");
			goto end;
		}

		cnt = sbuf.f_bavail;
		if (cnt == 0)
			continue;
		fprintf(where, "%s %lu %lu\n", ent.mnt_mountp, (unsigned long) cnt, sbuf.f_bsize);
		
	}

  end:
	endmntent(mtab);
}
