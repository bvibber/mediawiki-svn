/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * util: miscellaneous utilities.
 */

#ifndef UTIL_H
#define UTIL_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <boost/shared_ptr.hpp>
#include <boost/utility.hpp>
#include <boost/lexical_cast.hpp>
#include <boost/archive/iterators/base64_from_binary.hpp>
#include <boost/archive/iterators/binary_from_base64.hpp>
#include <boost/archive/iterators/transform_width.hpp>

#include <iostream>
#include <string>
#include <stdexcept>
#include <vector>
#include <cstddef>
#include <utility>
#include <map>
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

using boost::noncopyable;
using boost::lexical_cast;
using boost::io::str;
using boost::basic_format;
using boost::shared_ptr;

#if 0
struct bad_lexical_cast : runtime_error {
	bad_lexical_cast() 
		: runtime_error("lexical_cast could not convert arguments") {}
};

template<typename From, typename To>
struct lexical_caster {
	static To cast (From const &f) {
	std::stringstream	strm;
	To			t;
		if (!(strm << f) || !(strm >> t))
			throw bad_lexical_cast();
		return t;
	}
};

template<typename From, typename charT, typename traits, typename allocator>
struct lexical_caster<From, basic_string<charT, traits, allocator> > {
	static basic_string<charT, traits, allocator> cast (From const &f) {
	std::basic_stringstream<charT, traits, allocator>	strm;

		if (!(strm << f))
			throw bad_lexical_cast();
		return strm.str();
	}
};
		
template<typename To, typename From>
To lexical_cast(From const &f)
{
	return lexical_caster<From, To>::cast(f);
}
#endif

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

namespace smap_detail {

	template<typename Key, typename Value>
	struct inner {
		Key k;
		Value v;
		mutable inner const	*next;

		inner(Key k_, Value v_)
		: k(k_), v(v_), next(0) {
		}

		inner const& operator, (inner const &other) const {
			other.next = this;
			return other;
		}
	};

} // namespace smap_detail

template<typename Key, typename Value>
smap_detail::inner<Key, Value>
smap_pair(Key k, Value v)
{
	return smap_detail::inner<Key, Value>(k, v);
}

template<typename Key, typename Value>
struct static_map
{
	struct inner {
		Key	first;
		Value	second;
	};
	typedef std::map<Key, Value> map_type;
	typedef map_type value_type;
	typedef typename map_type::iterator iterator;
	typedef typename map_type::const_iterator const_iterator;

	iterator begin(void) {
		return elems.begin();
	}
	iterator end(void) {
		return elems.end();
	}
	const_iterator begin(void) const {
		return elems.begin();
	}
	const_iterator end(void) const {
		return elems.end();
	}
	iterator find(Key const &k) {
		return elems.find(k);
	}
	const_iterator find(Key const &k) const {
		return elems.find(k);
	}

	template<typename NK, typename NV>
	static_map(smap_detail::inner<NK, NV> const &init) {
	smap_detail::inner<NK, NV> const *it = &init;
		while (it) {
			elems.insert(std::pair<Key, Value>(Key(it->k), Value(it->v)));
			it = it->next;
		}
	}

private:
	map_type	elems;
};

#endif
