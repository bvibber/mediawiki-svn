/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<boost/format.hpp>
#include	<boost/bind.hpp>
#include	<boost/asio/write.hpp>
#include	<boost/asio/placeholders.hpp>

#include	"fcgi_record_writer.h"

namespace asio = boost::asio;
using asio::ip::tcp;
using boost::format;

using namespace record_writer_detail;

fcgi_record_writer::fcgi_record_writer(
		sbcontext &context,
		buffered_tcp_socket &socket,
		boost::function<void (boost::system::error_code const &)> errorfunc)
	: context_(context)
	, socket_(socket)
	, errorfunc_(errorfunc)
	, logger(log4cxx::Logger::getLogger("switchboard.fcgi_record_writer"))
{
	assert(!errorfunc_.empty());
	LOG4CXX_DEBUG(logger, format("record_writer@%p: created") % this);
}

void
fcgi_record_writer::write_noflush(fcgi::recordp record)
{
	assert(socket_.next_layer().native() != -1);
	LOG4CXX_DEBUG(logger, format("record_writer@%p: write_noflush()") % this);

	pending_record pend = {
		record,
		{ 
			asio::buffer((void *) record.get(), 8),
			asio::buffer(record->contentData),
			asio::buffer(record->paddingData),
		}
	};

	waiting_.push_back(pend);
}

void
fcgi_record_writer::write(fcgi::recordp record)
{
	assert(socket_.next_layer().native() != -1);
	LOG4CXX_DEBUG(logger, format("record_writer@%p: write()") % this);

	write_noflush(record);
	flush();
}

void
fcgi_record_writer::flush()
{
	assert(socket_.next_layer().native() != -1);
	if (!inflight_.empty() || waiting_.empty())
		return;

	LOG4CXX_DEBUG(logger, format("record_writer@%p: flush()") % this);

	inflight_.swap(waiting_);
	buffers_.reserve(inflight_.size() * 3);
	int towrite = 0;

	for (int i = 0; i < inflight_.size(); ++i) {
		towrite += 8 + inflight_[i].record->contentData.size()
			+ inflight_[i].record->paddingData.size();
		buffers_.push_back(inflight_[i].data[0]);
		buffers_.push_back(inflight_[i].data[1]);
		buffers_.push_back(inflight_[i].data[2]);
	}

	asio::async_write(socket_, buffers_,
			boost::bind(&fcgi_record_writer::write_done, this,
				asio::placeholders::error,
				asio::placeholders::bytes_transferred));
}

void
fcgi_record_writer::flush_done(boost::system::error_code const &error)
{
	if (error == asio::error::operation_aborted) {
		return;
	}

	/*
	 * Somehow, the native socket can end up as -1 here, even though
	 * there's no error.
	 */
	if (socket_.next_layer().native() == -1) {
		return;
	}

	if (error) {
		LOG4CXX_DEBUG(logger, format("record_writer@%p: error: %s")
				% this % error.message());
		errorfunc_(error);
		return;
	}

	std::vector<pending_record>().swap(inflight_);
	std::vector<asio::mutable_buffer>().swap(buffers_);
	LOG4CXX_DEBUG(logger, format("record_writer@%p: flush_done") % this);
	flush();
}

void
fcgi_record_writer::write_done(
	boost::system::error_code const &error,
	std::size_t bytes)
{
	if (error == asio::error::operation_aborted) {
		return;
	}

	if (socket_.next_layer().native() == -1) {
		return;
	}

	assert(socket_.next_layer().native() != -1);
	if (error) {
		LOG4CXX_DEBUG(logger, format("record_writer@%p: error: %s")
				% this % error.message());
		errorfunc_(error);
		return;
	}

	socket_.async_flush(boost::bind(
			&fcgi_record_writer::flush_done, this,
			asio::placeholders::error));

	LOG4CXX_DEBUG(logger, format("record_writer@%p: write_done") % this);
}

void
fcgi_record_writer::close() {
	socket_.close();
}
