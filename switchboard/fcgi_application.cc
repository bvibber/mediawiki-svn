/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<cstring>
using std::strerror;	/* for asio */

#include	<iostream>
#include	<map>
#include	<iterator>

#include	<boost/format.hpp>

#include	"fcgi.h"
#include	"fcgi_application.h"
#include	"fcgi_server_connection.h"
#include	"fcgi_cgi.h"

using asio::ip::tcp;
using boost::format;

fcgi_application::fcgi_application(
		int request_id,
		fcgi_server_connectionp server,
		sbcontext &context)
	: server_(server)
	, context_(context)
	, request_id_(request_id)
	, logger(log4cxx::Logger::getLogger("switchboard.fcgi_application"))
{
	LOG4CXX_DEBUG(logger, format("creating application@%p") % this);
	assert(server);
}

fcgi_application::~fcgi_application()
{
	LOG4CXX_DEBUG(logger, format("destroying application@%p") % this);
	if (cgi_)
		cgi_->close();
}

void
fcgi_application::record_from_server(fcgi::recordp record)
{
	LOG4CXX_DEBUG(logger, format("received record from server (request id=%d)")
			% record->request_id());
	switch (record->type) {
	case fcgi::rectype::begin_request:
		if (!cgi_)
			buffer.push_back(record);

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
				cgi_.reset(new fcgi_cgi(request_id_, context_, 
						       	shared_from_this(), params_));
				cgi_->start();
			} catch (std::exception &e) {
				LOG4CXX_DEBUG(logger, format( "error creating fcgi_cgi: %s")
						% e.what());
				if (!server_.expired())
					server_.lock()->destroy(request_id_);
				return;
			}

			/* Send all the buffered records to the app */
			for (int i = 0; i < buffer.size(); ++i)
				cgi_->record_noflush(buffer[i]);

			/*
			 * Insert a fake SCRIPT_FILENAME, which we generated earlier
			 * in fcgi_cgi's ctor.
			 *
			 * Yes, constructing FastCGI records is very unpleasant.
			 */
			std::map<std::string, std::string>::iterator it
				= params_.find("SCRIPT_FILENAME");
			if (it != params_.end()) {
				fcgi::recordp rec(new fcgi::record);

				std::string const &name = it->first;
				std::string const &value = it->second;

				rec->contentData.push_back(
					(unsigned char) name.size()); /* <= 127 always */
				if (value.size() <= 127) {
					rec->contentData.push_back(
						(unsigned char) value.size());
				} else {
					rec->contentData.push_back(
						(unsigned char) (value.size() & 0x000000FF));
					rec->contentData.push_back(
						(unsigned char) ((value.size() & 0x0000FF00) >> 8));
					rec->contentData.push_back(
						(unsigned char) ((value.size() & 0x00FF0000) >> 16));
					rec->contentData.push_back(
						(unsigned char) ((value.size() & 0xFF000000) >> 24));
				}

				std::copy(name.begin(), name.end(),
						std::back_inserter(rec->contentData));
				std::copy(value.begin(), value.end(),
						std::back_inserter(rec->contentData));

				rec->version = 1;
				rec->type = fcgi::rectype::params;
				rec->requestId1 = record->requestId1;
				rec->requestId0 = record->requestId0;
				rec->contentLength1 = (rec->contentData.size() & 0xFF00) >> 8;
				rec->contentLength0 = (rec->contentData.size() & 0x00FF);
				rec->paddingLength = 0;
				rec->reserved = 0;
				cgi_->record_noflush(rec);
			}

			cgi_->record_noflush(record);

			cgi_->flush();
			std::vector<fcgi::recordp>().swap(buffer);
			return;
		} else {
			std::pair<std::string, std::string> p;
			fcgi::decode_params(record->contentData.begin(), record->contentData.end(), &p);
#if 0
			if (p.first != "PATH_TRANSLATEDx") {
				params_.insert(p);
				if (!cgi_)
					buffer.push_back(record);
			} 
#else
			params_.insert(p);
			if (!cgi_)
				buffer.push_back(record);
#endif
		}
		break;

	default:
		if (!cgi_)
			buffer.push_back(record);
	}

	if (cgi_) {
		cgi_->record(record);
	}
}

void
fcgi_application::record_from_child(fcgi::recordp record)
{
	LOG4CXX_DEBUG(logger, "received record from child, fwding to server");
	if (!server_.expired())
		server_.lock()->record_to_server(record);
}

void
fcgi_application::destroy()
{
	LOG4CXX_DEBUG(logger, format("application@%p, destroy() called") % this);
	if (cgi_)
		cgi_->close();
	cgi_.reset();
	if (!server_.expired())
		server_.lock()->destroy(request_id_);
}

void
fcgi_application::close()
{
	LOG4CXX_DEBUG(logger, format("application@%p, close() called") % this);
	if (cgi_)
		cgi_->close();
	cgi_.reset();
}
