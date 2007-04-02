/* $Id$ */
/*
 * Six degrees of Wikipedia: Server.
 * This source code is released into the public domain.
 *
 * Linux version, modified to use AF_UNIX socket instead of doors 2006-09-20.
 */

#include <iostream>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <string>
#include <fstream>
#include <sstream>
#include <utility>
#include <cerrno>

#include <boost/function.hpp>
#include <boost/format.hpp>
#include <boost/bind.hpp>
#include <boost/noncopyable.hpp>

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#include <unistd.h>
#include <fcntl.h>

#include "linksc.h"
#include "pathfinder.h"
#include "request_dispatcher.h"
#include "io.h"
#include "client.h"
#include "bdb_adjacency_store.h"
#include "defs.h"

struct client_handler : boost::noncopyable {
	client_handler(pathfinder *);
	
	void run(void);

private:
	void accept_client(void);
	void start_accept(void);
	void read_data(client *);

	poller p;
	request_dispatcher d;
	pathfinder *f;

	int listener;
};

client_handler::client_handler(pathfinder *f_)
	: d(f_)
	, f(f_)
{
	struct sockaddr_in addr;
	int one = 1;

	if ((listener = socket(AF_INET, SOCK_STREAM, 0)) == -1) {
		std::perror("socket");
		std::exit(1);
	}

	setsockopt(listener, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));

	std::memset(&addr, 0, sizeof(addr));
	addr.sin_family = AF_INET;
	addr.sin_addr.s_addr = inet_addr("127.0.0.1");
	addr.sin_port = htons(PORT);

	if (bind(listener, (struct sockaddr *)&addr, sizeof(addr)) == -1) {
		std::perror("bind");
		std::exit(1);
	}

	if (listen(listener, 5) == -1) {
		std::perror("listen");
		std::exit(1);
	}
}

void
client_handler::read_data(client *c)
{
	char buf[1024];
	ssize_t s;

	while ((s = read(c->fd, buf, sizeof buf)) > 0) {
		c->decoder.add_data(buf, s);
		if (c->decoder.error()) {
			delete c;
			return;
		}
	}

	if (s == 0) {
		delete c;
		return;
	}

	if (s == -1) {
		if (errno == EAGAIN) {
			if (c->decoder.finished()) {
				d.dispatch(c);
				return;
			}

			p.read(c->fd, boost::bind(&client_handler::read_data, this, c));
			return;
		}

		delete c;
		return;
	}
}

void
client_handler::run(void)
{
	start_accept();
	p.run();
}

void
client_handler::start_accept(void)
{
	p.read(listener, boost::bind(&client_handler::accept_client, this));
}

void
client_handler::accept_client(void)
{
	int cli;
	struct sockaddr_in cliaddr;
	socklen_t clilen;
	clilen = sizeof(cliaddr);
	std::memset(&cliaddr, 0, clilen);
	if ((cli = accept(listener, (struct sockaddr *)&cliaddr, &clilen)) == -1) {
		std::perror("accept");
		std::exit(1);
	}

	int val;
        val = fcntl(cli, F_GETFL, 0);
	val |= O_NONBLOCK;
	fcntl(cli, F_SETFL, val);

	client *n = new client(cli);
	p.read(n->fd, boost::bind(&client_handler::read_data, this, n));
	start_accept();
}

int
main(int, char *[])
{
	std::ifstream in(CACHE);
	std::string l;

	bdb_adjacency_store aj;

	aj.open(DB, bdb_adjacency_store::read_open);
	if (aj.error()) {
		std::cerr << "opening database: " << aj.strerror() << "\n";
		return 1;
	}

	pathfinder *finder = new pathfinder(aj);

	client_handler ch(finder);
	ch.run();
}
