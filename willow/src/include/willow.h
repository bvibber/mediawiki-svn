/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * willow: General utility functions.
 */

#ifndef WILLOW_H
#define WILLOW_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include "config.h"

#include <sstream>
#include <cstddef>
#include <iostream>
#include <typeinfo>
#include <stdexcept>
#include <string>
#include <cmath>
#include <vector>
using std::runtime_error;
using std::basic_string;
using std::char_traits;
using std::vector;
using std::basic_ostream;
using std::istream;

#include "wlog.h"
#include "radix.h"
#include "ptalloc.h"
#include "format.h"
#include "wconfig.h"

typedef unsigned long long w_size_t;

#include <stdlib.h>
#define wmalloc malloc
#define wfree free
#define wstrdup strdup
#define wrealloc realloc
#define wcalloc calloc

	char **wstrvec		(const char *, const char *, int);
	void wstrvecfree	(char **);

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

int str10toint(char const *src, int len);
int str16toint(char const *src, int len);

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

#if defined(__GNUC__) || defined(__INTEL_COMPILER)
# define likely(c) __builtin_expect((c), true)
# define unlikely(c) __builtin_expect((c), false)
# define prefetch_memory(a) __builtin_prefetch(a)
#else
# define likely(c) c
# define unlikely(c) c
# define prefetch_memory(a) a
#endif

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

extern struct stats_stru : noncopyable {
	atomic<int>	interval;	/* update interval	*/
	access_list	access;

	/*
	 * Absolute values.
	 */
	struct abs_t {
			abs_t() {
				memset(this, 0, sizeof(*this));
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

template<typename charT, typename allocator>
struct basic_imstring {
	typedef typename allocator::size_type	size_type;
	typedef charT 		*iterator;
	typedef charT const	*const_iterator;

	basic_imstring(void);
	basic_imstring(charT const *);
	basic_imstring(charT const *, size_type);
	basic_imstring(basic_imstring const &);
	~basic_imstring(void);

	template<typename Sallocator>
	basic_imstring(basic_string<charT, char_traits<charT>, Sallocator> const &);

	basic_imstring& operator= (basic_imstring const &);

	charT		*c_str		(void)	const;
	charT 		*data		(void)	const;
	std::basic_string<charT, char_traits<charT>, allocator >
			 string		(void)	const;
	
	void	 reserve	(size_type len);
	void	 assign		(charT const *);
	void	 assign		(charT const *, size_type);
	void	 assign		(charT const *, charT const *);
	void	 append		(charT const *);
	void	 append		(charT const *, size_type);
	void	 append		(charT const *, charT const *);

	size_type	length	(void) const;
	size_type	size	(void) const;
	bool		empty	(void) const;

	const_iterator	begin		(void) const;
	const_iterator	end		(void) const;
	iterator	begin		(void);
	iterator	end		(void);

	charT		&operator[]	(size_type n);
	charT const	&operator[]	(size_type n) const;

	basic_ostream<charT, char_traits<charT> >
		&print		(basic_ostream<charT, char_traits<charT> > &) const;

	bool	operator<	(basic_imstring const &) const;
	bool	operator>	(basic_imstring const &) const;
	bool	operator==	(basic_imstring const &) const;
	bool	operator!=	(basic_imstring const &) const;

private:
	template<typename charT2, typename allocator2>
	basic_imstring(basic_imstring<charT2, allocator2> const &);
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
basic_imstring<charT, allocator>::assign(charT const *str, charT const *end)
{
	assign(str, end - str);
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
		basic_imstring<charT, allocator>::size_type s)
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
		basic_imstring<charT, allocator>::size_type len)
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
	basic_imstring<charT, allocator>::size_type n)
{
	assert(n < size());
	return _buf[n];
}

template<typename charT, typename allocator>
charT const &
basic_imstring<charT, allocator>::operator[] (
	basic_imstring<charT, allocator>::size_type n) const
{
	assert(n < size());
	return _buf[n];
}

/*
 * A buffer which uses /dev/shm buffers if possible (on Linux), so we can use
 * sendfile() on the buffer.
 */
struct diobuf : freelist_allocator<diobuf> {
	diobuf(size_t size = 4096);
	~diobuf(void);

	void resize(size_t newsize);

	char *ptr(void) const {
		return _buf;
	}

	size_t size(void) const {
		return _size;
	}

	int fd(void) const {
		return _fd;
	}

	void append(char const *buf, size_t len) {
	size_t	osize = _size;
		resize(_size + len);
		memcpy(_buf + osize, buf, len);
	}

	void finished(void);

	/*
	 * Load data from the given file into the buffer.
	 */
	bool loadfile(istream &, size_t);
private:
	int	 _fd;
	char	*_buf;
	size_t	 _size;
	size_t	 _reserved;
};

#endif
