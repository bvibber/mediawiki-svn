/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id: fcgi.h 38741 2008-08-06 21:00:45Z river $ */

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
			stderr_ = 7,
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
		unsigned char version_;
		unsigned char type_;
		unsigned char requestId1_;
		unsigned char requestId0_;
		unsigned char contentLength1_;
		unsigned char contentLength0_;
		unsigned char paddingLength_;
		unsigned char reserved_;
		std::vector<unsigned char> contentData;
		std::vector<unsigned char> paddingData;

		int version() const {
			return version_;
		}

		rectype::rectype_t type() const {
			return rectype::rectype_t(type_);
		}

		std::size_t content_length() const {
			return (contentLength1_ << 8) | contentLength0_;
		}

		void content_length(std::size_t n) {
			contentLength1_ = n >> 8;
			contentLength0_ = n & 0xFF;
		}

		std::size_t padding_length() const {
			return paddingLength_;
		}

		int request_id() const {
			return (requestId1_ << 8) | requestId0_;
		}

		void request_id(int n) {
			requestId1_ = n >> 8;
			requestId0_ = n & 0xFF;
		}
	};

	struct begin_request_payload {
		unsigned char role1_;
		unsigned char role0_;
		unsigned char flags_;
		unsigned char reserved[5];

		role::role_t role() const {
			return role::role_t((role1_ << 8) | role0_);
		}

		int flags() const {
			return flags_;
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
		while (std::distance(begin, end) > 0) {
			int namelen, valuelen;
		
			namelen = decode_length(begin, end);
			valuelen = decode_length(begin, end);

			if (std::distance(begin, end) < (namelen + valuelen))
				throw short_data();

			std::string name(begin, begin + namelen);
			std::string value(begin + namelen, begin + namelen + valuelen);
			*output++ = std::make_pair(name, value);
			begin += (namelen + valuelen);
		}
	}

	template<typename OutputIterator>
	void encode_length(std::size_t s, OutputIterator &output) {
		if (s <= 127) {
			*output++ = (unsigned char) s;
		} else {
			*output++ = (unsigned char) (((s & 0xFF000000) >> 24) | 0x80);
			*output++ = (unsigned char) ((s & 0x00FF0000) >> 16);
			*output++ = (unsigned char) ((s & 0x0000FF00) >> 8);
			*output++ = (unsigned char) (s & 0x000000FF);
		}
	}

	template<typename InputIterator, typename OutputIterator>
	void encode_params(
			InputIterator begin,
			InputIterator end,
			OutputIterator output)
	{
		while (begin != end) {
			std::pair<std::string, std::string> const &p(*begin);

			encode_length(p.first.size(), output);
			encode_length(p.second.size(), output);

			std::copy(p.first.begin(), p.first.end(), output);
			std::copy(p.second.begin(), p.second.end(), output);

			begin++;
		}
	}

	bool read_fcgi_record(int fd, record *rec);
	bool write_fcgi_record(int fd, record const &rec);

	void pretty_print_record(std::ostream &strm, record const &rec);

} // namespace fcgi

#endif
