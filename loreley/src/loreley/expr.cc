/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* expr: simple expression parser.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"

#if defined(LORELEY_DEBUG) && 0
# define BOOST_SPIRIT_DEBUG
#endif

#include <boost/spirit.hpp>
#include <boost/spirit/attribute.hpp>
#include <boost/spirit/phoenix.hpp>
#include <boost/spirit/phoenix/casts.hpp>
#include <boost/spirit/phoenix/closures.hpp>
#include <boost/shared_ptr.hpp>

#include "util.h"
#include "loreley.h"
#include "expr.h"

namespace expr {
using std::vector;
using std::stack;
using std::exception;
using std::runtime_error;
using boost::spirit::rule;
using boost::spirit::grammar;
using boost::spirit::int_p;
using boost::spirit::space_p;
using boost::spirit::str_p;
using boost::spirit::chset;
using boost::spirit::symbols;
using boost::spirit::closure;
using boost::shared_ptr;
using phoenix::var;
using phoenix::arg1;
using phoenix::arg2;

struct expr_closure : closure<expr_closure, int64_t> {
	member1	val;
};

struct expression_parser_impl : 
	grammar<expression_parser_impl, expr_closure::context_t> {

	symbols<int64_t> variables;

	expression_parser_impl() {
		add_variable("true", 1);
		add_variable("false", 0);
	}

	void add_variable(string const &name, int64_t val) {
		variables.add(name.begin(), name.end(), val);
	}

	bool variable_defined(string const &name) const {
		return boost::spirit::find(variables, name.c_str()) != NULL;
	}

	int64_t run(string const &str) {
	int64_t	n;
		if (!boost::spirit::parse(str.begin(), str.end(), 
			(*this)[var(n) = arg1], space_p).full)
			throw expr::syntax_error();
		return n;
	}

	template<typename scanner>
	struct definition {
		rule<scanner> 
			const &start(void) const { return top; }

		rule<scanner, expr_closure::context_t>
			add_expr, atom, eq_expr, mul_expr, land_expr, lor_expr,
			lt_expr, band_expr, bxor_expr, bor_expr, lsht_expr, expr;
		rule<scanner> top, identifier;

		definition(expression_parser_impl const &self) {
identifier = chset<>("a-zA-Z_") >> *chset<>("a-zA-Z0-9_");

top = expr[self.val = arg1];

expr = lor_expr[expr.val = arg1];

lor_expr  = land_expr[lor_expr.val = arg1] 
		>> *("||" >> land_expr[lor_expr.val = lor_expr.val || arg1]);

land_expr = band_expr[land_expr.val = arg1]
		>> *("&&" >> band_expr[land_expr.val = land_expr.val && arg1]);

band_expr = bxor_expr[band_expr.val = arg1]
		>> *('&'  >> bxor_expr[band_expr.val &= arg1]);

bxor_expr =  bor_expr[bxor_expr.val = arg1]
		>> *('^'  >>  bor_expr[bxor_expr.val ^= arg1]);

bor_expr  =   eq_expr[bor_expr.val = arg1]
		>> *('|'  >>   eq_expr[bor_expr.val |= arg1]);

eq_expr   = lt_expr[eq_expr.val = arg1]
		>> *( ("==" >> lt_expr[eq_expr.val = (eq_expr.val == arg1)])
		    | ("!=" >> lt_expr[eq_expr.val = (eq_expr.val != arg1)])
		    );

lt_expr   =   lsht_expr[lt_expr.val = arg1]
		>> *( ('<'  >> lsht_expr[lt_expr.val = (lt_expr.val < arg1)])
		    | ('>'  >> lsht_expr[lt_expr.val = (lt_expr.val > arg1)])
		    | ("<=" >> lsht_expr[lt_expr.val = (lt_expr.val <= arg1)])
		    | (">=" >> lsht_expr[lt_expr.val = (lt_expr.val >= arg1)])
		    );

lsht_expr = add_expr[lsht_expr.val = arg1]
		>> *( ("<<" >> add_expr[lsht_expr.val <<= arg1])
		    | (">>" >> add_expr[lsht_expr.val >>= arg1])
		    );

add_expr  =  mul_expr[add_expr.val = arg1]
		>> *( ('+'  >>  mul_expr[add_expr.val += arg1])
		    | ('-'  >>  mul_expr[add_expr.val -= arg1])
		    );

mul_expr  =  atom[mul_expr.val = arg1]
		>> *( ('*'  >>  atom[mul_expr.val *= arg1])
		    | ('%'  >>  atom[mul_expr.val %= arg1])
		    | ('/'  >>  atom[mul_expr.val /= arg1])
		    );

atom =    int_p[atom.val = arg1]
	| ( '(' >> expr[atom.val = arg1] >> ')' )
	| ( '-' >> expr[atom.val = -arg1])
	| ( '!' >> expr[atom.val = !arg1])
	| ( '~' >> expr[atom.val = ~arg1])
	| self.variables[atom.val = arg1]
	| ( str_p("defined") >> '(' >> 
			self.variables[atom.val = 1] >> ')' )
	| ( str_p("defined") >> '(' >>
			identifier[atom.val = 0] >> ')')
	;

BOOST_SPIRIT_DEBUG_RULE(identifier);
BOOST_SPIRIT_DEBUG_RULE(expr);
BOOST_SPIRIT_DEBUG_RULE(lor_expr);
BOOST_SPIRIT_DEBUG_RULE(land_expr);
BOOST_SPIRIT_DEBUG_RULE(band_expr);
BOOST_SPIRIT_DEBUG_RULE(bxor_expr);
BOOST_SPIRIT_DEBUG_RULE(bor_expr);
BOOST_SPIRIT_DEBUG_RULE(lt_expr);
BOOST_SPIRIT_DEBUG_RULE(eq_expr);
BOOST_SPIRIT_DEBUG_RULE(lsht_expr);
BOOST_SPIRIT_DEBUG_RULE(add_expr);
BOOST_SPIRIT_DEBUG_RULE(mul_expr);
BOOST_SPIRIT_DEBUG_RULE(atom);

		}
	};
};

} // anonymous namespace

namespace expr {

expr::parser::parser()
{
	impl = new expression_parser_impl;
}

expr::parser::~parser()
{
	delete impl;
}

void
expr::parser::add_variable(string const &name, int64_t val)
{
	impl->add_variable(name, val);
}

bool
expr::parser::variable_defined(string const &name) const
{
	return impl->variable_defined(name);
}

int64_t
expr::parser::run(string const &str) const
{
	return impl->run(str);
}

} // namespace expr

#ifdef TEST
int
main(int argc, char *argv[])
{
expression_parser	p;
	try {
		cout << p.run(argv[1]) << '\n';
	} catch (expression_error &e) {
		cout << e.what() << '\n';
	}
}
#endif
