/* Willow: Lightweight HTTP reverse-proxy.                              */
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

#include <boost/shared_ptr.hpp>
#include <boost/utility.hpp>
#include <boost/lexical_cast.hpp>
#include <boost/archive/iterators/base64_from_binary.hpp>
#include <boost/archive/iterators/binary_from_base64.hpp>
#include <boost/archive/iterators/transform_width.hpp>
#include <boost/assign/list_of.hpp>
#include <boost/format.hpp>
#include <boost/mpl/int.hpp>

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

using std::runtime_error;
using std::basic_string;
using std::char_traits;
using std::vector;
using std::map;

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
