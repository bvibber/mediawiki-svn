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

#include <iostream>
#include <string>

struct noncopyable {
	noncopyable() {};
private:
	noncopyable(noncopyable const &);	/* no implementation */
};

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
