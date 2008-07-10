/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef FCGI_LISTENER_H
#define FCGI_LISTENER_H

#include	<asio.hpp>
#include	<boost/noncopyable.hpp>
#include	<boost/multi_index_container.hpp>
#include	<boost/multi_index/ordered_index.hpp>


#include	<log4cxx/logger.h>

#include	"sbcontext.h"
#include	"fcgi_server_connection.h"

struct fcgi_listener_base {
	virtual void close(fcgi_server_connection_basep) = 0;
};

template<typename Protocol>
struct fcgi_listener :
	boost::noncopyable,
	fcgi_listener_base {

	fcgi_listener(
		sbcontext &context, 
		typename Protocol::endpoint const &endpoint);

	void close(fcgi_server_connection_basep);

private:
	void handle_accept(
		boost::shared_ptr<fcgi_server_connection<Protocol> >, 
		asio::error_code);

	sbcontext &context_;
	typename Protocol::socket socket_;
	typename Protocol::acceptor acceptor_;

	boost::multi_index_container<fcgi_server_connection_basep> connections_;

	log4cxx::LoggerPtr logger;
};

template<typename Protocol>
fcgi_listener<Protocol>::fcgi_listener(
		sbcontext &context,
		typename Protocol::endpoint const &endpoint)
	: context_(context)
	, socket_(context.service())
	, acceptor_(context.service(), endpoint)
	, logger(log4cxx::Logger::getLogger("switchboard.fcgi_listener"))

{
	acceptor_.set_option(typename Protocol::acceptor::reuse_address(true));
	fcntl(acceptor_.native(), F_SETFD, FD_CLOEXEC);
	boost::shared_ptr<fcgi_server_connection<Protocol> >
		 new_connection(
			new fcgi_server_connection<Protocol>(context_, this));
	acceptor_.async_accept(new_connection->socket_impl()->socket_impl(),
			boost::bind(&fcgi_listener::handle_accept, this,
				new_connection, asio::placeholders::error));

}

template<typename Protocol>
void
fcgi_listener<Protocol>::handle_accept(
		boost::shared_ptr<fcgi_server_connection<Protocol> > connection,
		asio::error_code error)
{
	boost::shared_ptr<fcgi_server_connection<Protocol> >
		new_connection(
			new fcgi_server_connection<Protocol>(context_, this));
	acceptor_.async_accept(new_connection->socket_impl()->socket_impl(),
			boost::bind(&fcgi_listener::handle_accept, this,
				new_connection, asio::placeholders::error));

	if (error) {
		LOG4CXX_ERROR(logger,
			boost::format("error during accept: %s")
			% error.message());
		return;
	}

	connections_.insert(boost::static_pointer_cast<fcgi_server_connection_base>(connection));
	fcntl(connection->socket_impl()->socket_impl().native(), F_SETFD, FD_CLOEXEC);
	//connection->socket()->socket().set_option(tcp::no_delay(true));
	connection->start();
}

template<typename Protocol>
void
fcgi_listener<Protocol>::close(fcgi_server_connection_basep conn)
{
	boost::multi_index_container<fcgi_server_connection_basep>::iterator it;
	if ((it = connections_.find(conn)) == connections_.end()) {
		LOG4CXX_DEBUG(logger,
			boost::format("cannot find connection at %p to destroy!")
			% conn);
		return;
	}

	LOG4CXX_DEBUG(logger,
		boost::format("erased connection %p") % conn);
	connections_.erase(it);
}

#endif
