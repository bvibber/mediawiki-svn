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
#include	"fcgi_response.h"

using asio::ip::tcp;
using boost::format;

fcgi_application::fcgi_application(
		int request_id,
		fcgi_server_connection_basep server,
		sbcontext &context)
	: server_(server)
	, context_(context)
	, request_id_(request_id)
	, connected_(false)
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
		push_record(record);
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
				cgi_->start(boost::bind(&fcgi_application::process_ready,
							shared_from_this()));
			} catch (std::exception &e) {
				LOG4CXX_DEBUG(logger, format( "error creating fcgi_cgi: %s")
						% e.what());
				if (!server_.expired()) {
					fcgi_response resp(request_id_);
					resp.add_stdout(boost::io::str(boost::format(
"Status: 500 Internal server error\r\n"
"Content-Type: text/html;charset=UTF-8\r\n"
"\r\n"
"<html><head><title>switchboard error</title></head>\r\n"
"<body><p>hi,</p>\r\n"
"<p>i am the PHP switchboard, and i handle PHP requests (like yours) on this server.\r\n"
"i'm afraid i was unable to handle your request.  when i tried, the following\r\n"
"error occurred: <tt>%1%</tt>.</p>\r\n"
"<p>please try your request again in a few minutes.  if it still doesn't work,\r\n"
"you should contact the server administrator and inform him of the problem.</p>\r\n"
"<p>regards,<br> the PHP switchboard.</p>\r\n"
					) % e.what()));
					resp.end();

					std::vector<fcgi::record> const &recs = resp.as_vector();
					for (int i = 0; i < recs.size(); ++i) {
						server_.lock()->record_to_server(
							fcgi::recordp(new fcgi::record(recs[i])));
					}

					server_.lock()->destroy(request_id_);
				}
				return;
			}

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
				push_record(rec);
			}

			push_record(record);
			return;
		} else {
			std::pair<std::string, std::string> p;
			fcgi::decode_params(record->contentData.begin(), record->contentData.end(), &p);
			params_.insert(p);
			push_record(record);
		}
		break;

	default:
		push_record(record);
	}
}

void
fcgi_application::process_ready()
{
	/* Send all the buffered records to the app */
	for (int i = 0; i < buffer.size(); ++i)
		cgi_->record_noflush(buffer[i]);
	cgi_->flush();

	std::vector<fcgi::recordp>().swap(buffer);

	connected_ = true;
}

void
fcgi_application::push_record(fcgi::recordp rec)
{
	if (connected_)
		cgi_->record(rec);
	else
		buffer.push_back(rec);
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
