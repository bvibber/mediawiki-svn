/* Willow: Lightweight HTTP reverse-proxy.                              */
/* confgrammar: Spirit grammar for confparse				*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include <iostream>
#include <iterator>
#include <string>
#include <map>
#include <exception>

#include <boost/spirit.hpp>
#include <boost/spirit/attribute.hpp>
#include <boost/spirit/phoenix.hpp>
#include <boost/spirit/phoenix/casts.hpp>
#include <boost/spirit/phoenix/closures.hpp>

#include <boost/variant.hpp>
#include <boost/static_assert.hpp>

#include <boost/mpl/vector_c.hpp>
#include <boost/mpl/equal.hpp>
#include <boost/mpl/plus.hpp>
#include <boost/mpl/minus.hpp>
#include <boost/mpl/transform.hpp>
#include <boost/mpl/placeholders.hpp>

#include "util.h"
#include "willow.h"
#include "confgrammar.h"
#include "preprocessor.h"

using std::random_access_iterator_tag;
using std::pair;
using std::make_pair;

namespace mpl = boost::mpl;
namespace mpp = boost::mpl::placeholders;
namespace spirit = boost::spirit;
using boost::variant;
using spirit::grammar;
using spirit::scanner;
using spirit::chset;
using spirit::c_escape_ch_p;
using spirit::int_p;
using spirit::rule;
using spirit::ch_p;
using spirit::confix_p;
using spirit::file_iterator;
using spirit::parse_info;
using spirit::comment_p;
using spirit::str_p;
using spirit::assertion;
using spirit::guard;
using spirit::error_status;
using spirit::lexeme_d;
using spirit::as_lower_d;
using spirit::symbols;
using phoenix::var;
using phoenix::arg1;
using phoenix::arg2;
using phoenix::construct_;

struct block_closure : spirit::closure<block_closure,
		string, string, vector<value_t> > {
	member1	name;
	member2	key;
	member3	values;
};

struct file_closure : spirit::closure<file_closure, vector<block> > {
	member1	blocks;
};

struct value_closure : spirit::closure<
		value_closure,
		string,
		file_position,
		vector<avalue_t>
	> {
	member1	name;
	member2	declpos;
	member3	values;
};

struct avalue_closure : spirit::closure<
		avalue_closure,
		avalue_t
	> {
	member1	value;
};

struct expr_closure : spirit::closure<expr_closure, int> {
	member1	val;
};

struct size_closure : spirit::closure<size_closure, size_q, scalar_q> {
	member1	value;
	member2	multiplier;
};

struct time_closure : spirit::closure<time_closure, time_q, scalar_q> {
	member1	value;
	member2	multiplier;
};

struct sizev_closure : spirit::closure<sizev_closure, size_q> {
	member1	val;
};

struct timev_closure : spirit::closure<timev_closure, time_q> {
	member1	val;
};

enum errors {
	semicolon_expected,
	equals_expected,
	close_brace_expected,
	open_brace_expected
};

struct error_handler {
	template<typename ScannerT, typename ErrorT>
	error_status<> operator()(ScannerT const &scan, ErrorT const &err) const {
	file_position pos = err.where.get_position();
	string	errtxt;

		switch (err.descriptor) {
		case semicolon_expected:
			errtxt = "expected semicolon";
			break;
		case equals_expected:
			errtxt = "expected '='";
			break;
		case close_brace_expected:
			errtxt = "expected '}'";
			break;
		case open_brace_expected:
			errtxt = "expected '{'";
			break;
		}

		wlog.error(format("%s(%d): %s") % pos.file % pos.line % errtxt);
		wlog.error('\t' + err.where.get_line(pos.line));
		wlog.error(format("\t%s^") % string(pos.col - 1, ' '));

		while (*scan.first != ';')
			++scan.first;

		++scan.first;

		return error_status<>(error_status<>::fail);
	}
};

struct conf_parser_impl : public grammar<conf_parser_impl, file_closure::context_t> {
	struct comment_parser {
		comment_parser() {
			comment = comment_p("/*", "*/") | comment_p("#");
BOOST_SPIRIT_DEBUG_RULE(comment);
		}

		rule<> const &start(void) const {
			return comment;
		}

		rule<> comment;
	};

	symbols<int>	times;
	symbols<int>	sizes;

	conf_parser_impl() {
		times.add("second", 1);
		times.add("minute", 60);
		times.add("hour", 60 * 60);
		times.add("day", 60 * 60 * 24);
		times.add("week", 60 * 60 * 24 * 7);
		times.add("fortnight", 60 * 60 * 24 * 7 * 2);

		sizes.add("byte", 1);

		sizes.add("kilobyte", 1024);
		sizes.add("megabyte", 1024 * 1024);
		sizes.add("gigabyte", 1024 * 1024 * 1024);

		sizes.add("kbyte", 1024);
		sizes.add("mbyte", 1024 * 1024);
		sizes.add("gbyte", 1024 * 1024 * 1024);

		sizes.add("kb", 1024);
		sizes.add("mb", 1024 * 1024);
		sizes.add("gb", 1024 * 1024 * 1024);
	}

        template<typename scanner>
        struct definition {
                rule<scanner> const &start(void) const {
			return file;
		}

		assertion<errors> expect_semicolon;
		assertion<errors> expect_close_brace;
		assertion<errors> expect_equals;
		assertion<errors> expect_open_brace;

		struct push_back_impl {
			template<typename C, typename I>
			struct result {
				typedef void type;
			};

			template<typename C, typename I>
			void operator() (C &c, I const &i) const {
				c.push_back(i);
			}
		};

		rule<scanner> tstring, qstring, semicolon, equals,
				close_brace, open_brace;
		rule<scanner> file, require_value;
		rule<scanner, time_closure::context_t> time_m, atime;
		rule<scanner, size_closure::context_t> size_m, asize;
		rule<scanner, timev_closure::context_t> time;
		rule<scanner, sizev_closure::context_t> size;
		rule<scanner, value_closure::context_t> value;
		rule<scanner, block_closure::context_t> block;
		rule<scanner, avalue_closure::context_t> avalue;

		error_handler err;
		guard<errors> errguard;
		phoenix::function<push_back_impl> const push_back;

                definition(conf_parser_impl const &self)
			: expect_semicolon(semicolon_expected)
			, expect_close_brace(close_brace_expected)
			, expect_equals(equals_expected)
			, expect_open_brace(open_brace_expected)
			, push_back(push_back_impl())
			{
/*
 * Conf grammar definition.
 */
tstring = lexeme_d[chset<>("a-zA-Z_") >> *chset<>("a-zA-Z0-9_-")];
qstring = lexeme_d[confix_p('"', *c_escape_ch_p, '"')];
semicolon = expect_semicolon(ch_p(';'));
equals = expect_equals(ch_p('='));
open_brace = expect_open_brace(ch_p('{'));
close_brace = expect_close_brace(ch_p('}'));

file = *block;
require_value = value;

block	=  (tstring[block.name = construct_<string>(arg1, arg2)] 
	>> !confix_p('"', (*c_escape_ch_p)[block.key = construct_<string>(arg1, arg2)], '"')
	>> errguard(open_brace)[err]
	>> *errguard(require_value)[err]
	>> errguard(close_brace)[err]
	>> errguard(semicolon)[err])[push_back(self.blocks,
			       construct_<struct block>(block.name, 
						        block.key,
						        block.values))];

value	=  tstring [value.name = construct_<string>(arg1, arg2),
		    value.declpos = construct_<file_position>(
					phoenix::bind(&preprocessor::iterator::get_position) (arg1))]

	>> errguard(equals)[err]
	>> errguard(list_p(avalue, ','))[err]
	>> errguard(semicolon)[err] 
		[push_back(block.values, 
			   construct_<value_t>(
				value.name,
				value.declpos,
				value.values))];

avalue	= (size  [push_back(value.values, avalue.value)])
	| (time  [push_back(value.values, avalue.value)])
	| str_p("yes") [push_back(value.values, true)]
	| str_p("no")  [push_back(value.values, false)]
	| int_p      [push_back(value.values, arg1)]
	| tstring     [push_back(value.values, construct_<u_string>(arg1, arg2))]
	| confix_p('"', (*c_escape_ch_p)
			[push_back(value.values, construct_<q_string>(arg1, arg2))], '"')
	;

/* size values: "30 megabytes" */
size = size_m[avalue.value = construct_<avalue_t>(size.val)];

asize = int_p[size_m.value = construct_<size_q>(arg1)] >> 
		lexeme_d
		[
			as_lower_d
			[
				self.sizes[size_m.multiplier = construct_<scalar_q>(arg1)]
			]
			>> !ch_p('s')
		]
	;

size_m	= +(asize [size.val += size_m.value * size_m.multiplier]);

/* time values: "1 hour 30 seconds" */
time = time_m[avalue.value = construct_<avalue_t>(time.val)];

atime = int_p[time_m.value = construct_<time_q>(arg1)] >> 
		lexeme_d
		[
			as_lower_d
			[
				self.times[time_m.multiplier = construct_<scalar_q>(arg1)]
			]
			>> !ch_p('s')
		];

time_m	= +(atime [time.val += time_m.value * time_m.multiplier]);
	
/**/

BOOST_SPIRIT_DEBUG_RULE(block);
BOOST_SPIRIT_DEBUG_RULE(string);
BOOST_SPIRIT_DEBUG_RULE(qstring);
BOOST_SPIRIT_DEBUG_RULE(value);
BOOST_SPIRIT_DEBUG_RULE(avalue);
BOOST_SPIRIT_DEBUG_RULE(file);
BOOST_SPIRIT_DEBUG_RULE(size);
BOOST_SPIRIT_DEBUG_RULE(time);
		}
	};
};

confgrammar::confgrammar(expr::parser const &expr)
	: _expr(expr)
{
}

vector<block>
confgrammar::parse(string const &file)
{
	typedef char char_t;
	typedef preprocessor::iterator iterator_t;
	typedef scanner<iterator_t> scanner_t;
	conf_parser_impl g;
	conf_parser_impl::comment_parser skip;

	vector<block> result;

	error_handler err;

	try {
		preprocessor p(file, _expr);

		parse_info<iterator_t> info = spirit::parse(p.begin(), p.end(),
			g[var(result) = arg1], 
			comment_p("/*", "*/") | comment_p("#") | chset<>("\n\t "));
		if (!info.full)
			throw parser_error("failed to parse configuration file");
	} catch (exception &e) {
		throw parser_error(e.what());
	}

	return result;
}
