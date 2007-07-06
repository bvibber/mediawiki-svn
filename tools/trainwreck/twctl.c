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

#include	<my_global.h>
#include	"status.h"

static int rq_ping(void);
static int rq_status(void);
static int rq_stop(void);
static int rq_start(void);
static int rq_shutdown(void);

static char const *status_to_name(int);
static char const *format_seconds(uint32_t nsecs);

static int		door;
static door_arg_t	args;

int
main(argc, argv)
	int argc;
	char *argv[];
{
	if (argv[1] == NULL) {
		(void) fprintf(stderr, "usage: twctl <ping | status | start | stop | shutdown>\n");
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
	else if (!strcmp(argv[1], "shutdown"))
		return rq_shutdown();
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
rq_shutdown()
{
char	rq = RQ_SHUTDOWN;
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
char		 rq = RQ_STATUS;
uint32_t	 logpos, logtime;
char		*logname;
int		 rstat;
char		*wstats;
int		 nwstats, i, j;

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

		rstat = args.rbuf[1];
		wstats = args.rbuf + 2;
		nwstats = args.rsize - 2;
		break;

	case RR_INVALID_QUERY:
		printf("invalid query\n");
		return 1;
	
	default:
		printf("unknown response %d\n", (int) *args.rbuf);
		return 1;
	}

	(void) printf("reader: %s\n", status_to_name(rstat));
	memset(&args, 0, sizeof(args));

	rq = RQ_READER_POSITION;
	args.data_ptr = &rq;
	args.data_size = 1;

	if (door_call(door, &args) == -1) {
		(void) fprintf(stderr, "door_call: \"%s\": %s\n",
			       STATUS_DOOR, strerror(errno));
		exit(1);
	}

	if (args.rsize > 0) {
		switch (*args.rbuf) {
		case RR_OK:
			if (args.rsize < 6) {
				(void) fprintf(stderr, "short reply from server\n");
				return 1;
			}

			logpos = uint4korr(args.rbuf + 1);
			logname = args.rbuf + 5;
			(void) printf("    log position: %.*s,%lu\n",
				      args.rsize - 4, logname,
				      (unsigned long) logpos);
			break;
		
		case RR_INVALID_QUERY:
			printf("invalid query\n");
			return 1;
	
		default:
			printf("unknown response %d\n", (int) *args.rbuf);
			return 1;
		}
	}

	rq = RQ_WRITER_POSITION;
	munmap(args.rbuf, args.rsize);
	memset(&args, 0, sizeof(args));
	args.data_ptr = &rq;
	args.data_size = 1;

	if (door_call(door, &args) == -1) {
		(void) fprintf(stderr, "door_call: \"%s\": %s\n",
			       STATUS_DOOR, strerror(errno));
		exit(1);
	}

	if (args.rsize > 0) {
		switch (*args.rbuf) {
		case RR_OK:
			++args.rbuf;
			j = *args.rbuf;
			++args.rbuf;
			i = 0;
			while (j--) {
			uint16_t	loglen;
				(void) printf("writer-%d: %s\n", i, status_to_name(wstats[i]));

				logpos = uint4korr(args.rbuf);
				if (logpos == 0) {
					args.rbuf += 4;
					continue;
				}

				logtime = uint4korr(args.rbuf + 4);
				loglen = uint2korr(args.rbuf + 8);
				(void) printf("    log position: %.*s,%lu\n",
					      loglen, args.rbuf + 10,
					      (unsigned long) logpos);
				if (logtime != 0) {
				time_t	now = time(NULL);
					(void) printf("    seconds behind master: %s\n",
							format_seconds(now - logtime));
				}
				args.rbuf += 6 + loglen;
				i++;
			}
			break;
		
		case RR_INVALID_QUERY:
			printf("invalid query\n");
			return 1;
		
		default:
			printf("unknown response %d\n", (int) *args.rbuf);
			return 1;
		}
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

static char const *
format_seconds(t)
	uint32_t t;
{
static char	buf[512], *p = buf;
int		weeks, days, hours, minutes, seconds;
	weeks = t / (60 * 60 * 24 * 7);
	t %= (60 * 60 * 24 * 7);
	days = t / (60 * 60 * 24);
	t %= (60 * 60 * 24);
	hours = t / (60 * 60);
	t %= (60 * 60);
	minutes = t / 60;
	t %= 60;
	seconds = t;

	if (weeks)
		p += sprintf(p, " %dw", weeks);
	if (days)
		p += sprintf(p, " %dd", days);
	if (hours)
		p += sprintf(p, " %dh", hours);
	if (minutes)
		p += sprintf(p, " %dm", minutes);
	sprintf(p, " %ds", seconds);
	return buf + 1;
}
