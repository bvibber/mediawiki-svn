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

#include <iostream>
#include <string>
#include <stdexcept>
#include <vector>
#include <cstddef>
#include <utility>
#include <map>

using namespace boost::assign;
using boost::noncopyable;
using boost::lexical_cast;
using boost::io::str;
using boost::basic_format;
using boost::shared_ptr;

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

struct true_pred {
	template<typename T>
	struct test {
		typedef T type;
	};
};

template<typename T>
struct is_char_type;

template<>
struct is_char_type<char> : true_pred {};

template<>
struct is_char_type<unsigned char> : true_pred {};

template<>
struct is_char_type<signed char> : true_pred {};

template<>
struct is_char_type<char const> : true_pred {};

template<>
struct is_char_type<unsigned char const> : true_pred {};

template<>
struct is_char_type<signed char const> : true_pred {};

template<typename Pred, typename Ret>
struct enable_if {
	typedef typename Pred::template test<Ret>::type type;
};

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
