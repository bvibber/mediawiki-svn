/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef FCGI_RECORD_WRITER_H
#define FCGI_RECORD_WRITER_H

#include	<vector>

#include	<boost/asio/ip/tcp.hpp>
#include	<boost/asio/buffer.hpp>
#include	<boost/system/error_code.hpp>
#include	<boost/function.hpp>
#include	<boost/noncopyable.hpp>
#include	<boost/shared_ptr.hpp>
#include	<boost/enable_shared_from_this.hpp>
#include	<log4cxx/logger.h>

#include	"switchboard.h"
#include	"fcgi.h"

struct sbcontext;

namespace record_writer_detail {
	struct pending_record {
		fcgi::recordp record;
		boost::array<boost::asio::mutable_buffer, 3> data;
	};
}

struct fcgi_record_writer : 
	boost::noncopyable, boost::enable_shared_from_this<fcgi_record_writer> {

	fcgi_record_writer(
		sbcontext &context,
		boost::asio::ip::tcp::socket &socket,
		boost::function<void (boost::system::error_code)> errorfunc);

	void write(fcgi::recordp record);
	void write_noflush(fcgi::recordp record);
	void flush();

private:
	sbcontext &context_;
	boost::asio::ip::tcp::socket &socket_;
	std::vector<record_writer_detail::pending_record> inflight_;
	std::vector<record_writer_detail::pending_record> waiting_;
	std::vector<boost::asio::mutable_buffer> buffers_;
	boost::function<void (boost::system::error_code)> errorfunc_;

	void write_done(
		boost::system::error_code error,
		std::size_t bytes);
	void flush_done(boost::system::error_code error);

	template<typename InputIterator>
	void write(InputIterator begin, InputIterator end)
	{
		for (; begin != end; ++begin)
			write_noflush(begin);
	}

	log4cxx::LoggerPtr logger;
};

typedef boost::shared_ptr<fcgi_record_writer> fcgi_record_writerp;

#endif
