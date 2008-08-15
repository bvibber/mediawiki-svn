/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#include	<iostream>
#include	<cerrno>
#include	<cstring>
#include	<unistd.h>

#include	"fcgi.h"
#include	"util.h"

namespace fcgi {

void
pretty_print_string(std::ostream &strm, std::string const &str)
{
	strm << '"';
	for (int i = 0; i < str.size(); ++i) {
		if (str[i] > 0x1F && str[i] < 0x7F)
			strm << str[i];
		else
			strm << "\\0" << std::oct << (int)str[i];
	}
	strm << '"';
}

void
pretty_print_record(std::ostream &strm, fcgi::record const &rec)
{
	switch (rec.type_) {
	case rectype::begin_request:
		strm << "{FCGI_BEGIN_REQUEST,    ";
		strm << rec.request_id() << ", ";
		break;
	
	case rectype::end_request:
		strm << "{FCGI_END_REQUEST,      ";
		strm << rec.request_id() << ", ";
		break;

	case rectype::params:
		strm << "{FCGI_PARAMS,           ";
		strm << rec.request_id() << ", ";
		pretty_print_string(strm, std::string(rec.contentData.begin(), rec.contentData.end()));
		break;

	case rectype::stdin_:
		strm << "{FCGI_STDIN,            ";
		strm << rec.request_id() << ", ";
		pretty_print_string(strm, std::string(rec.contentData.begin(), rec.contentData.end()));
		break;

	case rectype::stdout_:
		strm << "{FCGI_STDOUT,           ";
		strm << rec.request_id() << ", ";
		pretty_print_string(strm, std::string(rec.contentData.begin(), rec.contentData.end()));
		break;

	default:
		strm << "{unknown type,          ";
		strm << rec.request_id() << ", ";
		break;
	}

	strm << "}\n";
}

bool
read_fcgi_record(int fd, fcgi::record *rec, int timeout)
{
	/*
	 * Format:
	 *   version		1 byte
	 *   type		1 byte
	 *   request id		2 bytes
	 *   content length	2 bytes
	 *   padding length	1 byte
	 *   reserved		1 byte
	 *   content data	variable
	 *   padding data	variable
	 */

	ssize_t i;

	if ((i = timed_read(fd, static_cast<void *>(rec), 8, timeout)) < 8) {
		std::fprintf(stderr, "couldn't read entire record header\n");
		if (i == -1)
			std::fprintf(stderr, "   error: %s\n", std::strerror(errno));
		else
			std::fprintf(stderr, "   bytes read = %d\n", i);
		return false;
	}

	rec->contentData.resize(rec->content_length());
	rec->paddingData.resize(rec->padding_length());

	if (rec->content_length() > 0) {
		if (timed_read(fd, &rec->contentData[0], rec->content_length(), timeout) <
				rec->content_length()) {
			std::fprintf(stderr, "couldn't read entire content\n");
			return false;
		}
	}

	if (rec->padding_length() > 0) {
		if (timed_read(fd, &rec->paddingData[0], rec->padding_length(), timeout) <
				rec->padding_length()) {
			std::fprintf(stderr, "couldn't read entire padding\n");
			return false;
		}
	}

#if 0
	std::cerr << "read:  ";
	pretty_print_record(std::cerr, *rec);
#endif

	return true;
}

bool
write_fcgi_record(int fd, fcgi::record const &rec, int timeout)
{
#if 0
	std::cerr << "write: ";
	pretty_print_record(std::cerr, rec);
#endif

	if (timed_write(fd, static_cast<void const *>(&rec), 8, timeout) == -1)
		return false;

	if (rec.content_length() > 0)
		if (timed_write(fd, static_cast<void const *>(&rec.contentData[0]), 
				rec.content_length(), timeout) == -1)
			return false;
	if (rec.padding_length() > 0)
		if (timed_write(fd, static_cast<void const *>(&rec.paddingData[0]),
				rec.padding_length(), timeout) == -1)
			return false;

	return true;
}

} // namespace fcgi
