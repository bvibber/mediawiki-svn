/*
 * Six degrees of Wikipedia: Server (request handler).
 * This source code is released into the public domain.
 */

#ifndef REQUEST_DISPATCHER_H
#define REQUEST_DISPATCHER_H

#include <cstddef>

#include "work_queue.h"

struct client;
struct pathfinder;
struct request_decoder;

struct request_dispatcher {
	request_dispatcher(pathfinder *);

	void dispatch(client *);

private:
	void handle(client *);
	pathfinder *finder;
	work_queue queue;
};

#endif	/* !REQUEST_DISPATCHER_H */
