/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<boost/bind.hpp>
#include	<boost/format.hpp>
#include	<log4cxx/logger.h>

#include	"async_read_fcgi_record.h"

namespace asio = boost::asio;
using asio::ip::tcp;
using boost::shared_ptr;
using boost::function;
using boost::format;

namespace {
	struct async_fcgi_reader {
		async_fcgi_reader(
				buffered_tcp_socket &socket,
				boost::system::error_code &error,
				function<void (fcgi::recordp)> call);

		void	start();
		void	read_header_done(
				const boost::system::error_code &error,
				std::size_t bytes);
		void	read_data_done(
				const boost::system::error_code &error,
				std::size_t bytes);

		buffered_tcp_socket &socket_;
		boost::system::error_code &error_;
		function<void (fcgi::recordp)> call_;
		boost::array<asio::mutable_buffer, 2> data_bufs;
		asio::io_service &service_;

		fcgi::recordp record;

		log4cxx::LoggerPtr logger;
	};

async_fcgi_reader::async_fcgi_reader(
		buffered_tcp_socket &socket,
		boost::system::error_code &error,
		function<void (fcgi::recordp)> call)
	: socket_(socket)
	, error_(error)
	, call_(call)
	, service_(socket.get_io_service())
	, record(new fcgi::record)
	, logger(log4cxx::Logger::getLogger("switchboard.async_fcgi_reader"))
{
	LOG4CXX_DEBUG(logger, format("reader@%p: created (sock=%d)") 
			% this % socket_.next_layer().native());
}

void
async_fcgi_reader::start()
{
	/*
	 * The FCGI header is 8 bytes, followed by some content and some 
	 * padding.  We assume the compiler has laid out our record
	 * struct with no padding, which is likely on nearly all systems.
	 *
	 * First read the header.
	 */
	LOG4CXX_DEBUG(logger, format("reader@%p: starting") % this);
	error_ = boost::system::error_code();
	asio::async_read(socket_, 
			asio::buffer((void *) record.get(), 8),
			asio::transfer_at_least(8),
			boost::bind(&async_fcgi_reader::read_header_done, this,
				asio::placeholders::error,
				asio::placeholders::bytes_transferred));
}

void
async_fcgi_reader::read_header_done(
		const boost::system::error_code &error,
		std::size_t bytes)
{
	if (error == asio::error::operation_aborted) {
		delete this;
		return;
	}

	if (error) {
		LOG4CXX_DEBUG(logger, format("reader@%p: header read failed: %s")
				% this % error.message());
		error_ = error;
		service_.post(
			boost::bind(call_, fcgi::recordp()));
		delete this;
		return;
	}

	LOG4CXX_DEBUG(logger, format("reader@%p: header read finished; "
			"content=%d padding=%d") 
			% this
			% record->content_length()
			% (int) record->paddingLength);
	assert(bytes == 8);

	/*
	 * We've read the header.  Now we read however much data is
	 * present.  We don't care about the type of request, or if
	 * it's even valid.
	 *
	 * We read the padding data here, even though we don't care about
	 * it - it's only <256 bytes.
	 */
	record->contentData.resize(record->content_length());
	record->paddingData.resize(record->paddingLength);
	std::size_t datasize = record->content_length() + record->paddingLength;

	data_bufs[0] = asio::buffer(record->contentData);
	data_bufs[1] = asio::buffer(record->paddingData);

	asio::async_read(socket_, data_bufs, asio::transfer_at_least(datasize),
			boost::bind(&async_fcgi_reader::read_data_done, this,
				asio::placeholders::error,
				asio::placeholders::bytes_transferred));
}

void
async_fcgi_reader::read_data_done(
	const boost::system::error_code &error,
	std::size_t bytes)
{
	LOG4CXX_DEBUG(logger, format("reader@%p, read_data_done bytes=%d expected=%d error=[%s]") 
			% this % bytes
			% (record->content_length()+record->paddingLength)
			% error.message());
	if (error == asio::error::operation_aborted) {
		delete this;
		return;
	}

	if (error)
		service_.post(boost::bind(call_, fcgi::recordp()));
	else
		service_.post(boost::bind(call_, record));
	delete this;
}

} // anonymous namespace

void 
async_read_fcgi_record(
		buffered_tcp_socket &socket, 
		boost::system::error_code &error,
		function<void (fcgi::recordp)> call)
{
	async_fcgi_reader *reader = new async_fcgi_reader(
			socket, error, call);
	reader->start();
}

