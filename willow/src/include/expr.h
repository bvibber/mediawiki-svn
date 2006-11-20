/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * expr: simple expression parser.
 */

#ifndef EXPR_H
#define EXPR_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#define BOOST_SPIRIT_DEBUG

#include <iostream>
#include <vector>
#include <stack>
#include <functional>
#include <stdexcept>
#include <boost/spirit.hpp>
#include <boost/shared_ptr.hpp>

#include "util.h"

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
using boost::shared_ptr;

struct expression_error : runtime_error {
	expression_error(char const *what) : runtime_error(what) {}
};

struct stack_underflow : expression_error {
	stack_underflow() : expression_error("stack underflow in expression parser") {}
};

struct syntax_error : expression_error {
	syntax_error() : expression_error("syntax error in expression parser") {}
};

template<typename T>
struct bitwise_and {
	T operator() (T const &a, T const &b) const {
		return a & b;
	}
};

template<typename T>
struct bitwise_or {
	T operator() (T const &a, T const &b) const {
		return a | b;
	}
};

template<typename T>
struct bitwise_xor {
	T operator() (T const &a, T const &b) const {
		return a ^ b;
	}
};

template<typename T>
struct bitwise_not {
	T operator() (T const &a) const {
		return ~a;
	}
};

template<typename T>
struct shift_left {
	T operator() (T const &a, T const &b) const {
		return a << b;
	}
};

template<typename T>
struct shift_right {
	T operator() (T const &a, T const &b) const {
		return a >> b;
	}
};

struct expression_parser : public grammar<expression_parser> {
	typedef stack<int64_t> stack_t;
	struct operation {
		virtual ~operation() {}
		virtual void execute(stack_t &stack) = 0;
	};

	typedef vector<shared_ptr<operation> > program_t;

	template<int64_t value>
	struct op_literal : operation {
		virtual void execute(stack_t &s) {
			s.push(value);
		}

		struct add {
		program_t &_program;
			add(program_t &p)
				: _program(p)
			{}
		
			void operator() (int64_t) const {
				_program.push_back(shared_ptr<operation>(
					new op_literal<value>));
			}
			void operator() (string::const_iterator,
					 string::const_iterator) const {
				_program.push_back(shared_ptr<operation>(
					new op_literal<value>));
			}
		};
	};

	template<typename func>
	struct binary_operator : operation {
	func		 _f;
		virtual void execute(stack_t &s) {
		int64_t	a, b, r;
			/* note that args are pushed in reverse order -- so 
			 * the first op argument is the second on the stack
			 */
			b = s.top();
			s.pop();
			a = s.top();
			s.pop();
			r = _f(a, b);
			s.push(r);
		}

		struct add {
		program_t &_program;
			add(program_t &p)
				: _program(p)
			{}
		
			void operator() (string::const_iterator,
					 string::const_iterator) const {
				_program.push_back(shared_ptr<operation>(
					new binary_operator<func>));
			}
		};
	};

	template<typename func>
	struct unary_operator : operation {
	func	_f;
		virtual void execute(stack_t &s) {
		int64_t	v, r;
			v = s.top();
			s.pop();
			r = _f(v);
			s.push(r);
		}

		struct add {
		program_t &_program;
			add(program_t &p)
				: _program(p)
			{}
		
			void operator() (string::const_iterator,
					 string::const_iterator) const {
				_program.push_back(shared_ptr<operation>(
					new unary_operator<func>));
			}
		};
	};

	struct op_value : operation {
	int64_t	v;
		op_value(int64_t v_) : v(v_) {}
		virtual void execute(stack_t &s) {
			s.push(v);
		}

		struct add {
		program_t &_program;
			add(program_t &p)
				: _program(p)
			{}
	
			void operator() (int64_t v) const {
				_program.push_back(shared_ptr<operation>(
					new op_value(v)));
			}
		};
	};

	typedef binary_operator<std::plus           <int64_t> > op_add;
	typedef binary_operator<std::minus          <int64_t> > op_sub;
	typedef binary_operator<std::multiplies     <int64_t> > op_mul;
	typedef binary_operator<std::divides        <int64_t> > op_div;
	typedef binary_operator<std::modulus        <int64_t> > op_mod;
	typedef binary_operator<std::logical_and    <int64_t> > op_land;
	typedef binary_operator<std::logical_or     <int64_t> > op_lor;
	typedef binary_operator<std::less           <int64_t> > op_lt;
	typedef binary_operator<std::greater        <int64_t> > op_gt;
	typedef binary_operator<std::less_equal     <int64_t> > op_le;
	typedef binary_operator<std::greater_equal  <int64_t> > op_ge;
	typedef binary_operator<std::equal_to       <int64_t> > op_eq;
	typedef binary_operator<std::not_equal_to   <int64_t> > op_neq;
	typedef binary_operator<bitwise_and         <int64_t> > op_band;
	typedef binary_operator<bitwise_or          <int64_t> > op_bor;
	typedef binary_operator<bitwise_xor         <int64_t> > op_bxor;
	typedef binary_operator<shift_left          <int64_t> > op_lsht;
	typedef binary_operator<shift_right         <int64_t> > op_rsht;

	typedef unary_operator<bitwise_not          <int64_t> > op_bnot;
	typedef unary_operator<std::negate          <int64_t> > op_neg;
	typedef unary_operator<std::logical_not     <int64_t> > op_not;
	
	mutable program_t _program;
	stack_t stack;
	program_t &program(void) const {
		return _program;
	}

	symbols<int64_t> variables;

	expression_parser() {
		add_variable("true", 1);
		add_variable("false", 0);
	}

	void add_variable(string const &name, int val) {
		variables.add(name.begin(), name.end(), val);
	}

	bool variable_defined(string const &name) const {
		return boost::spirit::find(variables, name.c_str()) != NULL;
	}

	int64_t run(string const &str) {
		if (!boost::spirit::parse(str.begin(), str.end(), *this, space_p).full)
			throw syntax_error();

	program_t::iterator it = _program.begin(), end = _program.end();
		for (; it != end; ++it)
			(*it)->execute(stack);
		return stack.top();
	}

	template<typename scanner>
	struct definition {
		rule<scanner> const &start(void) const { return expr; }
		rule<scanner>	add_expr, atom, expr, mod_expr, eq_expr, neq_expr,
			mul_expr, div_expr, sub_expr, land_expr, lor_expr,
			lt_expr, gt_expr, le_expr, ge_expr, band_expr,
			bxor_expr, bor_expr, lsht_expr, rsht_expr, identifier;

		definition(expression_parser const &self) {
identifier = chset<>("a-zA-Z_") >> *chset<>("a-zA-Z0-9_");

expr = lor_expr;

lor_expr  = land_expr >> *(("||" >> land_expr)[ op_lor::add(self.program())]);
land_expr = band_expr >> *(("&&" >> band_expr)[op_land::add(self.program())]);
band_expr = bxor_expr >> *(('&'  >> bxor_expr)[op_band::add(self.program())]);
bxor_expr =  bor_expr >> *(('^'  >>  bor_expr)[op_bxor::add(self.program())]);
bor_expr  =   lt_expr >> *(('|'  >>   lt_expr)[ op_bor::add(self.program())]);
lt_expr   =   gt_expr >> *(('<'  >>   gt_expr)[  op_lt::add(self.program())]);
gt_expr   =   le_expr >> *(('>'  >>   le_expr)[  op_gt::add(self.program())]);
le_expr   =   ge_expr >> *(("<=" >>   ge_expr)[  op_le::add(self.program())]);
ge_expr   =  neq_expr >> *((">=" >>  neq_expr)[  op_ge::add(self.program())]);
neq_expr  =   eq_expr >> *(("!=" >>   eq_expr)[ op_neq::add(self.program())]);
eq_expr   = lsht_expr >> *(("==" >> lsht_expr)[  op_eq::add(self.program())]);
lsht_expr = rsht_expr >> *(("<<" >> rsht_expr)[op_lsht::add(self.program())]);
rsht_expr =  add_expr >> *((">>" >>  add_expr)[op_rsht::add(self.program())]);
add_expr  =  sub_expr >> *(('+'  >>  sub_expr)[ op_add::add(self.program())]);
sub_expr  =  mul_expr >> *(('-'  >>  mul_expr)[ op_sub::add(self.program())]);
mul_expr  =  mod_expr >> *(('*'  >>  mod_expr)[ op_mul::add(self.program())]);
mod_expr  =  div_expr >> *(('%'  >>  div_expr)[ op_mod::add(self.program())]);
div_expr  =      atom >> *(('/'  >>      atom)[ op_div::add(self.program())]);

atom =    int_p[op_value::add(self.program())] 
	| ( '(' >> expr >> ')' )
	| ( '-' >> expr )[op_neg::add(self.program())]
	| ( '!' >> expr )[op_not::add(self.program())]
	| ( '~' >> expr )[op_bnot::add(self.program())]
	| self.variables[op_value::add(self.program())]
	| ( str_p("defined") >> '(' >> 
			self.variables[op_literal<1>::add(self.program())] >> ')' )
	| ( str_p("defined") >> '(' >>
			identifier[op_literal<0>::add(self.program())] >> ')')
	;

BOOST_SPIRIT_DEBUG_RULE(identifier);
BOOST_SPIRIT_DEBUG_RULE(expr);
BOOST_SPIRIT_DEBUG_RULE(lor_expr);
BOOST_SPIRIT_DEBUG_RULE(land_expr);
BOOST_SPIRIT_DEBUG_RULE(band_expr);
BOOST_SPIRIT_DEBUG_RULE(bxor_expr);
BOOST_SPIRIT_DEBUG_RULE(bor_expr);
BOOST_SPIRIT_DEBUG_RULE(lt_expr);
BOOST_SPIRIT_DEBUG_RULE(gt_expr);
BOOST_SPIRIT_DEBUG_RULE(le_expr);
BOOST_SPIRIT_DEBUG_RULE(ge_expr);
BOOST_SPIRIT_DEBUG_RULE(neq_expr);
BOOST_SPIRIT_DEBUG_RULE(eq_expr);
BOOST_SPIRIT_DEBUG_RULE(lsht_expr);
BOOST_SPIRIT_DEBUG_RULE(rsht_expr);
BOOST_SPIRIT_DEBUG_RULE(add_expr);
BOOST_SPIRIT_DEBUG_RULE(sub_expr);
BOOST_SPIRIT_DEBUG_RULE(mul_expr);
BOOST_SPIRIT_DEBUG_RULE(mod_expr);
BOOST_SPIRIT_DEBUG_RULE(div_expr);
BOOST_SPIRIT_DEBUG_RULE(atom);

		}
	};
};

} // namespace expr
using expr::expression_parser;
using expr::expression_error;
using expr::stack_underflow;
using expr::syntax_error;

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

#endif
