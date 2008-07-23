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

#include	<asio/ip/tcp.hpp>
#include	<asio/local/stream_protocol.hpp>
#include	<asio/buffer.hpp>
#include	<asio/write.hpp>
#include	<asio/placeholders.hpp>

#include	<boost/system/error_code.hpp>
#include	<boost/function.hpp>
#include	<boost/noncopyable.hpp>
#include	<boost/shared_ptr.hpp>
#include	<boost/enable_shared_from_this.hpp>
#include	<boost/format.hpp>
#include	<boost/bind.hpp>
#include	<log4cxx/logger.h>

#include	"switchboard.h"
#include	"fcgi.h"

struct sbcontext;

namespace record_writer_detail {
	struct pending_record {
		fcgi::recordp record;
		boost::array<asio::mutable_buffer, 3> data;
	};
}

template<typename Socket>
struct fcgi_record_writer : 
	boost::noncopyable, 
	boost::enable_shared_from_this<fcgi_record_writer<Socket> > {

	fcgi_record_writer(
		sbcontext &context,
		Socket &socket);

	void write(
		fcgi::recordp record,
		boost::function <void(asio::error_code)>);
	void write_noflush(fcgi::recordp record);
	void flush(boost::function<void (asio::error_code)>);
	void close();

private:
	sbcontext &context_;
	Socket &socket_;
	std::vector<record_writer_detail::pending_record> inflight_;
	std::vector<record_writer_detail::pending_record> waiting_;
	std::vector<asio::mutable_buffer> buffers_;
	bool alive_;

	void write_done(
		asio::error_code error,
		std::size_t bytes,
		boost::function <void(asio::error_code)>);
	void flush_done(asio::error_code error);

	template<typename InputIterator>
	void write(InputIterator begin, InputIterator end)
	{
		for (; begin != end; ++begin)
			write_noflush(begin);
	}

	log4cxx::LoggerPtr logger;
};

typedef boost::shared_ptr<fcgi_record_writer<asio::ip::tcp::socket> > fcgi_record_writer_tcpp;
typedef boost::shared_ptr<fcgi_record_writer<asio::local::stream_protocol::socket> > fcgi_record_writer_unixp;

template<typename Socket>
fcgi_record_writer<Socket>::fcgi_record_writer(
		sbcontext &context,
		Socket &socket)
	: context_(context)
	, socket_(socket)
	, alive_(true)
	, logger(log4cxx::Logger::getLogger("switchboard.fcgi_record_writer"))
{
	LOG4CXX_DEBUG(logger, boost::format("record_writer@%p: created") % this);
}

template<typename Socket>
void
fcgi_record_writer<Socket>::close()
{
	alive_ = false;
}

template<typename Socket>
void
fcgi_record_writer<Socket>::write_noflush(fcgi::recordp record)
{
	LOG4CXX_DEBUG(logger, boost::format("record_writer@%p: write_noflush()") % this);

	if (!alive_)
		return;

	record_writer_detail::pending_record pend = {
		record,
		{ 
			asio::buffer((void *) record.get(), 8),
			asio::buffer(record->contentData),
			asio::buffer(record->paddingData),
		}
	};

	waiting_.push_back(pend);
}

template<typename Socket>
void
fcgi_record_writer<Socket>::write(
	fcgi::recordp record,
	boost::function<void (asio::error_code)> func)
{
	if (!alive_)
		return;

	assert(socket_.native() != -1);
	LOG4CXX_DEBUG(logger, boost::format("record_writer@%p: write()") % this);

	write_noflush(record);
	if (inflight_.empty())
		flush(func);
}

template<typename Socket>
void
fcgi_record_writer<Socket>::flush(boost::function<void (asio::error_code)> func)
{
	if (!alive_)
		return;

	assert(socket_.native() != -1);
	if (!inflight_.empty() || waiting_.empty()) {
		func(asio::error_code());
		return;
	}

	LOG4CXX_DEBUG(logger, boost::format("record_writer@%p: flush()") % this);

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
			boost::bind(&fcgi_record_writer::write_done, 
				this->shared_from_this(), 
				asio::placeholders::error,
				asio::placeholders::bytes_transferred,
				func));
}

template<typename Socket>
void
fcgi_record_writer<Socket>::write_done(
	asio::error_code error,
	std::size_t bytes,
	boost::function<void (asio::error_code)> func)
{
	if (!alive_)
		return;

	if (error) {
		func(error);
		return;
	}

	assert(socket_.native() != -1);

	std::vector<record_writer_detail::pending_record>().swap(inflight_);
	std::vector<asio::mutable_buffer>().swap(buffers_);
	flush(func);

	LOG4CXX_DEBUG(logger, boost::format("record_writer@%p: write_done, bytes=%d") 
			% this % bytes);
}

#endif
