/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* expr: simple expression parser.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef EXPR_H
#define EXPR_H

namespace expr {

struct error : runtime_error {
	error(char const *err) : runtime_error(err) {}
};

struct stack_underflow : error {
	stack_underflow() : error("stack underflow in expression parser") {}
};

struct syntax_error : error {
	syntax_error() : error("syntax error in expression parser") {}
};

struct expression_parser_impl;

/**
 * A maths expression parser.  This parses strings of the form "(2 * 5) / 7"
 * into 64-bit integer values.  Basic usage is very simple:
 *
 * \code
 * expression_parser p;
 * p.add_variable("x", 7);
 * int64_t result = p.run("(2 * 5) / x");
 * \endcode
 *
 * add_variable() adds a new variable to the parser, which can be used in
 * subsequent expressions.  There are two default variables: "true", with
 * the value 1, and "false" with the value 0.
 *
 * All expressions are evaluated using 64-bit integer maths.  The following
 * operators are supported in expressions:
 *
 * ! - + ~ * / % << >> < <= > >= == != & ^ | && || defined
 *
 * All have the same meaning as their C equivalents.  defined() is used to
 * test whether a variable exists: defined(x) has the value '1' if 'x' is
 * defined.
 */
struct parser {
	/**
	 * Construct a new parser.
	 */
	parser();

	/**
	 * Destructor.
	 */
	~parser();

	/**
	 * Add a new variable to this parser.
	 *
	 * \param name variable name
	 * \param val variable value
	 */
	void add_variable(string const &name, int64_t val);

	/**
	 * Test if a given variable has been defined in this parser.
	 *
	 * \param name name of variable to test
	 * \returns true if the variable exists, otherwise false
	 */
	bool variable_defined(string const &name) const;

	/**
	 * Run a given expression and return its value.
	 *
	 * \param str expression to run, in text format
	 * \returns result of the expression
	 * \throws syntax_error if the expression could not be parser
	 * \throws stack_underflow if the expression parser unexpectedly
	 * runs out of stack (this is an internal error that should never be
	 * seen).
	 */
	int64_t run(string const &str) const;

private:
	struct expression_parser_impl	*impl;
};

} // namespace expr

#endif
