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

#ifndef PREPROCESSOR_H
#define PREPROCESSOR_H

#include <iterator>
#include <iostream>
#include <exception>
using std::exception;
using std::ostream;
using std::random_access_iterator_tag;

#include "willow.h"
#include "expr.h"

struct preprocessor_exception : exception {
	preprocessor_exception(char const *err) : _err(err) {}
	char const *what(void) const throw() {
		return _err;
	}
private:
	char const *_err;
};

struct file_position {
	file_position(string const &f, int l, int p);
	file_position(file_position const &o);
	file_position &operator= (file_position const &o);

	string file;
	int line;
	int col;
};

ostream& operator<< (ostream &o, file_position const &p);

struct preprocessor {
	struct iterator {
		typedef char value_type;
		typedef value_type const &reference;
		typedef reference const_reference;
		typedef value_type const *pointer;
		typedef vector<char>::difference_type difference_type;
		typedef vector<char>::size_type size_type;
		typedef random_access_iterator_tag iterator_category;

		preprocessor const *_cpp;
		vector<char>::const_iterator _it;

		iterator();
		iterator(preprocessor const &cpp, vector<char>::const_iterator it);
		iterator(iterator const &other);

		iterator& operator= (iterator const &other);
		iterator& operator++(void);
		iterator  operator++(int);
		iterator& operator--(void);
		iterator  operator--(int);
		reference operator*(void) const;

		bool operator< (iterator const &other) const;
		bool operator== (iterator const &other) const;
		bool operator!= (iterator const &other) const;
		bool operator> (iterator const &other) const;

		bool operator<= (iterator const &other) const;
		bool operator>= (iterator const &other) const;
		difference_type operator- (iterator const &other) const;
		iterator operator- (size_type s) const;
		iterator operator+ (size_type s) const;
		file_position get_position(void) const;
		string const &get_line(int i) const;
	};

	typedef iterator const_iterator;
	typedef char value_type;
	typedef value_type const &reference;
	typedef value_type const &const_reference;
	typedef vector<char>::size_type size_type;
	typedef vector<char>::difference_type difference_type;

	map<vector<char>::difference_type, file_position> positions;
	vector<string> lines;

	void set_position(vector<char>::const_iterator n);

	string const &get_line(int line) const;
	file_position get_position(vector<char>::const_iterator it) const;

	preprocessor(string const &file, expr::parser const &expr);

	iterator begin(void);
	const_iterator begin(void) const;
	iterator end(void);
	const_iterator end(void) const;

private:
	template<typename iterT>
	void process(iterT begin, iterT end);

	template<typename iterT>
	bool evaluate_expr(iterT begin, iterT end, bool ignoring);

	template<typename iterT>
	bool itercmp(iterT begin, iterT end, char const *s);

	vector<char>	_data;
	string		_curfile;
	int		_curline;
	int		_curpos;
	iterator	_end;
	expr::parser const &
			_expr;
};


#endif	/* PREPROCESSOR_H */
