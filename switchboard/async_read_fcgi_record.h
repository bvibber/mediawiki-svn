/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef ASYNC_READ_FCGI_RECORD_H
#define ASYNC_READ_FCGI_RECORD_H

#include	<boost/asio.hpp>
#include	<boost/function.hpp>

#include	"switchboard.h"
#include	"fcgi.h"

	void	async_read_fcgi_record(
			buffered_tcp_socket &, 
			boost::system::error_code &,
			boost::function<void (fcgi::recordp)>);

#endif
