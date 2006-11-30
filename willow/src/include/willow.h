/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef LORELEY_H
#define LORELEY_H

#include "autoconf.h"

#include <sstream>
#include <cstddef>
#include <iostream>
#include <typeinfo>
#include <stdexcept>
#include <string>
#include <cmath>
#include <vector>
#include <strings.h>
using std::runtime_error;
using std::basic_string;
using std::char_traits;
using std::vector;
using std::basic_ostream;
using std::istream;

#include <boost/utility.hpp>
#include <boost/mpl/int.hpp>
namespace mpl = boost::mpl;

#include "log.h"
#include "ptalloc.h"
#include "format.h"
#include "autoconf.h"

typedef unsigned long long w_size_t;

#include <stdlib.h>
#define wmalloc malloc
#define wfree free
#define wstrdup strdup
#define wrealloc realloc
#define wcalloc calloc

#ifndef HAVE_DAEMON
extern "C" int daemon(int, int);
#endif

#ifndef HAVE_SOCKLEN_T
typedef int socklen_t;
#endif

#ifndef HAVE_STRLCAT
extern "C" size_t strlcat(char *dst, const char *src, size_t siz);
#endif
#ifndef HAVE_STRLCPY
extern "C" size_t strlcpy(char *dst, const char *src, size_t siz);
#endif

#define rotl(i,r) (((i) << (r)) | ((i) >> (sizeof(i)*CHAR_BIT-(r))))

/**
 * Convert the buffer src, which is len bytes long, need not be NUL
 * terminated, and contains an integer in base, to an int.
 *
 * \param base the base of the integer to convert; must be between 2 and 16 or
 * a compile-time error will occur.
 * \param src buffer to convert
 * \param len length of src
 * \returns the converted integer, or -1 if the entire buffer could not be
 * parsed.
 */
template<int base>
typename boost::enable_if<mpl::int_<(base >= 2 && base <= 16)>, int>::type
strNtoint(char const *src, int len)
{
int     mult = 1;
int     res = 0;
        for (; len; len--) {
        int     tval;
        char    c = src[len - 1];
                if (c >= '0' && c <= '9')
                        tval = c - '0';
                else if (c >= 'a' && c <= 'f')
                        tval = 10 + c - 'a';
                else if (c >= 'A' && c <= 'F')
                        tval = 10 + c - 'A';
                else    return -1;

                if (tval >= base)
                        return -1;
                res += tval * mult;
                mult *= base;
        }
        return res;
}


/**
 * Compare a to b, ignoring case.
 *
 * \returns true if they are equal, or false if not.
 */
static inline bool
httpcompare(string const &a, string const &b)
{
	return a.size() == b.size() &&
	       !strncasecmp(a.data(), b.data(), a.size());
}

void outofmemory(void);
#ifdef __SUNPRO_C
# pragma does_not_return(outofmemory)
#endif

#define CHAR_HOST	1

extern int char_table[];

template<typename T, void (T::*ptmf) (void)>
void ptmf_transform(void *p)
{
T	*o = (T *)p;
	(o->*ptmf)();
}

template<typename T, typename AT1, typename AT2, void (T::*ptmf) (AT1, AT2)>
struct ptmf_transform2 {
	static void call(AT1 a, AT2 b, void *p) {
		(static_cast<T*>(p)->*ptmf)(a, b);
	}
};

#include "access.h"

extern struct stats_stru : noncopyable {
	atomic<int>	interval;	/* update interval	*/
	access_list	access;

	/*
	 * Absolute values.
	 */
	struct abs_t {
			abs_t() 
				: n_httpreq_ok(0)
				, n_httpreq_fail(0)
				, n_httpresp_ok(0)
				, n_httpresp_fail(0) {
			}
		uint64_t	n_httpreq_ok;		/* requests which were sent to a backend		*/
		uint64_t	n_httpreq_fail;		/* requests which did not reach a backend		*/
		uint64_t	n_httpresp_ok;		/* backend responses with status 200			*/
		uint64_t	n_httpresp_fail;	/* backend responses with status other than 200		*/
	} cur, last;
	lockable	cur_lock;
	tss<abs_t>	tcur;

	/*
	 * Averages.
	 */
	atomic<uint32_t>	n_httpreq_oks;		/* httpreq_ok per sec		*/
	atomic<uint32_t>	n_httpreq_fails;	/* httpreq_fail per sec		*/
	atomic<uint32_t>	n_httpresp_oks;		/* httpresp_ok per sec		*/
	atomic<uint32_t>	n_httpresp_fails;	/* httpresp_fail per sec	*/
} stats;

/**
 * A string whose size doesn't change after it's constructed.  This is much
 * more efficient for such strings than GNU's implementation of std::string.
 *
 * An imstring's size should be given before anything is added to it.  For
 * example:
 *
 * \code
 * imstring s;
 * s.reserve(6);
 * s.assign("foo");
 * s.assign("bar");
 * \endcode
 *
 * If assign() is called without reserving anything, it will reserve the length
 * of the assigned data.  Nothing more can be added to the string in this case.
 *
 * imstring contents (returned by data() or c_str()) can be modified directly
 * without calling imstring members.  The string is always nul-terminated.
 *
 * \param charT char type to store in this string
 * \param allocator allocator to use for string allocations.  defaults to
 * std::allocator<charT>.
 */
template<typename charT, typename allocator = std::allocator<charT> >
struct basic_imstring {
	typedef typename allocator::size_type	size_type;
	typedef charT 		*iterator;
	typedef charT const	*const_iterator;

	/**
	 * Construct an empty imstring.
	 */
	basic_imstring(void);

	/**
	 * Construct an imstring from the given nul-terminated C string s.
	 */
	basic_imstring(charT const *s);

	/**
	 * Construct an imstring from the char array s, which is len bytes n
	 * length and need not be nul-terminated.
	 */
	basic_imstring(charT const *s, size_type n);

	/**
	 * Construct an imstring as a copy of the string s.
	 */
	basic_imstring(basic_imstring const &s);
	~basic_imstring(void);

	/**
	 * Construct an imstring from the basic_string s.
	 */
	template<typename Sallocator>
	basic_imstring(basic_string<charT, char_traits<charT>, Sallocator> 
		const &s);

	/**
	 * Release the contents of this imstring and replace them with the
	 * contents of the string s.
	 */
	basic_imstring& operator= (basic_imstring const &s);

	/**
	 * Return a pointer to this string's buffer.  The buffer is
	 * nul-terminated.
	 */
	charT		*c_str		(void)	const;

	/**
	 * Return a pointer to this string's buffer.  The buffer is
	 * nul-terminated.
	 */
	charT 		*data		(void)	const;

	/**
	 * Convert this imstring to a basic_string.
	 */
	std::basic_string<charT, char_traits<charT>, allocator >
			 string		(void)	const;

	/**
	 * Reserve len bytes of storage for this string.  If the string has
	 * any existing contents, reserve invalidates them.  The string's
	 * length is set to len.
	 */	
	void	 reserve	(size_type len);

	/**
	 * Assign the C string s, which must be nul-terminated, to this string.
	 */
	void	 assign		(charT const *s);

	/**
	 * Assign the char array s, which need not be nul-terminated and is
	 * len bytes in length, to this string.
	 */
	void	 assign		(charT const *s, size_type len);

	/**
	 * Assign the char array s, which extends until one char before end,
	 * and need not be nul-terminated, to this string.
	 */
	void	 assign		(charT const *s, charT const *end);

	/**
	 * Append the C string s, which must be nul-terminated, to this string.
	 */
	void	 append		(charT const *s);

	/**
	 * Append the char array s, which need not be nul-terminated and is len
	 * bytes in length, to this string.
	 */
	void	 append		(charT const *s, size_type len);

	/**
	 * Append the char array s, which ends one character before end and need
	 * not be nul-terminated, to this string.
	 */
	void	 append		(charT const *s, charT const *end);

	/**
	 * Return the length of this string, not including the nul terminator.
	 */
	size_type	length	(void) const;

	/**
	 * Returns length().
	 */
	size_type	size	(void) const;

	/**
	 * Returns true if length()==0.
	 */
	bool		empty	(void) const;

	/**
	 * Return a const (immutable) iterator referring to the start of this
	 * string.
	 */
	const_iterator	begin		(void) const;

	/**
	 * Return a const (immutable) iterator referring to one byte past the
	 * end of this string.
	 */
	const_iterator	end		(void) const;

	/**
	 * Return a mutable iterator referring to the start of this string.
	 */
	iterator	begin		(void);

	/**
	 * Return a mutable iterator referring to the end of this string.
	 */
	iterator	end		(void);

	/**
	 * Return the character at position n.
	 */
	charT		&operator[]	(size_type n);

	/**
	 * Return the character at position n.
	 */
	charT const	&operator[]	(size_type n) const;

	/**
	 * Print the contents of this string to the given ostream.
	 */
	basic_ostream<charT, char_traits<charT> >
		&print		(basic_ostream<charT, char_traits<charT> > &) const;

	/**
	 * \returns true if this string is lexicographically less than s.
	 */
	bool	operator<	(basic_imstring const &s) const;

	/**
	 * \returns true if this string is lexicographically greater than s.
	 */
	bool	operator>	(basic_imstring const &s) const;

	/**
	 * \returns true if this string is lexicographically equal to s.
	 */
	bool	operator==	(basic_imstring const &s) const;

	/**
	 * \returns true if this string's contents differ from s's.
	 */
	bool	operator!=	(basic_imstring const &s) const;

private:
	/**
	 * Prevent construction from an imstring of differing chartype or
	 * allocator.
	 */
	template<typename charT2, typename allocator2>
	basic_imstring(basic_imstring<charT2, allocator2> const &);

	/**
	 * Prevent assignment from an imstring of differing chartype or
	 * allocator.
	 */
	template<typename charT2, typename allocator2>
	basic_imstring &
	operator=(basic_imstring<charT2, allocator2> const &);
	
	charT		*_buf, *_end;
	size_type	 _len;
	
	allocator	 _alloc;
};

typedef basic_imstring<char, pt_allocator<char> > imstring;

template<typename charT, typename allocator>
basic_imstring<charT, allocator>::~basic_imstring(void)
{
	if (_buf)
		_alloc.deallocate(_buf, _len);
}

template<typename charT, typename allocator>
basic_imstring<charT, allocator>::basic_imstring(void)
	: _buf(NULL)
	, _len(0)
{
}

template<typename charT, typename allocator>
basic_imstring<charT, allocator>::basic_imstring(charT const *str)
	: _buf(NULL)
	, _len(0)
{
	reserve(strlen(str));
	_end = _buf + _len;
	memcpy(_buf, str, _len + 1);
}

template<typename charT, typename allocator>
basic_imstring<charT, allocator>::basic_imstring(charT const *str, size_type len)
	: _buf(NULL)
	, _len(0)
{
	reserve(len);
	_end = _buf + _len;
	memcpy(_buf, str, _len);
	_buf[len] = 0;
}

template<typename charT, typename allocator>
basic_imstring<charT, allocator>::basic_imstring(basic_imstring const &o)
	: _buf(NULL)
	, _len(0)
{
	reserve(o._len);
	_end = _buf + _len;
	memcpy(_buf, o._buf, _len + 1);
}

template<typename charT, typename allocator>
void
basic_imstring<charT, allocator>::assign(charT const *str)
{	
int	slen = strlen(str);
	if (!_buf)
		reserve(slen);
	_end = _buf + slen;
	memcpy(_buf, str, _len + 1);
}

template<typename charT, typename allocator>
void
basic_imstring<charT, allocator>::assign(charT const *str, charT const *send)
{
	assign(str, send - str);
}

template<typename charT, typename allocator>
void
basic_imstring<charT, allocator>::assign(charT const *str, size_type len)
{
	if (!_buf)
		reserve(len);
	_end = _buf + len;
	memcpy(_buf, str, len);
	_buf[len] = 0;
}

template<typename charT, typename allocator>
basic_imstring<charT, allocator> &
basic_imstring<charT, allocator>::operator= (
	basic_imstring<charT, allocator> const &o)
{
	if (this == &o)
		return *this;

	if (_buf) {
		_alloc.deallocate(_buf, _len);
		_buf = NULL;
		_len = 0;
	}

	if (!o._buf) {
		_buf = 0;
		_len = 0;
		return *this;
	}

	reserve(o._len);
	_end = _buf + _len;
	memcpy(_buf, o._buf, _len + 1);
	return *this;
}
	
template<typename charT, typename allocator>
template<typename Sallocator>
basic_imstring<charT, allocator>::basic_imstring(
		basic_string<charT, char_traits<charT>, Sallocator> const &s)
	: _buf(NULL)
	, _len(0)
{
	reserve(s.size());
	_end = _buf + _len;
	memcpy(_buf, s.data(), _len);
	_buf[_len] = 0;
}


template<typename charT, typename allocator>
void
basic_imstring<charT, allocator>::reserve(
		typename basic_imstring<charT, allocator>::size_type s)
{
	if (_buf)
		_alloc.deallocate(_buf, _len);
	_buf = _alloc.allocate(s + 1);
	_len = s;
	_end = _buf;
}

template<typename charT, typename allocator>
void
basic_imstring<charT, allocator>::append(charT const *s)
{
	append(s, strlen(s));
}

template<typename charT, typename allocator>
void
basic_imstring<charT, allocator>::append(charT const *s, charT const *e)
{
	append(s, e - s);
}

template<typename charT, typename allocator>
void
basic_imstring<charT, allocator>::append(charT const *s,
		typename basic_imstring<charT, allocator>::size_type len)
{
	memcpy(_end, s, len);
	_end += len;
	*_end = '\0';
}

template<typename charT, typename allocator>
charT *
basic_imstring<charT, allocator>::c_str(void) const
{
	return _buf;
}

template<typename charT, typename allocator>
charT *
basic_imstring<charT, allocator>::data(void) const
{
	return _buf;
}

template<typename charT, typename allocator>
typename basic_imstring<charT, allocator>::size_type
basic_imstring<charT, allocator>::length(void) const
{
	return _len;
}

template<typename charT, typename allocator>
typename basic_imstring<charT, allocator>::size_type
basic_imstring<charT, allocator>::size(void) const
{
	return _len;
}

template<typename charT, typename allocator>
std::basic_string<charT, char_traits<charT>, allocator>
basic_imstring<charT, allocator>::string(void) const
{
	return std::basic_string<charT, char_traits<charT>, allocator>(
		_buf, _end);
}

template<typename charT, typename allocator>
basic_ostream<charT, char_traits<charT> > &
basic_imstring<charT, allocator>::print(basic_ostream<charT, char_traits<charT> > &o) const
{
	if (_buf)
		o << _buf;
	return o;
}

template<typename charT, typename allocator>
basic_ostream<charT, char_traits<charT> > &
operator<< (	basic_ostream<charT, char_traits<charT> > &o,
		basic_imstring<charT, allocator> const &s)
{
	return s.print(o);
}

template<typename charT, typename allocator>
bool
basic_imstring<charT, allocator>::operator==(
	basic_imstring<charT, allocator> const &o) const
{
	return strcasecmp(_buf, o._buf) == 0;
}

template<typename charT, typename allocator>
bool
basic_imstring<charT, allocator>::operator!=(
	basic_imstring<charT, allocator> const &o) const
{
	return !(*this == o);
}

template<typename charT, typename allocator>
bool
basic_imstring<charT, allocator>::operator<(
	basic_imstring<charT, allocator> const &o) const
{
	return strcasecmp(_buf, o._buf) < 0;
}

template<typename charT, typename allocator>
bool
basic_imstring<charT, allocator>::operator>(
	basic_imstring<charT, allocator> const &o) const
{
	return strcasecmp(_buf, o._buf) > 0;
}

template<typename charT, typename allocator>
typename basic_imstring<charT, allocator>::const_iterator
basic_imstring<charT, allocator>::begin(void) const
{
	return _buf;
}

template<typename charT, typename allocator>
typename basic_imstring<charT, allocator>::iterator
basic_imstring<charT, allocator>::begin(void)
{
	return _buf;
}

template<typename charT, typename allocator>
typename basic_imstring<charT, allocator>::const_iterator
basic_imstring<charT, allocator>::end(void) const
{
	return _end;
}

template<typename charT, typename allocator>
typename basic_imstring<charT, allocator>::iterator
basic_imstring<charT, allocator>::end(void)
{
	return _end;
}

template<typename charT, typename allocator>
bool
basic_imstring<charT, allocator>::empty(void) const
{
	return size() == 0;
}

template<typename charT, typename allocator>
charT &
basic_imstring<charT, allocator>::operator[] (
	typename basic_imstring<charT, allocator>::size_type n)
{
	assert(n < size());
	return _buf[n];
}

template<typename charT, typename allocator>
charT const &
basic_imstring<charT, allocator>::operator[] (
	typename basic_imstring<charT, allocator>::size_type n) const
{
	assert(n < size());
	return _buf[n];
}

/**
 * A buffer which uses /dev/shm buffers if possible (on Linux), so we can use
 * sendfile() on the buffer.
 */
struct diobuf : noncopyable, freelist_allocator<diobuf> {
	/**
	 * Create a new diobuf.
	 *
	 * \param size initial size of the buffer.  will be rounded to a
	 * multiple of page size for allocation.
	 */
	diobuf(size_t size = 4096);
	~diobuf(void);

	/**
	 * Resize this buffer, preserving the contents.
	 */
	void resize(size_t newsize);

	/**
	 * Return a pointer to the start of this buffer.
	 */
	char *ptr(void) const {
		return _buf;
	}

	/**
	 * Returns the size of this buffer, not including any allocation
	 * rounding.
	 */
	size_t size(void) const {
		return _size;
	}

	/**
	 * Return the file description to the shm file for this buffer.
	 */
	int fd(void) const {
		return _fd;
	}

	/**
	 * Append the buffer buf, of len bytes, to the end of this buffer.
	 * Automatically resizes the buffer to fit.
	 */
	void append(char const *buf, size_t len) {
	size_t	osize = _size;
		resize(_size + len);
		memcpy(_buf + osize, buf, len);
	}

	/**
	 * Indicate that this buffer will not be written to again (reading is
	 * still allowed.)
	 */
	void finished(void);

	/**
	 * Load n bytes of data from the file f nto this buffer.
	 *
	 * \return true if the data was loaded successfully, or false if an
	 * error occured.
	 */
	bool loadfile(istream &f, size_t n);

private:
	int	 _fd;
	char	*_buf;
	size_t	 _size;
	size_t	 _reserved;
};

#endif
