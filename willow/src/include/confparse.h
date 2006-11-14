/* @(#) $Id$ */
/* From: $Nightmare: nightmare/include/config.h,v 1.32.2.2.2.2 2002/07/02 03:41:28 ejb Exp $ */
/* From: newconf.h,v 7.36 2005/03/21 22:42:10 leeh Exp */
/* From: newconf.h 2651 2006-10-13 18:54:49Z river */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * confparse: configuration parser.
 */

#ifndef CONFPARSE_H
#define CONFPARSE_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <string>
#include <map>
#include <vector>
#include <utility>
#include <cassert>

using std::map;
using std::vector;
using std::pair;

#include "willow.h"

/*
 * the config tree.
 *
 * it's structured as a tree, e.g. this config file:
 *
 * server {
 *   name = "foo";
 * };
 * oper "bar" {
 *   class = "opers";
 * };
 *
 * would produce the keys:
 *
 *  /server/name    = "foo"
 *  /oper=bar/class = "opers"
 *
 * consumers can either look up a key by name, or traverse the tree
 * and receive all keys below a certain point.
 */

namespace conf {

extern string linebuf;
extern int curpos;
extern string current_file;
extern int lineno;
extern int conf_parse_error;

struct declpos {
		declpos(string const &file_, int line_, int pos_)
			: file(file_), line(line_), pos(pos_) {}
		declpos() : file(""), line(0), pos(0) {}

	string	format(void) const {
		return file + "(" + lexical_cast<string>(line) + ")";
	}

	static declpos here() {
		return declpos(current_file, lineno, curpos);
	}

	string	file;
	int	line, pos;
};

enum cv_type {
	cv_int	= 1,
	cv_string,
	cv_qstring,
	cv_time,
	cv_yesno
};

struct avalue {
		avalue();

	string	av_strval;
	long	av_intval;
	int	av_type;
};

struct value {
			 value(declpos const &pos);

	void	report_error	(const char *, ...) const;
	void	vreport_error	(const char *, va_list) const;
	size_t	nvalues		(void) const;
	bool	is_single	(cv_type) const;

	string           cv_name;
        vector<avalue>   cv_values;     /* list of conf_avalue_t                */
        bool             cv_touched;       /* 1 if someone touched this item       */
	declpos		 cv_pos;
};

struct tree_entry {
				tree_entry(declpos const &);
	void	 report_error	(const char *, ...) const;
	void	 vreport_error	(const char *, va_list) const;
	value 	*operator/	(string const &value);
	void	 add		(value const &);

        string			item_name;     /* e.g. "oper"                          */
        string			item_key;      /* e.g. "bar"                           */
        map<string, value>	item_values;
	declpos			item_pos;
        bool			item_unnamed;
        bool			item_touched;
        bool			item_is_template;
};

struct tree {
	void	reset();
	/*
	 * lookup a key by name.  warning, this is slow!  it should only
	 * be called once per rehash, cache the value somewhere (for the
	 * core ircd this is done in ConfigFileEntry etc.)
	 *
	 * conf_find_tree_entry only finds keys with no name.
	 * conf_find_named_tree_entry finds a key with a name (e.g.
	 * conf_find_named_tree_entry("operator", "god")).
	 *
	 * if found, the key is touched.
	 * if the key doesn't exist, returns NULL.
	 *
	 * conf_iterate_tree_entries() finds all the entries of a certain type.  the
	 * void* parameter is used to store state; pass it in as NULL on the first call.
	 * NULL is returned after the last matching entry.  all iterated entries are
	 * touched.
	 *
	 */
	bool		add		(tree_entry const &);
	tree_entry	*find_item	(tree_entry const &);
	tree_entry	*find		(string const &key);
	tree_entry	*find		(string const &key, string const &name);
	tree_entry	*find_or_new	(string const &block, string const &name, declpos const &pos,
					 bool unnamed, bool is_template);

	vector<tree_entry>	entries;
};

extern tree global_conf_tree;
#ifdef NEED_PARSING_TREE
extern tree parsing_tree;
#endif

tree	*parse_file	(string const &file);
void	 add_if_entry	(string const &name, bool v);

string type_name(cv_type);

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

template<cv_type type>
struct simple_value : callable<bool> {
	bool operator() (tree_entry &, value &v) const {
		if (!v.is_single(type)) {
			v.report_error("expected single %s", type_name(type).c_str());
			return false;
		}
		return true;
	}
};
typedef simple_value<cv_int> simple_int_t;
typedef simple_value<cv_yesno> simple_yesno_t;
typedef simple_value<cv_time> simple_time_t;
extern simple_int_t simple_int;
extern simple_yesno_t simple_yesno;
extern simple_time_t simple_time;

struct simple_range : callable<bool> {
	simple_range(int min_, int max_ = INT_MAX) : min(min_), max(max_) {}
	bool operator() (tree_entry &, value &v) const {
		if (!v.is_single(cv_int)) {
			v.report_error("expected single integer");
			return false;
		}
		int i = v.cv_values[0].av_intval;
		if (i < min || i > max) {
			v.report_error("value must be between %d and %d", min, max);
			return false;
		}
		return true;
	}
	int	min, max;
};

template<cv_type string_type>
struct nonempty_astring : callable<bool> {
	bool operator() (tree_entry &, value &v) const {
		if (!v.is_single(string_type)) {
			v.report_error("expected single string");
			return false;
		}
		if (v.cv_values[0].av_strval.empty()) {
			v.report_error("expected non-empty string");
			return false;
		}
		return true;
	}
};
typedef nonempty_astring<cv_string> nonempty_string_t;
typedef nonempty_astring<cv_qstring> nonempty_qstring_t;
extern nonempty_string_t nonempty_string;
extern nonempty_qstring_t nonempty_qstring;

struct ip_address_list_t : callable<bool> {
	bool operator() (tree_entry &, value &v) const {
	pair<string,string>	ip;
	vector<avalue>::iterator	it = v.cv_values.begin(),
					end = v.cv_values.end();
		for (; it != end; ++it) {
			if (it->av_type != cv_qstring) {
				v.report_error("IP address must be quoted string");
				return false;
			}
			if (!parse_ip(it->av_strval, ip)) {
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
	vector<avalue>::iterator	it = v.cv_values.begin(),
					end = v.cv_values.end();
		for (; it != end; ++it) {
			parse_ip(it->av_strval, ip);
			list.push_back(ip);
		}
	}
};

template<typename T>
struct set_simple : callable<void> {
	set_simple(T& sv_) : sv(sv_) {}
	void operator() (tree_entry &, value &v) const {
		sv = v.cv_values[0].av_intval;
	}
	T& sv;
};
template<typename T>
struct set_simple<atomic<T> > : callable<void> {
	atomic<T>	&sv;
	set_simple(atomic<T>& sv_) : sv(sv_) {}
	void operator() (tree_entry &, value &v) const {
		sv = v.cv_values[0].av_intval;
	}
};

struct set_astring : callable<void> {
	set_astring(string& sv_) : sv(sv_) {}
	void operator() (tree_entry &, value &v) const {
		sv = v.cv_values[0].av_strval;
	}
	string& sv;
};

typedef set_astring		set_string;
typedef set_astring		set_qstring;
typedef set_simple<time_t>	set_time;
typedef set_simple<bool>	set_yesno;
typedef set_simple<int>		set_int;
typedef set_simple<atomic<time_t> >	set_atime;
typedef set_simple<atomic<bool> >	set_abool;
typedef set_simple<atomic<int> >	set_aint;

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

struct block_definer {
	block_definer(conf_definer &parent_, string const &name, int flags);
	~block_definer();

	template<typename Vt, typename St>
	block_definer &value(string const &name, Vt const &v, St const &s) {
		values.insert(make_pair(name, new value_definer(name, v, s)));
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

	conf_definer			&parent;
	string				 name;
	map<string, value_definer *>	 values;
	ender<bool>			*vefn;
	ender<void>			*sefn;
	int				 flags;
};

/* get the first string or int value from an avalue */
#define CONF_FIRST(value) ((value).cv_values[0])
#define CONF_ASTRVAL(value) (				\
        assert((value).is_single(cv_string)		\
	       || (value).is_single(cv_qstring)),	\
        CONF_FIRST(value).av_strval)

#define CONF_AINTVAL(value) (				\
	assert((value).is_single(cv_int)		\
	       || (value).is_single(cv_time)		\
	       || (value).is_single(cv_yesno)),		\
	CONF_FIRST(value).av_intval)

/*
 * these are mostly internal to the configuration parser.
 */

tree_entry	*new_tree_entry_from_template(tree &t, string const &,
					      string const &, string const &,
					      declpos const &, bool unnamed, bool is_template);
value		*value_from_variable(string const &name, string const &varname, 
				     declpos const &);

void	report_parse_error	(const char *, ...);
void	catastrophic_error	(const char *, ...);
void	confparse_init		(void);
void	handle_pragma		(string const &);
bool	if_true			(string const &);
void	add_variable		(value *value);
void	add_variable_simple	(const char *, const char *);
bool	find_include		(string &);

} // namespace conf

extern FILE *yyin;      /* same         */
extern "C" int yylex(void);        /* same         */
extern "C" int yyparse(void);      /* from parser  */
extern "C" void yyerror(const char *);

#endif
