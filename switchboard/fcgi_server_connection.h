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

struct fcgi_listener_base;

struct fcgi_server_connection_base : 
	boost::noncopyable,
	boost::enable_shared_from_this<fcgi_server_connection_base> {

	fcgi_server_connection_base(
			sbcontext &context, 
			fcgi_listener_base *lsnr);
	virtual ~fcgi_server_connection_base();

	void	start();
	void	record_to_server(fcgi::recordp record);
	void	destroy(int id);

	virtual fcgi_socket_basep socket() = 0;

private:
	void	handle_record(
			fcgi::recordp record,
			asio::error_code);

	std::vector<fcgi_applicationp> requests_;
	sbcontext &context_;
	fcgi::recordp record_;
	fcgi_listener_base *lsnr_;

	void write_done(asio::error_code error);
	void destroy();
	void delete_me();
	bool alive_;

	log4cxx::LoggerPtr logger;
};

template<typename Protocol>
struct fcgi_server_connection : fcgi_server_connection_base {
	fcgi_server_connection(
		sbcontext &context, 
		fcgi_listener_base *lsnr)
	: fcgi_server_connection_base(context, lsnr)
	, socket_(new fcgi_socket<typename Protocol::socket>(context))
	{
	}

	//boost::shared_ptr<fcgi_socket<typename Protocol::socket> > &socket();
	fcgi_socket_basep socket() {
		return boost::static_pointer_cast<fcgi_socket<typename Protocol::socket> >(socket_);
	}

	boost::shared_ptr<fcgi_socket<typename Protocol::socket> > 
		socket_impl() {
			return socket_;
	}
private:
	boost::shared_ptr<fcgi_socket<typename Protocol::socket> > socket_;
};

typedef boost::shared_ptr<fcgi_server_connection_base>
	fcgi_server_connection_basep;

#endif
