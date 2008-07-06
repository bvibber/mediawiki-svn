/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef SWITCHBOARD_H
#define SWITCHBOARD_H

#include	<boost/asio/ip/tcp.hpp>
#include	<boost/asio/buffered_stream.hpp>

typedef boost::asio::buffered_stream<
		boost::asio::ip::tcp::socket>
	buffered_tcp_socket;

#endif
