/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef FCGI_SERVER_CONNECTION_H
#define FCGI_SERVER_CONNECTION_H

#include	<vector>

#include	<boost/asio/ip/tcp.hpp>
#include	<boost/asio/buffered_stream.hpp>
#include	<log4cxx/logger.h>

#include	"switchboard.h"
#include	"fcgi.h"
#include	"fcgi_application.h"
#include	"sbcontext.h"
#include	"fcgi_record_writer.h"

struct fcgi_server_connection {
	fcgi_server_connection(sbcontext &context);
	~fcgi_server_connection();

	void	start();
	void	record_to_server(fcgi::recordp record);
	void	destroy(int id);

	buffered_tcp_socket &socket();

private:
	void	handle_record(fcgi::recordp record);

	std::vector<fcgi_application *> requests_;
	buffered_tcp_socket socket_;
	sbcontext &context_;
	boost::system::error_code error_;
	fcgi::recordp record_;
	fcgi_record_writer writer_;

	void writer_error(boost::system::error_code const &error);
	void destroy();
	void delete_me();
	bool alive_;

	log4cxx::LoggerPtr logger;
};

#endif
