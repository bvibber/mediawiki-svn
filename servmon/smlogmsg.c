/* @(#) $Header$ */
#include <sys/socket.h>
#include <sys/un.h>

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <errno.h>
#include <unistd.h>

int
main(int argc, char *argv[])
{
	struct sockaddr_un 	 sa;
	socklen_t		 len;
	int			 s;
	struct iovec		 iovec[3];

	if (argc != 3) {
		fprintf(stderr, "usage: %s <log level> <message>\n", argv[0]);
		exit(8);
	}
	
	bzero(&sa, sizeof(sa));
	sa.sun_family = AF_UNIX;
	strcpy(sa.sun_path, "/tmp/servmon.log");
	len = SUN_LEN(&sa);

	if ((s = socket(AF_UNIX, SOCK_STREAM, 0)) < 0) {
		perror("socket");
		exit(8);
	}

	if (connect(s, (struct sockaddr *) &sa, len) < 0) {
		perror("connect");
		exit(8);
	}
	
	/*
	 * servmon truncates messages longer than this.
	 */
	if (strlen(argv[2]) > 4096)
		argv[2][4096] = '\0';

	iovec[0].iov_base = argv[1];
	iovec[0].iov_len = strlen(argv[1]);
	iovec[1].iov_base = " ";
	iovec[1].iov_len = 1;
	iovec[2].iov_base = argv[2];
	iovec[2].iov_len = strlen(argv[2]);

	if (writev(s, iovec, 3) < 0) {
		perror("write");
		exit(8);
	}

	exit(0);
}
