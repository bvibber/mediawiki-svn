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

#include	<asio/ip/tcp.hpp>
#include	<boost/enable_shared_from_this.hpp>
#include	<log4cxx/logger.h>

#include	"switchboard.h"
#include	"fcgi.h"
#include	"fcgi_application.h"
#include	"sbcontext.h"
#include	"fcgi_socket.h"

struct fcgi_listener;

struct fcgi_server_connection : 
	boost::noncopyable,
	boost::enable_shared_from_this<fcgi_server_connection> {

	fcgi_server_connection(sbcontext &context, fcgi_listener *lsnr);
	~fcgi_server_connection();

	void	start();
	void	record_to_server(fcgi::recordp record);
	void	destroy(int id);

	fcgi_socket_tcpp socket();

private:
	void	handle_record(
			fcgi::recordp record,
			asio::error_code);

	std::vector<fcgi_applicationp> requests_;
	fcgi_socket_tcpp socket_;
	sbcontext &context_;
	fcgi::recordp record_;
	fcgi_listener *lsnr_;

	void write_done(asio::error_code error);
	void destroy();
	void delete_me();
	bool alive_;

	log4cxx::LoggerPtr logger;
};

typedef boost::shared_ptr<fcgi_server_connection>
	fcgi_server_connectionp;

#endif
