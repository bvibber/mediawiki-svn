/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef FCGI_SOCKET_H
#define FCGI_SOCKET_H

#include	<asio/ip/tcp.hpp>
#include	<asio/socket_base.hpp>

#include	<boost/shared_ptr.hpp>
#include	<boost/signal.hpp>
#include	<boost/function.hpp>
#include	<boost/enable_shared_from_this.hpp>

#include	<log4cxx/logger.h>

#include	"fcgi_record_writer.h"
#include	"sbcontext.h"

struct fcgi_socket_base : 
	boost::noncopyable,
	boost::enable_shared_from_this<fcgi_socket_base> {

	virtual ~fcgi_socket_base() {}

	virtual void close() = 0;
	virtual void write_record(
			fcgi::recordp record,
			boost::function<void (asio::error_code)>) = 0;
	virtual void write_record_noflush(fcgi::recordp record) = 0;
	virtual void flush(boost::function<void (asio::error_code)>) = 0;

	virtual void async_read_record(
		boost::function<void (fcgi::recordp, asio::error_code)>) = 0;

	virtual asio::socket_base &socket() = 0;

	virtual boost::signals::connection 
		register_close_listener(boost::function<void (void)>) = 0;
};

template<typename Socket>
struct fcgi_socket : fcgi_socket_base {

	fcgi_socket(sbcontext &context);
	~fcgi_socket();

	void close();

	void write_record(
			fcgi::recordp record,
			boost::function<void (asio::error_code)>);
	void write_record_noflush(fcgi::recordp record);
	void flush(boost::function<void (asio::error_code)>);

	void async_read_record(
		boost::function<void (fcgi::recordp, asio::error_code)>);

	boost::signals::connection register_close_listener(
			boost::function<void (void)> func) {
		return close_sig_.connect(func);
	}

	Socket &socket_impl() {
		return socket_;
	}

	asio::socket_base &socket() {
		return socket_;
	}

private:
	sbcontext &context_;
	Socket socket_;
	boost::signal<void ()> close_sig_;
	bool alive_;
	
	boost::shared_ptr<fcgi_record_writer<Socket> > writer_;

	void create_writer(void);

	log4cxx::LoggerPtr logger;
};

typedef boost::shared_ptr<fcgi_socket_base> fcgi_socket_basep;
typedef boost::shared_ptr<fcgi_socket<asio::ip::tcp::socket> > fcgi_socket_tcpp;
typedef boost::shared_ptr<fcgi_socket<asio::local::stream_protocol::socket> > fcgi_socket_unixp;

template<typename Socket>
fcgi_socket<Socket>::fcgi_socket(sbcontext &context)
	: context_(context)
	, socket_(context.service())
	, alive_(true)
	, logger(log4cxx::Logger::getLogger("switchboard.fcgi_socket"))
{
}

template<typename Socket>
fcgi_socket<Socket>::~fcgi_socket()
{
}

template<typename Socket>
void
fcgi_socket<Socket>::close() 
{
	if (!alive_)
		return;

	alive_ = false;
	close_sig_();
	writer_.reset();
	socket_.close();
}

template<typename Socket>
void
fcgi_socket<Socket>::write_record(
		fcgi::recordp record,
		boost::function<void (asio::error_code)> func)
{
	if (!alive_) 
		return;

	if (!writer_)
		create_writer();

	writer_->write(record, func);
}

template<typename Socket>
void
fcgi_socket<Socket>::write_record_noflush(fcgi::recordp record)
{
	if (!alive_)
		return;

	if (!writer_)
		create_writer();

	writer_->write_noflush(record);
}

template<typename Socket>
void
fcgi_socket<Socket>::flush(boost::function<void (asio::error_code)> func)
{
	if (!alive_)
		return;

	if (!writer_)
		create_writer();

	writer_->flush(func);
}

template<typename Socket>
void
fcgi_socket<Socket>::async_read_record(
	boost::function<void (fcgi::recordp, asio::error_code)> func)
{
	if (!alive_)
		return;

	async_read_fcgi_record(
		boost::static_pointer_cast<fcgi_socket<Socket> >(this->shared_from_this()), func);
}

template<typename Socket>
void
fcgi_socket<Socket>::create_writer()
{
	writer_.reset(new fcgi_record_writer<Socket>(context_, socket_));
}

#endif	/* !FCGI_SOCKET_H */
