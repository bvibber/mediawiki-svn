/*
 * Six degrees of Wikipedia: Server.
 * This source code is released into the public domain.
 *
 * Linux version, modified to use AF_UNIX socket instead of doors 2006-09-20.
 */

#pragma ident "@(#)linksd.cc	1.3 07/01/24 14:14:48"

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/un.h>

#include <iostream>
#include <map>
#include <list>
#include <set>
#include <vector>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <cassert>
#include <queue>
#include <string>
#include <algorithm>
#include <utility>
#include <fstream>
#include <sstream>
#include <exception>

#include <unistd.h>
#include <mysql.h>
#include <fcntl.h>

#include "linksc.h"
#include "pathfinder.h"
#include "request_dispatcher.h"

int
main(int argc, char *argv[])
{

	std::ifstream in(CACHE);
	std::string l;
	std::printf("retrieving links table...\n");

	pathfinder finder;

	while (std::getline(in, l)) {
		if (l.empty())
			break;
		int from, to;
		std::istringstream str(l);
		str >> from >> to;
		finder.add_adjacency(from, to);
	}

	std::printf("ok\n");
	std::printf("retrieving titles...\n");
	while (std::getline(in, l)) {
		int id;
		std::string ttl;
		std::istringstream str(l);
		str >> id;
		std::getline(str, ttl);
		while (!ttl.empty() && ttl[0] == ' ')
			ttl.erase(ttl.begin());
		finder.add_title(ttl, id);
	}
	std::printf("filtering links...\n");
	finder.filter();
	std::printf("ok\n");

	request_dispatcher dispatcher(finder);

	int did;
	struct sockaddr_un addr;

	if ((did = socket(AF_LOCAL, SOCK_STREAM, 0)) == -1) {
		std::perror("socket");
		std::exit(1);
	}
	unlink(DOOR);
	std::memset(&addr, 0, sizeof(addr));
	addr.sun_family = AF_LOCAL;
	strncpy(addr.sun_path, DOOR, sizeof(addr.sun_path));
	if (bind(did, (struct sockaddr *)&addr, sizeof(addr)) == -1) {
		std::perror("bind");
		std::exit(1);
	}

	if (listen(did, 5) == -1) {
		std::perror("listen");
		std::exit(1);
	}

	for (;;) {
	int			cli;
	struct sockaddr_un	cliaddr;
	size_t			clilen;
		clilen = sizeof(addr);
		std::memset(&cliaddr, 0, clilen);
		if ((cli = accept(did, (struct sockaddr *)&cliaddr, (socklen_t *)&clilen)) == -1) {
			std::perror("accept");
			std::exit(1);
		}

		dispatcher.dispatch(cli);
	}
	close(did);
}
