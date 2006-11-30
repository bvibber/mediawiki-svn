/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* util: miscellaneous utilities.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef UTIL_H
#define UTIL_H

#include <sys/socket.h>
#include <sys/un.h>

#include <netinet/in.h>

#include <boost/shared_ptr.hpp>
#include <boost/utility.hpp>
#include <boost/lexical_cast.hpp>
#include <boost/archive/iterators/base64_from_binary.hpp>
#include <boost/archive/iterators/binary_from_base64.hpp>
#include <boost/archive/iterators/transform_width.hpp>
#include <boost/assign/list_of.hpp>
#include <boost/format.hpp>
#include <boost/mpl/int.hpp>
#include <boost/mpl/map.hpp>
#include <boost/mpl/set.hpp>
#include <boost/mpl/at.hpp>
#include <boost/mpl/pair.hpp>
#include <boost/mpl/vector.hpp>
#include <boost/mpl/find.hpp>
#include <boost/mpl/has_key.hpp>
#include <boost/type_traits.hpp>

#include <iostream>
#include <string>
#include <stdexcept>
#include <vector>
#include <cstddef>
#include <utility>
#include <map>

using namespace boost::assign;
namespace mpl = boost::mpl;
using boost::noncopyable;
using boost::lexical_cast;
using boost::io::str;
using boost::basic_format;
using boost::shared_ptr;
using boost::enable_if;
using boost::remove_pointer;
using boost::remove_const;

using std::runtime_error;
using std::basic_string;
using std::char_traits;
using std::vector;
using std::map;

#include "autoconf.h"

#ifdef __INTEL_COMPILER
# pragma warning (disable: 869 981 304 383 1418 1469 810 444)
#endif

#if defined(__GNUC__) || defined(__INTEL_COMPILER)
# define likely(c) __builtin_expect((c), true)
# define unlikely(c) __builtin_expect((c), false)
# define prefetch_memory(a) __builtin_prefetch(a)
#else
# define likely(c) c
# define unlikely(c) c
# define prefetch_memory(a) a
#endif

typedef boost::archive::iterators::base64_from_binary<
		boost::archive::iterators::transform_width<
			u_char const *, 6, 8> > base64_text;
typedef boost::archive::iterators::binary_from_base64<
		boost::archive::iterators::transform_width<
			u_char const *, 8, 6> > unbase64_text;

template<typename T>
struct is_char_type : mpl::int_<sizeof(T) == 1> {
};

#ifdef LORELEY_DEBUG
/*
 * Helper classes for sockaddr_caster, see below.
 */
typedef mpl::map<
        mpl::pair<sockaddr_in, mpl::int_<AF_INET> >,
        mpl::pair<sockaddr_in6, mpl::int_<AF_INET6> >,
        mpl::pair<sockaddr_un, mpl::int_<AF_UNIX> >,
	mpl::pair<sockaddr_storage, mpl::int_<AF_UNSPEC> >
> aftypes;

template<typename T>
struct is_sockaddr : mpl::has_key<aftypes::type, 
	typename remove_const<typename remove_pointer<T>::type>::type> {
};

template<typename To, typename From>
struct sockaddr_caster {
	static To cast(From f) {
		assert((mpl::at<aftypes::type, typename remove_const<typename 
			remove_pointer<To>::type>::type>::type::value == f->sa_family));
		return reinterpret_cast<To>(f);
	}
};

template<typename From>
struct sockaddr_caster<sockaddr *, From> {
	typedef typename enable_if<is_sockaddr<From>, int>::type type;

	static sockaddr *cast(From f) {
		return reinterpret_cast<sockaddr *>(f);
	}
};

template<typename From>
struct sockaddr_caster<sockaddr const *, From> {
	typedef typename enable_if<is_sockaddr<From>, int>::type type;

	static sockaddr const *cast(From f) {
		return reinterpret_cast<sockaddr const *>(f);
	}
};

#endif

/**
 * Cast from one sockaddr type to another.  If the conversion can be proved
 * invalid at compile time (e.g. the from type is not a sockaddr), a compile
 * time error will be generated.  Otherwise an assertion failure will be
 * triggered at runtime if the sa_family of the type being cast does not match
 * the destination type.
 *
 * Usage:
 *
 * \code
 * void f(sockaddr *s) {
 *   sockaddr_in *sin = sockaddr_cast<sockaddr_in *>(s);
 *   ...
 * }
 * \endcode
 *
 * \param To sockaddr type to cast to
 * \param From sockaddr type to cast from
 * \param f sockaddr struct to cast
 * \returns the result of the cast
 */
template<typename To, typename From>
To sockaddr_cast(From f) {
#ifdef LORELEY_DEBUG
	return sockaddr_caster<To, From>::cast(f);
#else
	return reinterpret_cast<To>(f);
#endif
}

/**
 * Find a "\r\n" sequence in a string.
 *
 * \param buf buffer to search
 * \param end pointer to one past the end of the buffer (buf + size)
 * \returns the position of the '\\r' character, if any, otherwise NULL
 */
template<typename charT>
inline typename enable_if<is_char_type<charT>, charT const *>::type
find_rn(charT const *buf, charT const *end) 
{
charT const	*s;
	for (s = buf; s < end; s += 2) {
		prefetch_memory(s + 2);
		prefetch_memory(s + 3);
		if (*s != '\r' && *s != '\n')
			continue;
		if (s + 1 < end && s[0] == '\r' && s[1] == '\n')
			return s;
		if (s > buf && s[-1] == '\r' && s[0] == '\n')
			return s - 1;
	}
	return NULL;
}

#endif
