/* $Header$ */

#include <stdio.h>
#include <unistd.h>

#include "bconf/prelude.h"

#define SERVMON PFX "/lib/servmon/exec"

int
main(argc, argv)
	int	 argc;
	char	*argv[];
{
	for (;;) {
		pid_t	pid;
		int	status;
		int	lo, hi;

		if ((pid = fork()) == -1) {
			perror("fork");
			exit(1);
		}

		if (pid == 0) {
			execv(SERVMON, argv);
			perror("exec");
			exit(1);
		}

		waitpid(pid, &status, 0);
		lo = status & 0xF;
		hi = status << 8 & 0xF;
		if (lo == 0) {
			fprintf(stderr, "exec exited with exit status %d\n", hi);
		} else {
			fprintf(stderr, "exec exited with signal %d\n", lo);
		}
	}
}
