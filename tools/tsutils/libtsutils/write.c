/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include	<sys/types.h>
#include	<sys/stat.h>
#include	<string.h>
#include	<fcntl.h>
#include	<stdio.h>
#include	<utmpx.h>
#include	<unistd.h>

int
get_user_tty(user)
	char const *user;
{
struct utmpx	*ut;
char		 ttydev[32];
struct stat	 st;
time_t		 idle = 0;
int		 fd = -1;

	setutxent();
	while ((ut = getutxent()) != NULL) {
	int	tmp;
		if (ut->ut_type != USER_PROCESS)
			continue;

		if (strcmp(ut->ut_user, user))
			continue;

		(void) snprintf(ttydev, sizeof(ttydev), "/dev/%s", ut->ut_line);
		if ((tmp = open(ttydev, O_WRONLY)) == -1)
			continue;

		if (fstat(tmp, &st) == -1) {
			(void) close(tmp);
			continue;
		}

		if (st.st_atime > idle) {
			fd = tmp;
			idle = st.st_atime;
		} else {
			(void) close(tmp);
		}
	}

	return fd;
}

#ifdef TEST
int
main() {
	sleep(5);
	int i = get_user_tty("river");
	if (i == -1) {
		perror("get_user_tty");
		return 0;
	}
#define MESSAGE "\n\ntesting!\n\n"
	write(i, MESSAGE, sizeof(MESSAGE) - 1);
	close(i);
	return 0;
}
#endif	/* TEST */
