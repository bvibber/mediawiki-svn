/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* confgrammar: Spirit grammar for confparse				*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#ifndef CONFGRAMMAR_H
#define CONFGRAMMAR_H

#include <iostream>
#include <vector>
#include <exception>

#include <boost/mpl/vector_c.hpp>
#include <boost/mpl/equal.hpp>
#include <boost/mpl/plus.hpp>
#include <boost/mpl/minus.hpp>
#include <boost/mpl/transform.hpp>
#include <boost/mpl/placeholders.hpp>
#include <boost/static_assert.hpp>
#include <boost/variant.hpp>
#include <boost/spirit.hpp>
using std::vector;
using std::ostream;
using std::exception;
using boost::variant;

namespace mpl = boost::mpl;
namespace mpp = boost::mpl::placeholders;

#include "loreley.h"
#include "util.h"
#include "preprocessor.h"
#include "expr.h"

struct parser_error : exception {
	parser_error(char const *e) : _err(e) {}
	char const *what(void) const throw() {
		return _err;
	}

private:
	char const *_err;
};

enum string_type {
	string_tag,
	quoted_string_tag
};

template<string_type tag>
struct tagged_string {
	tagged_string() {}
	tagged_string(string const &s, file_position const &pos_)
		: v(s), pos(pos_) {}

	tagged_string(tagged_string const &other)
		: v(other.v), pos(other.pos) {}

	template<typename iter>
	tagged_string(iter begin, iter end)
		: v(begin, end)
		, pos(begin.get_position()) {}

	tagged_string &operator= (tagged_string const &other) {
		v = other.v;
		pos = other.pos;
		return *this;
	}

	string const &value(void) const {
		return v;
	}

	string &value(void) {
		return v;
	}

	file_position const &position(void) const {
		return pos;
	}

	operator string const& (void) const {
		return v;
	}

private:
	string		v;
	file_position	pos;
};

typedef tagged_string<string_tag> u_string;
typedef tagged_string<quoted_string_tag> q_string;

typedef mpl::vector_c<int64_t, 0, 0> scalar_d;
typedef mpl::vector_c<int64_t, 1, 0> time_d;
typedef mpl::vector_c<int64_t, 0, 1> size_d;

template<typename T, typename D>
struct quantity {
	quantity() : _value(0) {}
	explicit quantity(T v) : _value(v) {}
	explicit quantity(quantity<T, scalar_d> const &o)
		: _value(o.value()) {}

	template<typename D2>
	quantity(quantity<T, D2> const &other)
		: _value(other.value()) {
		BOOST_STATIC_ASSERT((
			mpl::equal<D, D2>::type::value
		));
	}

	quantity<T, D> &operator= (quantity<T, D> const &o) {
		_value = o.value();
		return *this;
	}

	template<typename D2>
	quantity<T, D> &operator= (quantity<T, D> const &o) {
		BOOST_STATIC_ASSERT((
			mpl::equal<D, D2>::type::value
		));
		_value = o.value();
		return *this;
	}

	quantity<T, D> &operator+= (quantity<T, D> const &o) {
		_value += o._value;
		return *this;
	}

	quantity<T, D> &operator-= (quantity<T, D> const &o) {
		_value -= o._value;
		return *this;
	}

	template<typename D2>
	quantity<T, D> &operator*= (quantity<T, D2> const &o) {
		*this = *this * o;
		return *this;
	}

	template<typename D2>
	quantity<T, D> &operator/= (quantity<T, D2> const &o) {
		*this = *this / o;
		return *this;
	}

	T value(void) const { return _value; }
private:
	T	_value;
};

template<typename T, typename D>
quantity<T, D>
operator+ (quantity<T, D> a, quantity<T, D> b)
{
	return quantity<T, D>(a.value() + b.value());
}

template<typename T, typename D>
quantity<T, D>
operator- (quantity<T, D> a, quantity<T, D> b)
{
	return quantity<T, D>(a.value() + b.value());
}

template<typename T, typename D1, typename D2>
quantity<T, typename mpl::transform<D1, D2, mpl::plus<mpp::_1, mpp::_2> >::type>
operator* (quantity<T, D1> a, quantity<T, D2> b)
{
	return quantity<T, typename mpl::transform<D1, D2,
				mpl::plus<mpp::_1, mpp::_2> >::type
		>(a.value() * b.value());
}

template<typename T, typename D1, typename D2>
quantity<T, typename mpl::transform<D1, D2, mpl::minus<mpp::_1, mpp::_2> >::type>
operator/ (quantity<T, D1> a, quantity<T, D2> b)
{
	return quantity<T, typename mpl::transform<D1, D2,
				mpl::minus<mpp::_1, mpp::_2> >::type
		>(a.value() / b.value());
}

template<typename T, typename D>
ostream&
operator<< (ostream &o, quantity<T, D> q)
{
	o << q.value();
	return o;
}

typedef quantity<bool, scalar_d> bool_q;
typedef quantity<int64_t, scalar_d> scalar_q;
typedef quantity<int64_t, time_d> time_q;
typedef quantity<int64_t, size_d> size_q;

typedef variant<
		u_string,
		q_string,
		bool_q,
		scalar_q,
		time_q,
		size_q,
		boost::spirit::nil_t /* needed for parse failures */
	> avalue_t;

struct value_t {
	string		 name;
	file_position	 pos;
	vector<avalue_t> values;

	value_t(string const &name_,
		file_position const &pos_,
		vector<avalue_t> const &vals_) 
		: name(name_), pos(pos_), values(vals_) {}
};

struct block {
	block();
	block(string const &name_, string const &value_, vector<value_t> const &values_)
		: name(name_), key(value_), values(values_) {}

	string name;
	string key;

	vector<value_t> values;
};

struct confgrammar {
	confgrammar(expr::parser const &expr);
	vector<block> parse(string const &file);

private:
	expr::parser const &_expr;
};

#endif	/* CONFGRAMMAR_H */
