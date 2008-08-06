/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	"fcgi_response.h"

fcgi_response::fcgi_response(int request_id)
	: request_id_(request_id)
{
}

void
fcgi_response::add_stdout(std::string const &text)
{
	fcgi::record rec;
	std::string::size_type len = text.size();

	rec.contentData.reserve(text.size());
	std::copy(text.begin(), text.end(), std::back_inserter(rec.contentData));

	rec.version = 1;
	rec.type = fcgi::rectype::stdout_;
	rec.requestId1 = (request_id_ & 0xFF00) >> 8;
	rec.requestId0 = (request_id_ & 0x00FF);
	rec.contentLength1 = (rec.contentData.size() & 0xFF00) >> 8;
	rec.contentLength0 = (rec.contentData.size() & 0x00FF);
	rec.paddingLength = 0;
	rec.reserved = 0;

	records_.push_back(rec);
}

void
fcgi_response::add_stderr(std::string const &text)
{
	fcgi::record rec;
	std::string::size_type len = text.size();

	rec.contentData.reserve(text.size());
	std::copy(text.begin(), text.end(), std::back_inserter(rec.contentData));

	rec.version = 1;
	rec.type = fcgi::rectype::stderr_;
	rec.requestId1 = (request_id_ & 0xFF00) >> 8;
	rec.requestId0 = (request_id_ & 0x00FF);
	rec.contentLength1 = (rec.contentData.size() & 0xFF00) >> 8;
	rec.contentLength0 = (rec.contentData.size() & 0x00FF);
	rec.paddingLength = 0;
	rec.reserved = 0;

	records_.push_back(rec);
}

void
fcgi_response::end()
{
	fcgi::record rec;

	rec.version = 1;
	rec.type = fcgi::rectype::end_request;
	rec.requestId1 = (request_id_ & 0xFF00) >> 8;
	rec.requestId0 = (request_id_ & 0x00FF);
	rec.contentData.push_back(0);
	rec.contentData.push_back(0);
	rec.contentData.push_back(0);
	rec.contentData.push_back(0);
	rec.contentData.push_back(0);
	rec.contentData.push_back(0);
	rec.contentData.push_back(0);
	rec.contentData.push_back(0);
	rec.contentLength1 = (rec.contentData.size() & 0xFF00) >> 8;
	rec.contentLength0 = (rec.contentData.size() & 0x00FF);
	rec.paddingLength = 0;
	rec.reserved = 0;

	records_.push_back(rec);
}

std::vector<fcgi::record> const &
fcgi_response::as_vector() const
{
	return records_;
}
