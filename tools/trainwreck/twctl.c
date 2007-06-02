/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include	<fcntl.h>
#include	<stdlib.h>
#include	<errno.h>
#include	<string.h>
#include	<stdio.h>
#include	<door.h>

#include	"status.h"

static int rq_ping(void);
static int rq_status(void);
static int rq_stop(void);
static int rq_start(void);

static char const *status_to_name(int);

static int		door;
static door_arg_t	args;

int
main(argc, argv)
	int argc;
	char *argv[];
{
	if (argv[1] == NULL) {
		(void) fprintf(stderr, "usage: twctl <ping | status>\n");
		return 1;
	}

	if ((door = open(STATUS_DOOR, O_RDWR)) == -1) {
		(void) fprintf(stderr, "cannot open door \"%s\": %s\n",
			       STATUS_DOOR, strerror(errno));
		return 1;
	}

	if (!strcmp(argv[1], "ping"))
		return rq_ping();
	else if (!strcmp(argv[1], "status"))
		return rq_status();
	else if (!strcmp(argv[1], "start"))
		return rq_start();
	else if (!strcmp(argv[1], "stop"))
		return rq_stop();
	else {
		(void) fprintf(stderr, "unknown command \"%s\"\n",
			       argv[1]);
		return 1;
	}
}

static int
rq_ping()
{
char	rq = RQ_PING;
	args.data_ptr = &rq;
	args.data_size = 1;
	
	if (door_call(door, &args) == -1) {
		(void) fprintf(stderr, "door_call: \"%s\": %s\n",
			       STATUS_DOOR, strerror(errno));
		exit(1);
	}

	if (args.rsize < 1) {
		(void) fprintf(stderr, "short reply from server\n");
		return 1;
	}

	switch (*args.rbuf) {
	case RR_OK:
		printf("ok\n");
		return 0;

	case RR_INVALID_QUERY:
		printf("invalid query\n");
		return 1;
	
	default:
		printf("unknown response %d\n", (int) *args.rbuf);
		return 1;
	}

	return 0;
}

static int
rq_status()
{
char	rq = RQ_STATUS;
	args.data_ptr = &rq;
	args.data_size = 1;
	
	if (door_call(door, &args) == -1) {
		(void) fprintf(stderr, "door_call: \"%s\": %s\n",
			       STATUS_DOOR, strerror(errno));
		exit(1);
	}

	if (args.rsize < 1) {
		(void) fprintf(stderr, "short reply from server\n");
		return 1;
	}

	switch (*args.rbuf) {
	case RR_OK:
		if (args.rsize < 3) {
			(void) fprintf(stderr, "short reply from server\n");
			return 1;
		}

		printf("reader: %s\n", status_to_name(args.rbuf[1]));
		printf("writer: %s\n", status_to_name(args.rbuf[2]));
		return 0;

	case RR_INVALID_QUERY:
		printf("invalid query\n");
		return 1;
	
	default:
		printf("unknown response %d\n", (int) *args.rbuf);
		return 1;
	}

	return 0;
}

static int
rq_start()
{
char	rq = RQ_START;
	args.data_ptr = &rq;
	args.data_size = 1;
	
	if (door_call(door, &args) == -1) {
		(void) fprintf(stderr, "door_call: \"%s\": %s\n",
			       STATUS_DOOR, strerror(errno));
		exit(1);
	}

	if (args.rsize < 1) {
		(void) fprintf(stderr, "short reply from server\n");
		return 1;
	}

	switch (*args.rbuf) {
	case RR_OK:
		return 0;

	default:
		printf("unknown response %d\n", (int) *args.rbuf);
		return 1;
	}

	return 0;
}

static int
rq_stop()
{
char	rq = RQ_STOP;
	args.data_ptr = &rq;
	args.data_size = 1;
	
	if (door_call(door, &args) == -1) {
		(void) fprintf(stderr, "door_call: \"%s\": %s\n",
			       STATUS_DOOR, strerror(errno));
		exit(1);
	}

	if (args.rsize < 1) {
		(void) fprintf(stderr, "short reply from server\n");
		return 1;
	}

	switch (*args.rbuf) {
	case RR_OK:
		return 0;

	default:
		printf("unknown response %d\n", (int) *args.rbuf);
		return 1;
	}

	return 0;
}

static char const *
status_to_name(st)
	int st;
{
	switch (st) {
	case ST_STOPPED:
		return "stopped";
	case ST_WAIT_FOR_MASTER:
		return "waiting for master to send event";
	case ST_EXECUTING:
		return "executing query on slave";
	case ST_QUEUEING:
		return "queueing event to write thread";
	case ST_WAIT_FOR_ENTRY:
		return "waiting for event from reader";
	case ST_INITIALISING:
		return "initialising";
	default:
		return "unknown";
	}
}
