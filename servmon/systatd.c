/* $Header$ */
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/statvfs.h>

#include <netinet/in.h>

#include <arpa/inet.h>

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include <paths.h>
#include <unistd.h>
#include <mntent.h>
#include <errno.h>

#ifdef _PATH_MOUNTED
# define PATH_MTAB _PATH_MOUNTED
#else
# define PATH_MTAB "/etc/mtab"
#endif

#define PORT 8576

static void print_mntents(FILE *where);

int main(argc, argv)
char **argv;
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

static void
print_mntents(where)
FILE *where;
{
	struct mntent  *ent;
	struct statvfs  sbuf;
        FILE           *mtab;
	fsblkcnt_t      cnt;
	
	if ((mtab = setmntent(PATH_MTAB, "r")) == NULL) {
		perror("setmntent");
		return;
	}

	while ((ent = getmntent(mtab)) != NULL) {
		if (statvfs(ent->mnt_dir, &sbuf) < 0) {
			perror("statvfs");
			goto end;
		}

		cnt = sbuf.f_bavail;
		if (cnt == 0)
			continue;
		fprintf(where, "%s %lu %lu\n", ent->mnt_dir, (unsigned long) cnt, sbuf.f_bsize);
		
	}

  end:
	endmntent(mtab);
}
