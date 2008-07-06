/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<iostream>
#include	<map>
#include	<iterator>

#include	<boost/format.hpp>

#include	"fcgi.h"
#include	"fcgi_application.h"
#include	"fcgi_server_connection.h"
#include	"fcgi_cgi.h"

namespace asio = boost::asio;
using asio::ip::tcp;
using boost::format;

fcgi_application::fcgi_application(
		int request_id,
		fcgi_server_connection *server,
		sbcontext &context)
	: cgi_(0)
	, server_(server)
	, context_(context)
	, request_id_(request_id)
	, logger(log4cxx::Logger::getLogger("switchboard.fcgi_application"))
{
	LOG4CXX_DEBUG(logger, format("creating application@%p") % this);
}

fcgi_application::~fcgi_application()
{
	LOG4CXX_DEBUG(logger, format("destroying application@%p") % this);
	if (cgi_)
		cgi_->destroy();
}

void
fcgi_application::record_from_server(fcgi::recordp record)
{
	LOG4CXX_DEBUG(logger, format("received record from server (request id=%d)")
			% record->request_id());
	if (!cgi_)
		buffer.push_back(record);

	switch (record->type) {
	case fcgi::rectype::begin_request:
		break;
	case fcgi::rectype::params:
		if (record->content_length() == 0) {
			/*
			 * end of parameters.  at this point we can create the
			 * real application.
			 */
			LOG4CXX_DEBUG(logger, "finished reading params, "
				"fcgi_cgi will be created");
			try {
				cgi_ = new fcgi_cgi(request_id_, context_, this, params_);
			} catch (std::exception &e) {
				LOG4CXX_DEBUG(logger, format( "error creating fcgi_cgi: %s")
						% e.what());
				server_->destroy(request_id_);
				return;
			}

			/* Send all the buffered records to the app */
			for (int i = 0; i < buffer.size(); ++i)
				cgi_->record_noflush(buffer[i]);
			cgi_->flush();
			std::vector<fcgi::recordp>().swap(buffer);
			return;
		} else {
			fcgi::decode_params(record->contentData.begin(), record->contentData.end(),
					std::inserter(params_, params_.begin()));
		}
		break;
	}

	if (cgi_) {
		cgi_->record(record);
	}
}

void
fcgi_application::record_from_child(fcgi::recordp record)
{
	LOG4CXX_DEBUG(logger, "received record from child, fwding to server");
	server_->record_to_server(record);
}

void
fcgi_application::destroy()
{
	LOG4CXX_DEBUG(logger, format("application@%p, destroy() called") % this);
	server_->destroy(request_id_);
}
