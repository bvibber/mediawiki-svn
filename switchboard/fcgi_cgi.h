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

#include	<asio.hpp>
#include	<boost/array.hpp>
#include	<boost/shared_ptr.hpp>
#include	<boost/enable_shared_from_this.hpp>
#include	<log4cxx/logger.h>

#include	"switchboard.h"
#include	"fcgi.h"
#include	"sbcontext.h"
#include	"process.h"
#include	"fcgi_socket.h"
#include	"process_factory.h"

struct fcgi_application;
typedef boost::shared_ptr<fcgi_application> fcgi_applicationp;

struct cgi_startup_error : std::runtime_error {
	cgi_startup_error(char const *s) : std::runtime_error(s) {};
};

/*
 * A FastCGI program we spawned, most likely php-cgi.
 */
struct fcgi_cgi : boost::enable_shared_from_this<fcgi_cgi> {
	fcgi_cgi(
		int request_id_,
		sbcontext &context,
		fcgi_applicationp app, 
		fcgi::params &params);
	~fcgi_cgi();

	void start(boost::function<void (void)>);
	void record(fcgi::recordp record);
	void record_noflush(fcgi::recordp record);
	void flush();
	void close();

	void destroy();

private:
	void write_done(asio::error_code error);
	void connect_done(
		asio::error_code error,
		boost::function<void (void)>);
	void handle_child_read(fcgi::recordp record, asio::error_code);
	void get_process();
	void process_ready(
		boost::function<void (void)>,
		processp);

	sbcontext &context_;
	fcgi_socket_unixp child_socket_;
	fcgi_applicationp app_;
	processp process_;
	process_ref ref_;

	int request_id_;
	bool alive_;
	std::string script_path_;

	log4cxx::LoggerPtr logger;
};

typedef boost::shared_ptr<fcgi_cgi> fcgi_cgip;

#endif
