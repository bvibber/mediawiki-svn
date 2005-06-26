/* $Header$ */

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/wait.h>

#include <stdio.h>
#include <unistd.h>
#include <stdlib.h>
#include <event.h>

#include "bconf/prelude.h"

#define SERVMON PFX "/lib/servmon/exec"
struct event *evs;
struct evbuffer *sm_buf;

static void run(void);
static void sm_read(int, short, void *);

static char **gargv;

int
main(argc, argv)
	int	 argc;
	char	*argv[];
{
	gargv = argv;
	argv[0] = "servmon: exec";
	evs = calloc(sizeof(struct event), getdtablesize());
	event_init();

	for (;;) {
		run();
	}
}

static void
run(void)
{
	pid_t	 pid;
	int	 status;
	int	 lo, hi;
	int	 io[2];

	if (socketpair(AF_UNIX, SOCK_STREAM, 0, io) == -1) {
		perror("pipe");
		exit(1);
	}

	if ((pid = fork()) == -1) {
		perror("fork");
		exit(1);
	}

	if (pid == 0) {
		execv(SERVMON, gargv);
		perror("exec");
		exit(1);
	}

	sm_buf = evbuffer_new();
	event_set(&evs[io[0]], io[0], EV_READ, sm_read, NULL);
	event_dispatch();

	close(io[0]);
	close(io[1]);

	event_del(&evs[io[0]]);

	waitpid(pid, &status, 0);
	lo = status & 0xF;
	hi = status << 8 & 0xF;
	if (lo == 0) {
		fprintf(stderr, "exec exited with exit status %d\n", hi);
		if (hi == 253)
			exit(0);
	} else {
		fprintf(stderr, "exec exited with signal %d\n", lo);
	}
	sleep(5);
}

static void
sm_read(fd, what, arg)
	int	 fd;
	short	 what;
	void	*arg;
{
static	struct timeval	 zero_timeval;
	char		*line;
	int		 i;

	if (what != EV_READ)
		return;

	i = evbuffer_read(sm_buf, fd, -1);
	if (i < 1) {
		event_loopexit(&zero_timeval);
		return;
	}

	line = evbuffer_readline(sm_buf);
	if (line == NULL)
		return;

	switch (*line) {
	case 'C':
		fprintf(stderr, "]]] %s", line + 2);
		break;
	default:
		fprintf(stderr, "SMM: Unknown message from exec: %s", line);
		break;
	}
	free(line);
	return;
}
