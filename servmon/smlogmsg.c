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
	char 			*logmsg;
	
	if (argc != 3) {
		fprintf(stderr, "usage: %s <log level> <message>\n", argv[0]);
		exit(8);
	}
	
	memset(&sa, 0, sizeof(sa));
	sa.sun_family = AF_UNIX;
	strcpy(sa.sun_path, "/tmp/servmon.log");
	len = SUN_LEN(&sa);

	if ((s = socket(AF_UNIX, SOCK_STREAM, 0)) < 0) {
		perror("socket");
		exit(8);
	}

	if (connect(s, (struct sockaddr *)&sa, len) < 0) {
		perror("connect");
		exit(8);
	}
	
	logmsg = malloc(strlen(argv[1]) + strlen(argv[2]) + 2);
	sprintf(logmsg, "%s %s", argv[1], argv[2]);
	if (write(s, logmsg, strlen(logmsg)) < 0) {
		perror("write");
		exit(8);
	}
	exit(0);
}
