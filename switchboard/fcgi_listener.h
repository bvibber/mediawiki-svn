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

#include	<boost/asio.hpp>
#include	<boost/noncopyable.hpp>

#include	<log4cxx/logger.h>

#include	"sbcontext.h"
#include	"fcgi_server_connection.h"

struct fcgi_server_connection;

struct fcgi_listener : boost::noncopyable {
	fcgi_listener(sbcontext &context, boost::asio::ip::tcp::endpoint const &endpoint);

	void close(int);

private:
	void handle_accept(fcgi_server_connectionp, boost::system::error_code);

	sbcontext &context_;
	boost::asio::ip::tcp::socket socket_;
	boost::asio::ip::tcp::acceptor acceptor_;

	std::map<int, fcgi_server_connectionp> connections_;

	log4cxx::LoggerPtr logger;
};

#endif
