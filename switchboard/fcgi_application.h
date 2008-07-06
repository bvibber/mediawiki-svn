/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef FCGI_APPLICATION_H
#define FCGI_APPLICATION_H

#include	<map>
#include	<string>
#include	<deque>

#include	<boost/asio.hpp>
#include	<boost/array.hpp>
#include	<log4cxx/logger.h>

#include	"fcgi.h"
#include	"sbcontext.h"

struct fcgi_cgi;
struct fcgi_server_connection;

struct fcgi_application {
	fcgi_application(
		int request_id,
		fcgi_server_connection *server,
		sbcontext &context);
	~fcgi_application();

	void	record_from_server(fcgi::recordp record);
	void	record_from_child(fcgi::recordp record);
	void	record_to_server(fcgi::recordp record);

	void	destroy();

private:
	bool buffering_;

	std::map<std::string, std::string> params_;
	std::vector<fcgi::recordp> buffer;
	fcgi_cgi *cgi_;
	fcgi_server_connection *server_;
	sbcontext &context_;
	int request_id_;

	log4cxx::LoggerPtr logger;
};

#endif
