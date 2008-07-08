/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<boost/asio.hpp>
#include	<boost/bind.hpp>
#include	<boost/format.hpp>

#include	"fcgi_server_connection.h"
#include	"fcgi_listener.h"
#include	"async_read_fcgi_record.h"

namespace asio = boost::asio;
using asio::ip::tcp;
using boost::format;

fcgi_server_connection::fcgi_server_connection(
		sbcontext &context,
		fcgi_listener *lsnr)
	: socket_(context.service())
	, context_(context)
	, alive_(true)
	, lsnr_(lsnr)
	, logger(log4cxx::Logger::getLogger("switchboard.fcgi_server_connection"))
{
}

fcgi_server_connection::~fcgi_server_connection()
{
	LOG4CXX_DEBUG(logger, format("destructing connection at %p") % this);
}

void
fcgi_server_connection::destroy(int r)
{
	LOG4CXX_DEBUG(logger, format("destroying request #%d") % r);

	if (writer_) {
		fcgi::recordp rec(new fcgi::record);
		rec->version = 1;
		rec->type = fcgi::rectype::end_request;
		rec->requestId1 = (r & 0xFF00) >> 8;
		rec->requestId0 = r & 0xFF;
		rec->contentLength1 = 0;
		rec->contentLength0 = 8;
		rec->paddingLength = 0;
		rec->reserved = 0;
		rec->contentData.resize(8);
		rec->contentData[0] = 0; /* appStatusB3 */
		rec->contentData[1] = 0; /* appStatusB2 */
		rec->contentData[2] = 0; /* appStatusB1 */
		rec->contentData[3] = 1; /* appStatusB0 */
		rec->contentData[4] = 0; /* protocolStatus */
		rec->contentData[5] = 0; /* reserved1 */
		rec->contentData[6] = 0; /* reserved2 */
		rec->contentData[7] = 0; /* reserved3 */

		writer_->write(rec);
	}

	requests_[r].reset();
}

void
fcgi_server_connection::handle_record(
		fcgi::recordp record,
		boost::system::error_code error)
{
	if (error == asio::error::operation_aborted) {
		std::cout << "fcgi_server_connection::handle_record: aborted\n";
		return;
	}

	if (error || !record) {
		LOG4CXX_DEBUG(logger, format("error reading record from server: %s")
				% error.message());
		destroy();
		return;
	}

	LOG4CXX_DEBUG(logger, format("record (request id=%d) arrived from the server")
			% record->request_id());
	async_read_fcgi_record(socket_,
			boost::bind(&fcgi_server_connection::handle_record, 
				shared_from_this(), _1, _2));

	/*
	 * A record arrived from the server.  We associate it with an existing
	 * application, or else create a new application if it's a BEGIN_REQUEST.
	 * Then forward it to the application.
	 */
	
	int id = record->request_id();

	if (id >= requests_.size() || !requests_[id]) {
		if (record->type != fcgi::rectype::begin_request) {
			/* Non-begin record for an unknown request; discard */
			LOG4CXX_DEBUG(logger,
				format("request id=%d does not exist and this is not a BEGIN_REQUEST") % id);
			return;
		}

		LOG4CXX_DEBUG(logger, "BEGIN_REQUEST for a new request");

		if (id >= requests_.size())
			requests_.resize(id + 1);

		requests_[id].reset(new fcgi_application(id, 
					shared_from_this(), context_));
	}

	requests_[id]->record_from_server(record);
}

void
fcgi_server_connection::start()
{
	LOG4CXX_DEBUG(logger, "starting server request processing");
	writer_.reset(new fcgi_record_writer(context_, socket_,
		boost::bind(&fcgi_server_connection::writer_error,
			shared_from_this(), _1)));
	tcp::socket::non_blocking_io cmd(true);
	socket_.io_control(cmd);
	async_read_fcgi_record(socket_,
			boost::bind(&fcgi_server_connection::handle_record, shared_from_this(), _1, _2));
}

tcp::socket &
fcgi_server_connection::socket()
{
	return socket_;
}

void
fcgi_server_connection::record_to_server(fcgi::recordp record)
{
	if (!alive_)
		return;

	LOG4CXX_DEBUG(logger, format("forwarding record (request id=%d) to server")
			% record->request_id());
	writer_->write(record);
}

void
fcgi_server_connection::writer_error(boost::system::error_code error)
{
	if (!alive_)
		return;

	LOG4CXX_DEBUG(logger, 
		format("writer_error: %s; server connection will be destroyed")
			% error.message());
	destroy();
}

void
fcgi_server_connection::destroy()
{
	LOG4CXX_DEBUG(logger, format("destroy called; alive_=%d") % alive_);

	if (!alive_)
		return;	/* we're already dying */

	alive_ = false;

	writer_.reset();
	requests_.clear();
	int fd = socket_.native();
	socket_.close();
	lsnr_->close(fd);
}
