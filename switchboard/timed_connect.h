/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef TIMED_CONNECT_H
#define TIMED_CONNECT_H

#include	<boost/shared_ptr.hpp>
#include	<boost/enable_shared_from_this.hpp>

template<typename Socket, typename Endpoint>
struct timed_connect 
	: boost::enable_shared_from_this<timed_connect<Socket, Endpoint> > {

	timed_connect(Socket socket) 
		: socket_(socket)
		, alive_(true)
		, timer_(socket->socket_impl().get_io_service())
	{
	}

	template<typename Timeout>
	void connect(
		Endpoint where,
		Timeout timeout,
		boost::function<void (asio::error_code)> func)
	{
		socket_->socket_impl().async_connect(where,
			boost::bind(&timed_connect::done,
				this->shared_from_this(), func, asio::placeholders::error));
		timer_.expires_from_now(timeout);
		timer_.async_wait(boost::bind(&timed_connect::timeout, 
					this->shared_from_this(), func));
	}

	void
	timeout(boost::function<void (asio::error_code)> func)
	{
		if (!alive_)
			return;

		alive_ = false;
		socket_->close();
		func(asio::error::timed_out);
	}

	void
	done(
		boost::function<void (asio::error_code)> func,
		asio::error_code error)
	{
		timer_.cancel();

		if (!alive_)
			return;

		alive_ = false;

		if (error == asio::error::operation_aborted)
			/* this indicates a timeout, in which case timeout()
			   will return the error for us */
			return;

		func(error);
	}

private:
	Socket socket_;
	bool alive_;
	asio::deadline_timer timer_;
};

template<typename Socket, typename Endpoint, typename Timeout>
void
async_timed_connect(
		Socket socket,
		Endpoint endpoint,
		Timeout timeout,
		boost::function<void (asio::error_code)> func)
{
	boost::shared_ptr<timed_connect<Socket, Endpoint> > c(new 
			timed_connect<Socket, Endpoint>(socket));
	c->connect(endpoint, timeout, func);
}

#endif	/* !TIMED_CONNECT_H */
