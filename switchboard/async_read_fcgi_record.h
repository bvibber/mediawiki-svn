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

#include	<asio.hpp>
#include	<boost/function.hpp>
#include	<boost/enable_shared_from_this.hpp>
#include	<log4cxx/logger.h>

#include	"switchboard.h"
#include	"fcgi.h"
#include	"fcgi_socket.h"

namespace async_read_fcgi_record_detail {
	template<typename Socket>
	struct async_fcgi_reader 
		: boost::enable_shared_from_this<async_fcgi_reader<Socket> > {

		async_fcgi_reader(
			Socket socket,
			boost::function<void (fcgi::recordp, 
				asio::error_code)> call);

		~async_fcgi_reader();

		void	start();
		void	read_header_done(
				asio::error_code error,
				std::size_t bytes);
		void	read_data_done(
				asio::error_code error,
				std::size_t bytes);

		void	socket_dead(void);

		Socket socket_;
		boost::function<void (fcgi::recordp, asio::error_code)> call_;
		boost::array<asio::mutable_buffer, 2> data_bufs;
		asio::io_service &service_;
		boost::signals::connection close_conn_;

		fcgi::recordp record;

		bool alive_;

		log4cxx::LoggerPtr logger;
	};

template<typename Socket>
async_fcgi_reader<Socket>::async_fcgi_reader(
		Socket socket,
		boost::function<void (fcgi::recordp, asio::error_code)> call)
	: socket_(socket)
	, call_(call)
	, service_(socket_->socket_impl().get_io_service())
	, record(new fcgi::record)
	, alive_(true)
	, logger(log4cxx::Logger::getLogger("switchboard.async_fcgi_reader"))
{
	LOG4CXX_DEBUG(logger, boost::format("reader@%p: created (sock=%d)") 
			% this % socket_->socket_impl().native());
	assert(socket_->socket_impl().native() != -1);
}

template<typename Socket>
async_fcgi_reader<Socket>::~async_fcgi_reader()
{
	LOG4CXX_DEBUG(logger, boost::format("async_fcgi_reader@%p destructed")
			% this);
	if (close_conn_.connected())
		close_conn_.disconnect();
}

template<typename Socket>
void
async_fcgi_reader<Socket>::socket_dead()
{
	alive_ = false;
	socket_.reset();
	close_conn_.disconnect();
}

template<typename Socket>
void
async_fcgi_reader<Socket>::start()
{
	close_conn_ = socket_->register_close_listener(
		boost::bind(&async_fcgi_reader::socket_dead, this->shared_from_this()));

	/*
	 * The FCGI header is 8 bytes, followed by some content and some 
	 * padding.  We assume the compiler has laid out our record
	 * struct with no padding, which is likely on nearly all systems.
	 *
	 * First read the header.
	 */
	LOG4CXX_DEBUG(logger, boost::format("reader@%p: starting") % this);
	asio::async_read(socket_->socket_impl(), 
			asio::buffer((void *) record.get(), 8),
			asio::transfer_at_least(8),
			boost::bind(&async_fcgi_reader::read_header_done, 
				this->shared_from_this(),
				asio::placeholders::error,
				asio::placeholders::bytes_transferred));
}

template<typename Socket>
void
async_fcgi_reader<Socket>::read_header_done(
		asio::error_code error,
		std::size_t bytes)
{
	if (error == asio::error::operation_aborted) {
		service_.post(boost::bind(call_, fcgi::recordp(),
				asio::error::operation_aborted));
		socket_.reset();
		close_conn_.disconnect();
		return;
	}

	if (!alive_) {
		return;
	}

	assert(socket_->socket_impl().native() != -1);

	if (error) {
		LOG4CXX_DEBUG(logger, boost::format("reader@%p: header read failed: %s")
				% this % error.message());
		service_.post(
			boost::bind(call_, fcgi::recordp(), error));
		socket_.reset();
		close_conn_.disconnect();
		return;
	}

	LOG4CXX_DEBUG(logger, boost::format("reader@%p: header read finished; "
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

	asio::async_read(socket_->socket_impl(), data_bufs, asio::transfer_at_least(datasize),
			boost::bind(&async_fcgi_reader::read_data_done, 
				this->shared_from_this(),
				asio::placeholders::error,
				asio::placeholders::bytes_transferred));
}

template<typename Socket>
void
async_fcgi_reader<Socket>::read_data_done(
	asio::error_code error,
	std::size_t bytes)
{
	if (error == asio::error::operation_aborted) {
		service_.post(boost::bind(call_, fcgi::recordp(),
				asio::error::operation_aborted));
		socket_.reset();
		close_conn_.disconnect();
		return;
	}

	if (!alive_) {
		return;
	}

	assert(socket_->socket_impl().native() != -1);

	LOG4CXX_DEBUG(logger, boost::format("reader@%p, read_data_done bytes=%d expected=%d error=[%s]") 
			% this % bytes
			% (record->content_length()+record->paddingLength)
			% error.message());

	if (error)
		service_.post(boost::bind(call_, fcgi::recordp(), error));
	else
		service_.post(boost::bind(call_, record, error));
	close_conn_.disconnect();
	socket_.reset();
}

} // anonymous namespace

template<typename Socket>
void 
async_read_fcgi_record(
		Socket socket, 
		boost::function<void (fcgi::recordp, asio::error_code error)> call)
{
	using async_read_fcgi_record_detail::async_fcgi_reader;

	boost::shared_ptr<async_fcgi_reader<Socket> > reader(new 
		async_fcgi_reader<Socket>(
			socket, call));
	reader->start();
}

#endif
