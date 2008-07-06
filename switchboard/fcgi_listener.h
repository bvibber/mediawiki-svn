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

#include	"sbcontext.h"

struct fcgi_server_connection;

struct fcgi_listener {
	fcgi_listener(sbcontext &context, boost::asio::ip::tcp::endpoint const &endpoint);

private:
	void handle_accept(fcgi_server_connection *, const boost::system::error_code &);

	sbcontext &context;
	boost::asio::ip::tcp::socket socket;
	boost::asio::ip::tcp::acceptor acceptor;

	fcgi_listener(fcgi_listener const *);
};

#endif
