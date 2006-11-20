/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * mbuffer: helper buffer for data marshalling.
 */

#ifndef MBUFFER_H
#define MBUFFER_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <sys/types.h>

#include <inttypes.h>
#include <cassert>
#include <string>
#include <cstddef>
using std::size_t;
using std::basic_string;

#include "willow.h"

struct marshalling_buffer {
	marshalling_buffer()
		: _buf(NULL)
		, _size(0)
		, _bufsz(0)
	{}

	marshalling_buffer(char const *buf, uint32_t sz)
		: _buf(const_cast<char *>(buf))
		, _size(0)
		, _bufsz(sz)
	{}
		
	~marshalling_buffer(void) {
	}

	void reserve(size_t size) {
		_bufsz = size;
		_buf = new char[size];
	}

	template<typename T>
	void append(T const &);
	
	template<typename charT, typename traits, typename allocator>
	void append(basic_string<charT, traits, allocator> const &);

	template<typename T>
	typename enable_if<is_char_type<T>, void>::type
	append_bytes(T const *buf, size_t s) {
		assert(_size + s <= _bufsz);
		memcpy(_buf + _size, buf, s);
		_size += s;
	}

	char const *buffer(void) const {
		return _buf;
	}

	size_t size(void) const {
		return _size;
	}

	template<typename T>
	bool extract(T &);

	template<typename charT, typename traits, typename allocator>
	bool extract(basic_string<charT, traits, allocator> &);

	template<typename T>
	typename enable_if<is_char_type<T>, bool>::type
	extract_bytes(T *b, size_t s) {
		if (_size + s > _bufsz)
			return false;
		memcpy(b, _buf + _size, s);
		_size += s;
		return true;
	}

	template<typename T>
	typename enable_if<is_char_type<T>, bool>::type
	extract_bytes(vector<T> &v, size_t s) {
		if (_size + s > _bufsz)
			return false;
		v.assign(_buf + _size, _buf + _size + s);
		_size += s;
		return true;
	}

	bool discard_bytes(size_t n) {
		if (_size + n > _bufsz)
			return false;
		_size += n;
		return true;
	}

private:
	char	*_buf;
	size_t	 _size;
	size_t	 _bufsz;
	bool	 _delete;
};

template<>
void
marshalling_buffer::append<imstring>(imstring const &);

template<>
bool
marshalling_buffer::extract<imstring>(imstring &);

template<typename T>
void
marshalling_buffer::append(T const &o)
{
	append_bytes((unsigned char const *) &o, sizeof(o));
}

template<typename T>
bool
marshalling_buffer::extract(T &o)
{
	return extract_bytes((unsigned char *) &o, sizeof(o));
}

#endif
