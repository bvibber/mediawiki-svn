/*
 * Six degrees of Wikipedia: Server (request handler).
 * This source code is released into the public domain.
 */

#include <cstddef>

struct pathfinder;

struct request_dispatcher {
	request_dispatcher(pathfinder &);

	void dispatch(int);

private:
	pathfinder &finder;

	void handle_request(int s, char *argp, std::size_t argz);
	static void *start_request(void *arg);
};


