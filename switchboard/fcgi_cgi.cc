/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<sys/socket.h>
#include	<sys/un.h>
#include	<netinet/in.h>
#include	<arpa/inet.h>

#include	<boost/asio.hpp>
#include	<boost/bind.hpp>
#include	<boost/format.hpp>

#include	"fcgi_cgi.h"
#include	"fcgi_application.h"
#include	"async_read_fcgi_record.h"
#include	"process.h"

namespace asio = boost::asio;
using asio::ip::tcp;
using boost::format;

fcgi_cgi::fcgi_cgi(
		int request_id,
		sbcontext &context,
		fcgi_application *app,
		fcgi::params const &params)
	: context_(context)
	, child_socket_(context_.service(), 8192, 8192)
	, app_(app)
	, writer_(context, child_socket_, boost::bind(&fcgi_cgi::writer_error, this, _1))
	, request_id_(request_id)
	, logger(log4cxx::Logger::getLogger("switchboard.fcgi_cgi"))
{
	LOG4CXX_DEBUG(logger, format("[req=%d] fcgi_cgi@%p is created") 
			% request_id_ % this);
	int i;

    assert(app_);

	fcgi::params::const_iterator it = params.find("PATH_TRANSLATED");
	if (it == params.end())
		throw creation_failure("PATH_TRANSLATED not specified");

	process_ = context_.factory().create_from_filename(it->second);
	process_->connect(child_socket_.next_layer());
	tcp::socket::non_blocking_io cmd(true);
	child_socket_.next_layer().io_control(cmd);

	LOG4CXX_DEBUG(logger, format("[req=%d] connected to child")
			% request_id_);
	async_read_fcgi_record(child_socket_, child_read_error_,
			boost::bind(&fcgi_cgi::handle_child_read, this, _1));
}

fcgi_cgi::~fcgi_cgi()
{
	LOG4CXX_DEBUG(logger, format("[req=%d] fcgi_cgi@%p destructed") 
			% request_id_ % this);
	context_.factory().release(process_);
}

void
fcgi_cgi::record(fcgi::recordp record)
{
	LOG4CXX_DEBUG(logger, format("[req=%d] received a record, fwding to child")
			% request_id_);
	writer_.write(record);
}

void
fcgi_cgi::record_noflush(fcgi::recordp record)
{
	LOG4CXX_DEBUG(logger, format("[req=%d] received a record, fwding to child")
			% request_id_);
	writer_.write_noflush(record);
}

void
fcgi_cgi::flush()
{
	writer_.flush();
}

void
fcgi_cgi::writer_error(boost::system::error_code const &error)
{
	LOG4CXX_DEBUG(logger, format("[req=%d] write to child completed with error: %s")
			% request_id_ % error.message());
    if (app_)
        app_->destroy();
}

void
fcgi_cgi::handle_child_read(fcgi::recordp record)
{
	if (!record) {
		if (child_read_error_)
			LOG4CXX_DEBUG(logger, format("[req=%d] fcgi_cgi, error reading from child: %s")
					% request_id_ % child_read_error_.message());
        if (app_)
            app_->destroy();
		return;
	}

	bool passup = false, destroy = false;

	switch (record->type) {
	case fcgi::rectype::abort_request:
	case fcgi::rectype::end_request:
		destroy = true;
	case fcgi::rectype::params:
	case fcgi::rectype::stdin_:
	case fcgi::rectype::stdout_:
	case fcgi::rectype::data:
		passup = true;
		break;

	case fcgi::rectype::begin_request:
	case fcgi::rectype::get_values:
	case fcgi::rectype::get_values_result:
	default:
		destroy = true;
		passup = false;
		break;
	}

	LOG4CXX_DEBUG(logger, format("[req=%d] received record from child, destroy=%d passup=%d")
			% request_id_ % destroy % passup);

	if (record->request_id() != request_id_) {
		LOG4CXX_DEBUG(logger, format("request id doesn't match!")
				% request_id_);
		passup = false;
		destroy = true;
	}

	if (passup)
		app_->record_from_child(record);

	if (destroy)
		app_->destroy();
	else
		async_read_fcgi_record(child_socket_, child_read_error_,
			boost::bind(&fcgi_cgi::handle_child_read,
				this, _1));
}

void
fcgi_cgi::destroy()
{
	LOG4CXX_DEBUG(logger, format("[req=%d] cgi@%p, destroy() called") 
			% request_id_ % this);
    if (app_)
        app_ = NULL;

	/*
	 * To destroy ourselves, we close the socket, then post a 'delete this'
	 * operation to the service.  This ensures that we still exist when
	 * any outstanding socket operations return.
	 */
	child_socket_.close();
	context_.service().post(boost::bind(&fcgi_cgi::delete_me, this));
}

void
fcgi_cgi::delete_me()
{
	LOG4CXX_DEBUG(logger, format("[req=%d] cgi@%p, delete_me() called") 
			% request_id_ % this);
	delete this;
}
