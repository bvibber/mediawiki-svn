/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef FCGI_H
#define FCGI_H

#include	<vector>
#include	<stdexcept>
#include	<map>

#include	<boost/shared_ptr.hpp>

namespace fcgi {

	namespace rectype {
		enum rectype_t {
			begin_request = 1,
			abort_request = 2,
			end_request = 3,
			params = 4,
			stdin_ = 5,
			stdout_ = 6,
			data = 8,
			get_values = 9,
			get_values_result = 10,
			unknown = 11
		};
	}

	namespace role {
		enum role_t {
			responder = 1,
			authorizer = 2,
			filter = 3
		};
	}

	struct record {
		unsigned char version;
		unsigned char type;
		unsigned char requestId1;
		unsigned char requestId0;
		unsigned char contentLength1;
		unsigned char contentLength0;
		unsigned char paddingLength;
		unsigned char reserved;
		std::vector<unsigned char> contentData;
		std::vector<unsigned char> paddingData;

		std::size_t content_length() const {
			return (contentLength1 << 8) | contentLength0;
		}

		int request_id() const {
			return (requestId1 << 8) | requestId0;
		}
	};

	typedef boost::shared_ptr<record> recordp;

	typedef std::map<std::string, std::string> params;

	struct short_data : std::runtime_error {
		short_data() : std::runtime_error("FastCGI parameter data is truncated") {}
	};

	template<typename InputIterator>
	int decode_length(InputIterator &begin, InputIterator end)
	{
		if (std::distance(begin, end) < 1)
			throw short_data();

		int len;
		if (*begin <= 0x7F) {
			len = *begin;
			begin++;
			return len;
		}

		if (std::distance(begin, end) < 4)
			throw short_data();

		len = (int)((*begin & 0x7F) << 24)
			| (*(begin + 1) << 16)
			| (*(begin + 2) << 8)
			| *(begin + 3);
		begin += 4;
		return len;
	}

	template<typename InputIterator, typename OutputIterator>
	void decode_params(
			InputIterator begin, 
			InputIterator end,
			OutputIterator output)
	{
		/*
		 * Params are encoded as <name len><value len><name><value>.
		 * Lengths are either 1 byte < 0x7f, or 4 bytes.
		 */
		int namelen, valuelen;
		
		namelen = decode_length(begin, end);
		valuelen = decode_length(begin, end);

		if (std::distance(begin, end) < (namelen + valuelen))
			throw short_data();

		std::string name(begin, begin + namelen);
		std::string value(begin + namelen, begin + namelen + valuelen);
		*output++ = std::make_pair(name, value);
	}

} // namespace fcgi

#endif
