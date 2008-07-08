/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<boost/function.hpp>
#include	<boost/bind.hpp>
#include	<boost/format.hpp>
using boost::format;

#include	"fcgi_listener.h"
#include	"fcgi_server_connection.h"

namespace asio = boost::asio;
using asio::ip::tcp;
using boost::shared_ptr;
using boost::function;

fcgi_listener::fcgi_listener(
		sbcontext &context,
		asio::ip::tcp::endpoint const &endpoint)
	: context_(context)
	, socket_(context.service())
	, acceptor_(context.service(), endpoint)
	, logger(log4cxx::Logger::getLogger("switchboard.fcgi_listener"))

{
	acceptor_.set_option(tcp::acceptor::reuse_address(true));
	fcntl(acceptor_.native(), F_SETFD, FD_CLOEXEC);
	fcgi_server_connectionp new_connection(new fcgi_server_connection(context_, this));
	acceptor_.async_accept(new_connection->socket(),
			boost::bind(&fcgi_listener::handle_accept, this,
				new_connection, boost::asio::placeholders::error));

}

void
fcgi_listener::handle_accept(
		fcgi_server_connectionp connection,
		boost::system::error_code error)
{
	fcgi_server_connectionp new_connection(
			new fcgi_server_connection(context_, this));
	acceptor_.async_accept(new_connection->socket(),
			boost::bind(&fcgi_listener::handle_accept, this,
				new_connection, boost::asio::placeholders::error));

	if (error) {
		LOG4CXX_ERROR(logger,
			format("error during accept: %s")
			% error.message());
		return;
	}

	connections_[connection->socket().native()] = connection;
	fcntl(connection->socket().native(), F_SETFD, FD_CLOEXEC);
	connection->socket().set_option(tcp::no_delay(true));
	connection->start();
}

void
fcgi_listener::close(int fd)
{
	std::map<int, fcgi_server_connectionp>::iterator it;
	if ((it = connections_.find(fd)) == connections_.end()) {
		LOG4CXX_DEBUG(logger,
			format("cannot find connection at %d to destroy!")
			% fd);
		return;
	}

	connections_.erase(it);
}
