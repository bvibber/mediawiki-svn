/* Six degrees of Wikipedia						*/
/* Copyright (c) 2005-2007 River Tarnell <river@attenuate.org>.		*/
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/*
 * Server core: sets up client listener/dispatcher and creates adjacency store.
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
#include <boost/shared_ptr.hpp>

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <netdb.h>

#include <unistd.h>
#include <fcntl.h>

#include "linksc.h"
#include "pathfinder.h"
#include "request_dispatcher.h"
#include "io.h"
#include "client.h"
#include "bdb_adjacency_store.h"
#include "defs.h"
#include "log.h"
#include "radix.h"
#include "access.h"

/*
 * Client handler registers listeners and hands new client connections to the 
 * request dispatcher.
 */
struct client_handler : boost::noncopyable {
	client_handler(pathfinder *);
	
	void allow(std::string const &addr);
	void listen(std::string const &addr, std::string const &port);
	void run(void);

private:
	void accept_client(int s);
	void start_accept(int s);
	void read_data(client *);

	poller p;
	request_dispatcher d;
	pathfinder *f;
	access_list acc;

	std::vector<int> listeners;
};

client_handler::client_handler(pathfinder *f_)
	: d(f_)
	, f(f_)
{
}

void
client_handler::allow(std::string const &addr)
{
	acc.allow(addr);
}

void
client_handler::listen(std::string const &host, std::string const &port)
{
	addrinfo hints, *res;
	int r, one = 1;

	std::memset(&hints, 0, sizeof(hints));
	hints.ai_socktype = SOCK_STREAM;
	hints.ai_flags = AI_PASSIVE;

	if ((r = getaddrinfo(host.c_str(), port.c_str(), &hints, &res)) != 0) {
		logger::error(str(boost::format("resolving %s:%s: %s") 
					% host % port % gai_strerror(r)));
		return;
	}

	for (addrinfo *r = res; r; r = r->ai_next) {
		int s;

		if ((s = socket(r->ai_family, r->ai_socktype, r->ai_protocol)) == -1) {
			logger::error(str(boost::format("adding listener %s:%s: %s") 
					% host % port % std::strerror(errno)));
			continue;
		}

		setsockopt(s, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));

		if (bind(s, r->ai_addr, r->ai_addrlen) == -1) {
			logger::error(str(boost::format("binding listener %s:%s: %s")
					% host % port % std::strerror(errno)));
			close(s);
			continue;
		}

		if (::listen(s, 5) == -1) {
			logger::error(str(boost::format("listening %s:%s: %s")
					% host % port % std::strerror(errno)));
			close(s);
			continue;
		}

		logger::info(str(boost::format("listening on %s:%s")
				% host % port));
		listeners.push_back(s);
		start_accept(s);
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
	p.run();
}

void
client_handler::start_accept(int s)
{
	p.read(s, boost::bind(&client_handler::accept_client, this, s));
}

void
client_handler::accept_client(int s)
{
	int		 cli;
	sockaddr_storage cliaddr;
	socklen_t	 clilen;
	char		 name[NI_MAXHOST];

	clilen = sizeof(cliaddr);
	std::memset(&cliaddr, 0, clilen);

	if ((cli = accept(s, (struct sockaddr *)&cliaddr, &clilen)) == -1) {
		std::perror("accept");
		start_accept(s);
		return;
	}

	getnameinfo((sockaddr *) &cliaddr, clilen, name, sizeof name, 0, 0, NI_NUMERICHOST);

	if (!acc.allowed((sockaddr *) &cliaddr).first) {
		logger::info(str(boost::format("client %s is disallowed") % name));
		close(cli);
		start_accept(s);
		return;
	}

	logger::info(str(boost::format("accept client from %s") % name));

	int val;
        val = fcntl(cli, F_GETFL, 0);
	val |= O_NONBLOCK;
	fcntl(cli, F_SETFL, val);

	client *n = new client(cli);
	p.read(n->fd, boost::bind(&client_handler::read_data, this, n));
	start_accept(s);
}

int
main(int argc, char *argv[])
{
	std::vector<std::pair<std::string, std::string> > listeners;
	std::vector<std::string> sources;
	int c;

	while ((c = getopt(argc, argv, "l:s:")) != -1) {
		std::string host, port;
		std::string::size_type i;

		switch (c) {
		case 'l':
			host = optarg;
			if ((i = host.find('/')) != std::string::npos) {
				port = host.substr(i + 1);
				host = host.substr(0, i);
			}

			listeners.push_back(std::make_pair(host, port));
			break;

		case 's':
			sources.push_back(optarg);
			break;

		default:
			std::exit(1);
		}
	}

	if (listeners.empty())
		listeners.push_back(std::make_pair("127.0.0.1", "6534"));

	bdb_adjacency_store aj;

	aj.open(DB, bdb_adjacency_store::read_open);
	if (aj.error()) {
		std::cerr << "opening database: " << aj.strerror() << "\n";
		return 1;
	}

	pathfinder *finder = new pathfinder(aj);

	client_handler ch(finder);
	for (std::size_t i = 0, end = listeners.size(); i < end; ++i)
		ch.listen(listeners[i].first, listeners[i].second);
	for (std::size_t i = 0, end = sources.size(); i < end; ++i)
		try {
			ch.allow(sources[i]);
		} catch (invalid_prefix &p) {
			logger::error(str(boost::format("%s: %s") % sources[i] % p.what()));
			return 1;
		}

	ch.run();
}
