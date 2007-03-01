/*
 * Six degrees of Wikipedia: Server (request handler).
 * This source code is released into the public domain.
 */

#ifndef REQUEST_DISPATCHER_H
#define REQUEST_DISPATCHER_H

#include <cstddef>

struct pathfinder;
struct request_decoder;

struct request_dispatcher {
	request_dispatcher(pathfinder *);

	void dispatch(int);

private:
	pathfinder *finder;

	void handle_request(int s, request_decoder &);
	static void *start_request(void *arg);
};

#endif	/* !REQUEST_DISPATCHER_H */
