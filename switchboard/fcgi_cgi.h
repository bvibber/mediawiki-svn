/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef FCGI_CGI_H
#define FCGI_CGI_H

#include	<stdexcept>
#include	<deque>

#include	<boost/asio.hpp>
#include	<boost/array.hpp>
#include	<log4cxx/logger.h>

#include	"switchboard.h"
#include	"fcgi.h"
#include	"sbcontext.h"
#include	"process.h"
#include	"fcgi_record_writer.h"

struct fcgi_application;

struct cgi_startup_error : std::runtime_error {
	cgi_startup_error(char const *s) : std::runtime_error(s) {};
};

/*
 * A FastCGI program we spawned, most likely php-cgi.
 */
struct fcgi_cgi {
	fcgi_cgi(
		int request_id_,
		sbcontext &context,
		fcgi_application *app, 
		fcgi::params const &params);
	~fcgi_cgi();

	void record(fcgi::recordp record);
	void record_noflush(fcgi::recordp record);
	void flush();

	void destroy();

private:
	void writer_error(boost::system::error_code const &error);
	void handle_child_read(fcgi::recordp record);

	sbcontext &context_;
	buffered_tcp_socket child_socket_;
	boost::system::error_code child_read_error_;
	fcgi_application *app_;
	processp process_;

	fcgi_record_writer writer_;
	int request_id_;

	void delete_me();

	log4cxx::LoggerPtr logger;
};

#endif
