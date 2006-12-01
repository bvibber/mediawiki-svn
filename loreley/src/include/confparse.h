/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* confparse: configuration parser.					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */
/* From: $Nightmare: nightmare/include/config.h,v 1.32.2.2.2.2 2002/07/02 03:41:28 ejb Exp $ */
/* From: newconf.h,v 7.36 2005/03/21 22:42:10 leeh Exp */
/* From: newconf.h 2651 2006-10-13 18:54:49Z river */

#ifndef CONFPARSE_H
#define CONFPARSE_H

#include <string>
#include <map>
#include <vector>
#include <utility>
#include <cassert>
#include <limits>

#include <boost/bind.hpp>
using std::numeric_limits;
using std::map;
using std::vector;
using std::pair;
using std::multimap;

#include "loreley.h"
#include "util.h"
#include "confgrammar.h"

namespace conf {

template<typename T>
bool is_type(avalue_t const &v) {
	return boost::get<T>(&v) != NULL;
}

template<typename T>
void
report_error(T &av, string const &err)
{
	av.position().error(
		boost::bind(
			static_cast<void (logger::*) (string const &)>(
				&logger::error), &wlog, _1), err);
}

template<typename T>
void
report_error(T &av, format const &err)
{
	report_error(av, str(err));
}

struct value {
		value(file_position const &pos);

	void	report_error	(string const &err) const;
	void	report_error	(format const &err) const {
		return report_error(str(err));
	}

	size_t	nvalues		(void) const;
	template<typename T>
	bool	is_single(void) {
		return cv_values.size() == 1 &&
			boost::get<T>(&cv_values[0]) != NULL;
	}

	template<typename T>
	bool	is_type(int n) {
		return boost::get<T>(&cv_values[n]) != NULL;
	}

	template<typename T>
	T const& get(int n = 0) {
		return boost::get<T>(cv_values[n]);
	}

	string           cv_name;
        vector<avalue_t> cv_values;     /* list of conf_avalue_t                */
        bool             cv_touched;    /* 1 if someone touched this item       */
	file_position	 cv_pos;
};

struct tree_entry {
		 tree_entry(file_position const &);
	void	 report_error	(const char *, ...) const;
	void	 vreport_error	(const char *, va_list) const;
	value 	*operator/	(string const &value);
	void	 add		(value const &);

	string		item_name;
	string		item_key;
	vector<value>	item_values;
	file_position	item_pos;
	bool		item_unnamed;
	bool		item_touched;
	bool		item_is_template;
};

struct tree {
	void	 reset();
	bool	 add		(tree_entry const &);

	vector<tree_entry>	entries;
};

extern tree global_conf_tree;
#ifdef NEED_PARSING_TREE
extern tree parsing_tree;
#endif

tree	*parse_file	(string const &file);
void	 add_if_entry	(string const &name, int64_t v);
bool	 if_defined	(string const &name);
void	 define_if	(string const &name);

template<typename T>
struct type_namer {
};

template<>
struct type_namer<scalar_q> {
	static char const *type;
};

template<>
struct type_namer<bool_q> {
	static char const *type;
};

template<>
struct type_namer<time_q> {
	static char const *type;
};

template<>
struct type_namer<size_q> {
	static char const *type;
};

template<>
struct type_namer<q_string> {
	static char const *type;
};

template<>
struct type_namer<u_string> {
	static char const *type;
};

template<typename T>
char const *type_name(void) {
	return type_namer<T>::type;
}

typedef bool (*function) (tree_entry &, value &);

struct value_definer;
struct block_definer;
struct conf_definer;

bool parse_ip (string const &, pair<string,string> &);

template<typename ret>
struct callable {
	virtual ~callable() {}
	virtual ret operator()(tree_entry &, value &) const = 0;
};

template<typename ret>
struct function_callable : callable<ret> {
	function_callable(ret (*f_)(tree_entry &, value &))
		: f(f_) {}

	ret operator()(tree_entry &e, value &v) const {
		return f(e, v);
	}

	ret (*f) (tree_entry &, value &);
};
typedef callable<bool> value_validator;
typedef callable<void> value_setter;

template<typename ret>
struct ender {
	virtual ~ender() {}
	virtual ret operator() (tree_entry &) const = 0;
};

template<typename ret>
struct function_ender : ender<ret> {
	function_ender(ret (*f_) (tree_entry &))
		: f(f_) {}
	ret operator() (tree_entry &e) const {
		return f(e);
	}
	ret (*f) (tree_entry &);
};

template<typename ret>
function_callable<ret> func(ret (*f)(tree_entry &, value &)) {
	return function_callable<ret>(f);
}
template<typename ret>
function_ender<ret> func(ret (*f)(tree_entry &)) {
	return function_ender<ret>(f);
}

template<typename type>
struct simple_value : callable<bool> {
	bool operator() (tree_entry &, value &v) const {
		if (!v.is_single<type>()) {
			v.report_error(format("expected single %s") % type_name<type>());
			return false;
		}
		return true;
	}
};
typedef simple_value<scalar_q> simple_int_t;
typedef simple_value<bool_q> simple_yesno_t;
typedef simple_value<time_q> simple_time_t;
typedef simple_value<size_q> simple_size_t;

extern simple_int_t simple_int;
extern simple_yesno_t simple_yesno;
extern simple_time_t simple_time;
extern simple_size_t simple_size;

struct simple_range : callable<bool> {
	simple_range(int64_t min_, int64_t max_ = numeric_limits<int64_t>::max())
		: min(min_), max(max_) {}
	bool operator() (tree_entry &, value &v) const {
		if (!v.is_single<scalar_q>()) {
			v.report_error("expected single integer");
			return false;
		}
		int64_t i = boost::get<scalar_q>(v.cv_values[0]).value();
		if (i < min || i > max) {
			v.report_error(format("value must be between %d and %d")
				% min % max);
			return false;
		}
		return true;
	}
	int64_t	min, max;
};

template<typename string_type>
struct nonempty_astring : callable<bool> {
	bool operator() (tree_entry &, value &v) const {
		if (!v.is_single<string_type>()) {
			v.report_error("expected single string");
			return false;
		}
		if (v.get<string_type>(0).value().empty()) {
			v.report_error("expected non-empty string");
			return false;
		}
		return true;
	}
};
typedef nonempty_astring<u_string> nonempty_string_t;
typedef nonempty_astring<q_string> nonempty_qstring_t;
extern nonempty_string_t nonempty_string;
extern nonempty_qstring_t nonempty_qstring;

struct ip_address_list_t : callable<bool> {
	bool operator() (tree_entry &, value &v) const {
	pair<string,string>	ip;
	vector<avalue_t>::iterator	it = v.cv_values.begin(),
					end = v.cv_values.end();
		for (; it != end; ++it) {
			if (!is_type<q_string>(*it)) {
				v.report_error("IP address must be quoted string");
				return false;
			}
			if (!parse_ip(boost::get<q_string>(*it).value(), ip)) {
				v.report_error("could not parse IP address");
				return false;
			}
		}
		return true;
	}
};
extern ip_address_list_t ip_address_list;

struct add_ip : callable<void> {
	vector<pair<string,string> > &list;
	add_ip(vector<pair<string,string> > &list_)
		: list(list_) {}

	void operator() (tree_entry &, value &v) const {
	pair<string,string>	ip;
	vector<avalue_t>::iterator	it = v.cv_values.begin(),
					end = v.cv_values.end();
		for (; it != end; ++it) {
			parse_ip(boost::get<q_string>(*it).value(), ip);
			list.push_back(ip);
		}
	}
};

template<typename T, typename U = T>
struct set_simple : callable<void> {
	set_simple(U& sv_) : sv(sv_) {}
	void operator() (tree_entry &, value &v) const {
		sv = v.get<T>(0);
	}
	U& sv;
};

template<typename T, typename U = T>
struct set_quantity : callable<void> {
	set_quantity(U& sv_) : sv(sv_) {}
	void operator() (tree_entry &, value &v) const {
		sv = v.get<T>(0).value();
	}
	U& sv;
};

template<typename T, typename U>
struct set_simple<U, atomic<T> > : callable<void> {
	atomic<T>	&sv;
	set_simple(atomic<T>& sv_) : sv(sv_) {}
	void operator() (tree_entry &, value &v) const {
		sv = v.get<U>(0).value();
	}
};

template<typename T>
struct set_astring : callable<void> {
	set_astring(string& sv_) : sv(sv_) {}
	void operator() (tree_entry &, value &v) const {
		sv = v.get<T>(0).value();
	}
	string& sv;
};


typedef set_astring<u_string>			set_string;
typedef set_astring<q_string>			set_qstring;
typedef set_quantity<time_q, time_t>		set_time;
typedef set_quantity<size_q, size_t>		set_size;
typedef set_quantity<bool_q, bool>		set_yesno;
typedef set_quantity<scalar_q, int>		set_int;
typedef set_quantity<scalar_q, long>		set_long;
typedef set_simple<time_q, atomic<time_t> >	set_atime;
typedef set_simple<size_q, atomic<size_t> >	set_asize;
typedef set_simple<bool_q, atomic<bool> >	set_abool;
typedef set_simple<scalar_q, atomic<int> >	set_aint;

struct accept_any_t : callable<bool> {
	bool operator() (tree_entry &, value &) const {
		return true;
	}
};
extern accept_any_t accept_any;

struct ignore_t : callable<void> {
	void operator() (tree_entry &, value &) const {
	}
};
extern ignore_t ignore;

struct conf_definer {
	conf_definer() {};
	~conf_definer();

	block_definer &block(string const &name, int flags = 0);
	vector<block_definer *> blocks;

	bool validate(tree &) const;
	void set(tree &) const;
};

struct value_definer {
	template<typename Vt, typename St>
	value_definer(string const &, Vt const &v_, St const &s_) {
		vv = new Vt(v_);
		vs = new St(s_);
	}
	~value_definer() {
		delete vv;
		delete vs;
	}

	bool validate(tree_entry &e, value &v);
	void set(tree_entry &e, value &v);

	value_validator const	*vv;
	value_setter const	*vs;
};

extern const int require_name;

struct block_definer : noncopyable {
	block_definer(conf_definer &parent_, string const &name, int flags);
	~block_definer();

	template<typename Vt, typename St>
	block_definer &value(string const &name_, Vt const &v, St const &s) {
		values.insert(make_pair(name_, new value_definer(name_, v, s)));
		return *this;
	}

	template<typename Vt, typename St>
	block_definer &end(Vt vefn_, St sefn_) {
		vefn = new Vt(vefn_);
		sefn = new St(sefn_);
		return *this;
	}

	template<typename St>
	block_definer &end(St sefn_) {
		sefn = new St(sefn_);
		return *this;
	}

	block_definer &block(string const &name, int flags = 0);
	bool validate(tree_entry &e);
	void set(tree_entry &e); 

private:
	friend class conf_definer;

	conf_definer			&parent;
	string				 name;
	map<string, value_definer *>	 values;
	ender<bool>			*vefn;
	ender<void>			*sefn;
	int				 flags;
};


/*
 * These are mostly internal to the configuration parser.
 */

tree_entry	*new_tree_entry_from_template(tree &t, string const &,
					      string const &, string const &,
					      file_position const &, bool unnamed, bool is_template);
value		*value_from_variable(string const &name, string const &varname, 
				     file_position const &);

void	report_parse_error	(const char *, ...);
void	catastrophic_error	(const char *, ...);
void	confparse_init		(void);
void	handle_pragma		(string const &);
bool	if_true			(string const &);
void	add_variable		(value *value);
void	add_variable_simple	(const char *, const char *);
bool	find_include		(string &);

} // namespace conf

#endif
