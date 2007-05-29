/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include	<sys/types.h>
#include	<sys/wait.h>
#include	<string.h>
#include	<errno.h>
#include	<unistd.h>
#include	"tsutils.h"

#define SENDMAIL "/usr/lib/sendmail"

int
sendmail(username, message)
	char const *username, *message;
{
	char const *args[] = {
		"/usr/lib/sendmail",
		"-oi",
		"-bm",
		"--",
		NULL,
		NULL
	};
	int fds[2];
	int status;

	args[4] = username;

	if (pipe(fds) == -1) {
		logmsg("while sending mail: pipe: %s", strerror(errno));
		return -1;
	}

	switch (fork()) {
	case 0:
		if (dup2(fds[0], 0) == -1) {
			logmsg("mail child: dup2: %s", strerror(errno));
			_exit(1);
		}

		(void) close(fds[0]);
		(void) close(fds[1]);
		(void) execv(SENDMAIL, (char *const *) args);
		logmsg("mail child: execv: %s", strerror(errno));
		_exit(1);

	case -1:
		logmsg("sending mail: fork: %s", strerror(errno));
		return -1;

	default:
		(void) close(fds[0]);
		(void) write(fds[1], message, strlen(message));
		(void) close(fds[1]);
	 }

	while (wait(&status) == -1)
		if (errno != EINTR)
			return -1;

	if (WIFEXITED(status)) {
		int ret = WEXITSTATUS(status);
		if (ret != 0) {
			logmsg("sending mail: child exited with status %d", ret);
			return -1;
		}
	} else if (WIFSIGNALED(status)) {
		logmsg("sending mail: child exited with signal %d",
				WTERMSIG(status));
		return -1;
	}
	return 0;
}

