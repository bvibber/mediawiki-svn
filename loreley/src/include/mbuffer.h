/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* mbuffer: helper buffer for data marshalling.				*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef MBUFFER_H
#define MBUFFER_H

using std::size_t;
using std::basic_string;
using std::logic_error;

#include "loreley.h"

struct marshalling_buffer_overflow : logic_error {
	marshalling_buffer_overflow() : logic_error("marshalling buffer overflow") {}
};

struct marshalling_buffer {
	marshalling_buffer()
		: _buf(NULL)
		, _size(0)
		, _bufsz(0)
		, _delete(false)
	{}

	marshalling_buffer(char const *buf, uint32_t sz)
		: _buf(const_cast<char *>(buf))
		, _size(0)
		, _bufsz(sz)
		, _delete(false)
	{}

	marshalling_buffer&
	operator= (marshalling_buffer const &other) {
		_delete = other._delete;
		_bufsz = other._bufsz;
		_size = other._size;
		_buf = NULL;
		if (_delete) {
			_buf = new char[_bufsz];
			memcpy(_buf, other._buf, _bufsz);
		} else {
			_buf = other._buf;
		}
		return *this;
	}

	~marshalling_buffer(void) {
		if (_delete)
			delete[] _buf;
	}

	void reserve(size_t nsize) {
		_bufsz = nsize;
		if (_delete)
			delete[] _buf;
		_delete = true;
		_buf = new char[nsize];
	}

	template<typename T>
	void append(T const &);
	
	template<typename charT, typename traits, typename allocator>
	void append(basic_string<charT, traits, allocator> const &);

	template<typename T>
	typename enable_if<is_char_type<T>, void>::type
	append_bytes(T const *buf, size_t s) {
		if (_size + s > _bufsz)
			throw marshalling_buffer_overflow();

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
