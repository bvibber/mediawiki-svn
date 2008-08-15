/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<sys/types.h>

#include	<fcntl.h>
#include	<stdio.h>
#include	<unistd.h>
#include	<syslog.h>
#include	<stdlib.h>

#define BINPATH PREFIX "/lib/switchboard/switchboard-bin"

int
main(argc, argv)
	char **argv;
{
	openlog("switchboard", LOG_PID, LOG_DAEMON);

	switch (fork()) {
	case -1:
		syslog(LOG_CRIT, "cannot fork: %m");
		return 1;
	case 0:
		break;
	default:
		return 0;
	}

	setsid();
	chdir("/");
	int fd;
	if ((fd = open("/dev/null", O_RDWR, 0)) != -1) {
		dup2(fd, STDIN_FILENO);
		dup2(fd, STDOUT_FILENO);
		dup2(fd, STDERR_FILENO);
		if (fd > STDERR_FILENO)
			close(fd);
	}

	for (;;) {
		pid_t pid;
		switch (pid = fork()) {
		case -1:
			syslog(LOG_CRIT, "cannot fork: %m");
			exit(1);

		case 0:
			execl(BINPATH, "switchboard", "-d", NULL);
			_exit(254);

		default:
			break;
		}

		int stat;
		waitpid(pid, &stat, 0);

		if (WIFEXITED(stat)) {
			syslog(LOG_INFO, "child %d exited with status %d",
					pid, WEXITSTATUS(stat));
			if (WEXITSTATUS(stat) == 0)
				return 0;
		}

		if (WIFSIGNALED(stat))
			syslog(LOG_INFO, "child %d exited with signal %d",
					pid, WTERMSIG(stat));

		sleep(1);
	}
}
