/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#ifndef STATUS_H
#define STATUS_H

#include	<sys/types.h>

#define STATUS_DOOR "trainwreck_door"

#define RQ_PING		1
#define RQ_STATUS	2
#define RQ_STOP		3
#define RQ_START	4

#define RR_OK			1
#define RR_INVALID_QUERY	2

typedef uchar_t status_t;
#define ST_STOPPED		0
#define ST_WAIT_FOR_MASTER	1
#define ST_EXECUTING		2
#define ST_QUEUEING		3
#define ST_WAIT_FOR_ENTRY	4
#define ST_INITIALISING		5


#endif	/* !STATUS_H */
