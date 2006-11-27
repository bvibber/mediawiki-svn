/* Willow: Lightweight HTTP reverse-proxy.                              */
/* preprocessor: cpp-style preprocessor for config parsing		*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include <utility>
#include <boost/spirit.hpp>
using std::pair;
using std::make_pair;
using boost::spirit::file_iterator;

#include "preprocessor.h"

file_position::file_position(string const &f, int l, int p)
	: file(f), line(l), col(p) {}

file_position::file_position(file_position const &o)
	: file(o.file), line(o.line), col(o.col) {}

file_position &
file_position::operator= (file_position const &o) {
	file = o.file;
	line = o.line;
	col = o.col;
	return *this;
}

ostream&
operator<< (ostream &o, file_position const &p) {
	o << p.file << '(' << p.line << ')';
	return o;
}

preprocessor::iterator::iterator()
	: _cpp(NULL) {}

preprocessor::iterator::iterator(preprocessor const &cpp, 
				 vector<char>::const_iterator it)
	: _cpp(&cpp)
	, _it(it) {
}

preprocessor::iterator::iterator(preprocessor::iterator::iterator const &other)
	: _cpp(other._cpp)
	, _it(other._it) {
}

preprocessor::iterator &
preprocessor::iterator::operator= (preprocessor::iterator const &other) {
	_cpp = other._cpp;
	_it = other._it;
	return *this;
}
			
preprocessor::iterator &
preprocessor::iterator::operator++(void) {
	++_it;
	return *this;
}

preprocessor::iterator
preprocessor::iterator::operator++(int) {
iterator	ret (*this);
	++_it;
	return ret;
}

preprocessor::iterator &
preprocessor::iterator::operator--(void) {
	--_it;
	return *this;
}

preprocessor::iterator
preprocessor::iterator::operator--(int) {
iterator	ret (*this);
	--_it;
	return ret;
}

preprocessor::iterator::reference
preprocessor::iterator::operator*(void) const {
	return *_it;
}

bool
preprocessor::iterator::operator< (preprocessor::iterator const &other) const {
	return _it < other._it;
}

bool
preprocessor::iterator::operator== (preprocessor::iterator const &other) const {
	return !(*this < other) && !(other < *this);
}

bool
preprocessor::iterator::operator!= (preprocessor::iterator const &other) const {
	return !(*this == other);
}

bool
preprocessor::iterator::operator> (preprocessor::iterator const &other) const {
	return !(*this < other) && !(*this == other);
}

bool
preprocessor::iterator::operator<= (preprocessor::iterator const &other) const {
	return (*this < other) || (*this == other);
}

bool
preprocessor::iterator::operator>= (preprocessor::iterator const &other) const {
	return (*this > other) || (*this == other);
}

preprocessor::iterator::difference_type
preprocessor::iterator::operator- (preprocessor::iterator const &other) const {
	return _it - other._it;
}

preprocessor::iterator
preprocessor::iterator::operator- (size_type s) const {
	return iterator(*_cpp, _it - s);
}

preprocessor::iterator
preprocessor::iterator::operator+ (size_type s) const {
	return iterator(*_cpp, _it + s);
}

file_position
preprocessor::iterator::get_position(void) const {
	return _cpp->get_position(_it);
}

string const &
preprocessor::iterator::get_line(int i) const {
	return _cpp->get_line(i);
}

void
preprocessor::set_position(vector<char>::const_iterator n) 
{
	positions.insert(make_pair(distance(
		static_cast<vector<char> const &>(_data).begin(), n),
		file_position(_curfile, _curline, _curpos)));
}

string const &
preprocessor::get_line(int line) const 
{
	return lines[line - 1];
}

file_position
preprocessor::get_position(vector<char>::const_iterator it) const 
{
map<vector<char>::difference_type, file_position>::const_iterator posit;

	posit = positions.upper_bound(distance(_data.begin(), it));
	assert(posit != positions.end());

file_position			p(posit->second);
vector<char>::const_iterator	lit = it;

	p.col = 0;
	for (; lit > _data.begin() && *lit != '\n'; --lit)
		if (*lit == '\t')
			p.col += 8;
		else
			++p.col;
	return p;
}

preprocessor::preprocessor(string const &file, expr::parser const &expr)
	: _curfile(file)
	, _curline(1)
	, _curpos(0)
	, _expr(expr)
{
	file_iterator<> first(file.c_str());
	file_iterator<> last = first.make_end();
	process(first, last);
	_end = iterator(*this, _data.end());
}

preprocessor::iterator
preprocessor::begin(void)
{
	return iterator(*this, _data.begin());
}

preprocessor::const_iterator
preprocessor::begin(void) const
{
	return iterator(*this, _data.begin());
}

preprocessor::iterator
preprocessor::end(void)
{
	return iterator(*this, _data.end());
}

preprocessor::const_iterator
preprocessor::end(void) const 
{
	return iterator(*this, _data.end());
}

template<typename iterT>
void 
preprocessor::process(iterT begin, iterT end)
{
bool ateol = false;
bool ignoring = false;
iterT	lbegin = begin;
	for (; begin != end; ++begin) {
		switch (*begin) {
		case '%': {
			/* everything until the end of the line is a directive */
			if (ateol) {
				iterT eol = find(begin, end, '\n');
			string	expr(begin, eol);
				ignoring = evaluate_expr(begin, eol, ignoring);
				lines.push_back(string(lbegin, begin));
				set_position(_data.end());
				begin = eol;
				lbegin = begin + 1;
				_curline++;
				_curpos = 0;
				break;
			}
			goto normal;
		}

		case '\n':
			ateol = true;
			lines.push_back(string(lbegin, begin));
			set_position(_data.end());
			lbegin = begin + 1;
			_curline++;
			_curpos = 0;
			_data.push_back(*begin);
			break;

		normal:
		default:
			if (!ignoring) {
				_data.push_back(*begin);
			}
			if (*begin == '\t')
				_curpos += 8;
			else
				++_curpos;
			ateol = false;
			break;
		}
	}
}

template<typename iterT>
bool
preprocessor::evaluate_expr(iterT begin, iterT end, bool ignoring)
{
iterT	space = find(begin, end, ' ');
	if (itercmp(begin, space, "%if")) {
		if (space == end)
			return false;

	string	expr(space + 1, end);
		try {
			if (_expr.run(expr))
				return false;
		} catch (expr::error &e) {
			wlog.error(format("%s(%d): error parsing expression: %s")
				% _curfile % _curline % e.what());
			wlog.error('\t' + string(begin, end) + '\n');
			throw preprocessor_exception(e.what());
		}

		return true;
	} else if (itercmp(begin, space, "%else")) {
		return !ignoring;
	} else if (itercmp(begin, space, "%endif")) {
		return false;
	} else {
		wlog.error(format("%s(%d): unrecognised directive")
			% _curfile % _curline);
			wlog.error('\t' + string(begin, end) + '\n');
		throw preprocessor_exception(
			"syntax error in preprocessor directive");
	}
	return false;
}

template<typename iterT>
bool
preprocessor::itercmp(iterT begin, iterT end, char const *s)
{
int	len = strlen(s);
	if (distance(begin, end) != len)
		return false;
	return equal(begin, end, s);
}
