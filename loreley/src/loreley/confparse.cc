/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* confparse: configuration file parser					*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include <sys/utsname.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#include <vector>
#include <string>
#include <utility>
#include <cstdarg>
#include <cerrno>
#include <stdio.h>
using std::vector;
using std::make_pair;

#include <boost/bind.hpp>

#define NEED_PARSING_TREE

#include "confparse.h"
#include "confgrammar.h"
#include "loreley.h"
#include "log.h"
#include "backend.h"
#include "config.h"
#include "format.h"
#include "expr.h"

namespace conf {

char const *type_namer<scalar_q>::type = "integer value";
char const *type_namer<bool_q>::type = "boolean value";
char const *type_namer<time_q>::type = "time value";
char const *type_namer<size_q>::type = "size value";
char const *type_namer<u_string>::type = "unquoted string";
char const *type_namer<q_string>::type = "quoted string";

expr::parser if_parser;

tree global_conf_tree;
map<string, value> variable_list;
map<string, bool> if_table;

vector<string> ipaths;
static void add_ipath(string const &);
bool parse_error;

tree parsing_tree;

const int require_name = 0x1;

simple_int_t simple_int;
simple_yesno_t simple_yesno;
simple_time_t simple_time;
simple_size_t simple_size;

nonempty_string_t nonempty_string;
nonempty_qstring_t nonempty_qstring;
ignore_t ignore;
accept_any_t accept_any;
ip_address_list_t ip_address_list;

tree *
parse_file(string const &file)
{
	parsing_tree.reset();
	
	if (if_table.empty()) {
	struct utsname	unm;
		uname(&unm);
		add_if_entry("true", 1);
		add_if_entry("false", 0);
		add_if_entry(unm.sysname, 1);
	}

#if 0
	if ((yyin = fopen(file.c_str(), "r")) == NULL) {
		wlog.error(format("could not open configuration file %s: %s") 
				% file % strerror(errno));
		return NULL;
	}
	if (yyparse() || parse_error)
		return NULL;
#endif

	confgrammar g(if_parser);
	vector<block> result;

	try {
		result = g.parse(file);
	} catch (parser_error &e) {
		wlog.error(format("cannot parse configuration file: %s")
			% e.what());
		return NULL;
	}

	vector<block>::iterator blockit, blockend;
	vector<value_t>::iterator valueit, valueend;
	vector<avalue_t>::iterator avalueit, avalueend;

	for (blockit = result.begin(), blockend = result.end();
             blockit != blockend; ++blockit)
	{
	tree_entry	e = file_position();
		e.item_name = blockit->name;
		e.item_key = blockit->key;
		
		for (valueit = blockit->values.begin(), valueend = blockit->values.end();
		        valueit != valueend; ++valueit)
		{
		value	v(valueit->pos);
			v.cv_name = valueit->name;
			v.cv_values = valueit->values;
		
			e.add(v);
		}
		parsing_tree.add(e);
	}

	return &parsing_tree;
}

void
free_newconf_state(void)
{
	variable_list.clear();
}

void
add_variable(value *val)
{
map<string, value>::iterator	it;
	it = variable_list.find(val->cv_name);
	if (it != variable_list.end())
		return;
	variable_list.insert(make_pair(val->cv_name, *val));
}

void
add_variable_simple(string const &name, string const &vval)
{
value		var = file_position();
avalue_t	aval;
	var.cv_name = name;
	aval = q_string(vval, file_position());
	var.cv_values.push_back(aval);
	variable_list.insert(make_pair(var.cv_name, var));
}

value *
value_from_variable(string const &, string const &varname, file_position const &pos)
{
map<string, value>::iterator	it;
value	*val;
	it = variable_list.find(varname);
	if (it == variable_list.end())
		return NULL;
	val = new value(it->second);
	val->cv_pos = pos;
	return val;
}

void
add_if_entry(string const &name, int64_t v)
{
	if_parser.add_variable(name, v);
}

bool
if_true(string const &if_)
{
char const	*dir;
	dir = if_.c_str();
	dir += sizeof("%if");
	while (isspace(*dir))
		dir++;

	try {
		return if_parser.run(dir);
	} catch (expr::error &e) {
		report_parse_error("error in %%if expression: %s", e.what());
		return false;
	}
}

bool
if_defined(string const &if_)
{
char const	*dir;
	dir = if_.c_str();
	while (isspace(*dir))
		dir++;

	WDEBUG(format("PARSE: if[n]def %s") % dir);
	return if_parser.variable_defined(dir);
}

void
define_if(string const &str)
{
string			name, value;
string::size_type	i;
	if ((i = str.find(' ')) == string::npos) {
		report_parse_error("syntax error");
		return;
	}
	name = str.substr(0, i);
	value = str.substr(i + 1);

	WDEBUG(format("add var: [%s] = [%s]") % name % value);
	if_parser.add_variable(name, strtoll(value.c_str(), NULL, 0));
}

void
add_ipath(string const &path)
{
	ipaths.push_back(path);
}

void
tree_entry::add(value const &v)
{
value	*existing;
	if ((existing = (*this)/v.cv_name) != NULL) {
		existing->cv_values.insert(
			existing->cv_values.end(),
			v.cv_values.begin(), v.cv_values.end());
		return;
	}
	item_values.insert(make_pair(v.cv_name, v));
}

tree_entry *
tree::find(string const &block, string const &name)
{
vector<tree_entry>::iterator	it, end;
	for (it = entries.begin(), end = entries.end(); it != end; ++it) {
		if (it->item_name == block && (name.empty() || (name == it->item_key))) {
			it->item_touched = true;
			return &*it;
		}
	}
	return NULL;
}

tree_entry *
tree::find(string const &block)
{
	return find(block, "");
}

tree_entry *
tree::find_or_new(
	string const	&block,
	string const	&name,
	file_position const	&pos,
	bool		 unnamed,
	bool		 is_template
) {
tree_entry	*f, n(pos);
	if ((f = find(block, name)) != NULL)
		return f;

	n.item_unnamed = unnamed;
	n.item_name = block;
	n.item_key = name;
	n.item_pos = pos;
	n.item_is_template = is_template;
	entries.push_back(n);
	return &*entries.rbegin();
}

tree_entry *
tree::create(
	string const	&block,
	string const	&name,
	file_position const	&pos,
	bool		 unnamed,
	bool		 is_template
) {
tree_entry	n(pos);
	n.item_unnamed = unnamed;
	n.item_name = block;
	n.item_key = name;
	n.item_pos = pos;
	n.item_is_template = is_template;
	entries.push_back(n);
	return &*entries.rbegin();
}

tree_entry *
tree::find_item(tree_entry const &e)
{
	if (e.item_unnamed)
		return find(e.item_name);
	else	return find(e.item_name, e.item_key);
}

tree_entry *
new_tree_entry_from_template(
	tree			&t,
	string const		&block,
	string const		&name,
	string const		&templatename,
	file_position const		&pos,
	bool			 unnamed,
	bool
) {
tree_entry	*n, *e;
	if ((e = t.find(block, templatename)) == NULL)
		return e;
	n = t.find_or_new(block, name, pos, unnamed, true);
	n->item_values = e->item_values;
	return n;
}


int
find_untouched(tree &t)
{
int		 i = 0;
vector<tree_entry>::const_iterator	it, end;
multimap<string, value>::const_iterator	vit, vend;
	for (it = t.entries.begin(), end = t.entries.end(); it != end; ++it) {
		if (it->item_is_template)
			continue;
		if (!it->item_touched) {
			it->report_error("top-level block \"%s\" not recognised", it->item_name.c_str());
			i++;
			continue;
		}
		for (vit = it->item_values.begin(), vend = it->item_values.end(); vit != vend; ++vit) {
		string	name;
			if (vit->second.cv_touched)
				continue;
			if (!it->item_unnamed)
				name = "/" + it->item_name + "=" + it->item_key + "/" + vit->second.cv_name;
			else
				name = "/" + it->item_name + "/" + vit->second.cv_name;
			vit->second.report_error(format("%s was not recognised") % name);
			i++;
		}
	}
	return i;
}

value *
tree_entry::operator/(string const &name)
{
multimap<string, value>::iterator	it;
	it = item_values.find(name);
	if (it == item_values.end())
		return NULL;
	it->second.cv_touched = true;
	return &it->second;
}

/*
 * add a new top-level item to the tree.
 */
bool
tree::add(tree_entry const &item)
{
	/* if the entry already exists, do nothing */
//	if (find_item(item) != NULL)
//		return false;
	entries.push_back(item);
	return true;
}

void
handle_pragma(string const &pragma)
{
char	*mp, *op, *ap;
	mp = wstrdup(pragma.c_str());
	op = mp;
	if (*mp == '\n')
		++mp;
	while (isspace(*mp))
		mp++;
	/* skip '%pragma ' */
	mp += sizeof("%pragma");
	while (isspace(*mp))
		++mp;

	/* now up to the first space or EOS is the pragma name */
	if ((ap = strchr(mp, ' ')) != NULL)
		*ap++ = '\0';
	if (!strcmp(mp, "include_path")) {
		if (*ap != '"' || ap[strlen(ap) - 1] != '"')
			report_parse_error("%%pragma include_path must be followed by a quoted string");
		else {
			ap++;
			ap[strlen(ap) - 1] = '\0';
				add_ipath(ap);
		}	
	} else {
		report_parse_error("unrecognised %%pragma \"%s\"", mp);
	}

	wfree(op);
}

value::value(file_position const &pos)
	: cv_touched(false)
	, cv_pos(pos)
{
}

tree_entry::tree_entry(file_position const &pos)
	: item_pos(pos)
	, item_unnamed(false)
	, item_touched(false)
	, item_is_template(false)
{
}

/* public functions */

void
report_parse_error(const char *fmt, ...)
{
va_list	ap;
char	msg[1024] = { 0 };

	parse_error = true;

	va_start(ap, fmt);
	vsnprintf(msg, sizeof msg, fmt, ap);
	va_end(ap);

	wlog.error(format("%s(%d): %s")	% "" % 0 % msg);
}

void
value::report_error(string const &err) const
{
	cv_pos.error(
		boost::bind(
			static_cast<void (logger::*) (string const &)>(
				&logger::error), &wlog, _1), err);
}

size_t
value::nvalues(void) const
{
	return cv_values.size();
}

void
tree_entry::vreport_error(const char *fmt, va_list ap) const
{
char	msg[1024] = { 0 };
	vsnprintf(msg, sizeof msg, fmt, ap);
	wlog.error(format("%s: %s") % item_pos.format() % msg);
}

void
tree_entry::report_error(const char *fmt, ...) const
{
va_list	ap;
	va_start(ap, fmt);
	vreport_error(fmt, ap);
	va_end(ap);
}

void
catastrophic_error(const char *fmt, ...)
{
char	msg[1024] = { 0 };
va_list	ap;
	va_start(ap, fmt);
	vsnprintf(msg, sizeof msg, fmt, ap);
	va_end(ap);
	wlog.error(format("%s(%d): catastrophic error: %s")
		% "" % 0 % msg);
	parse_error = true;
}

extern "C" void
yyerror(const char *err)
{
	parse_error = true;
	wlog.error(format("%s(%d): %s")	% "" % 0 % err);
}

void
tree::reset(void)
{
	entries.clear();
}

bool
find_include(string &file)
{
vector<string>::iterator it = ipaths.begin(), end = ipaths.end();
string	ret;
	for (; it != end; ++it) {
	struct stat	sb;
		if (stat((*it + '/' + file).c_str(), &sb) == 0) {
			file = *it + '/' + file;
			return true;
		}
	}

	return false;
}

block_definer &
conf_definer::block(string const &name, int flags)
{
block_definer *b = new block_definer(*this, name, flags);
	blocks.push_back(b);
	return *b;
}

conf_definer::~conf_definer()
{
vector<block_definer *>::iterator	it, end;
	for (it = blocks.begin(), end = blocks.end(); it != end; ++it)
		delete *it;
}

bool
value_definer::validate(tree_entry &e, value &v)
{
	return (*vv)(e, v);
}

void
value_definer::set(tree_entry &e, value &v)
{
	(*vs)(e, v);
}

block_definer::block_definer(conf_definer &parent_, string const &name_, int flags_)
	: parent(parent_)
	, name(name_)
	, vefn(NULL)
	, sefn(NULL)
	, flags(flags_)
{
}

block_definer::~block_definer()
{
map<string, value_definer *>::iterator	vit, vend;
	for (vit = values.begin(), vend = values.end(); vit != vend; ++vit)
		delete vit->second;
	delete vefn;
	delete sefn;
}

block_definer &
block_definer::block(string const &name_, int flags_)
{
	return parent.block(name_, flags_);
}

bool
block_definer::validate(tree_entry &e)
{
multimap<string, conf::value>::iterator	it, end_;
map<string, value_definer *>::iterator	vit;
bool ret = true;
	for (it = e.item_values.begin(), end_ = e.item_values.end(); it != end_; ++it) {
		vit = values.find(it->first);
		if (vit == values.end())
			continue;
		it->second.cv_touched = true;
		ret = vit->second->validate(e, it->second) && ret;
	}
	if (vefn)
		ret = (*vefn)(e) && ret;
	if ((flags & require_name) && e.item_key.empty()) {
		e.report_error("this block cannot be unnamed");
		ret = false;
	}
	return ret;
}

void
block_definer::set(tree_entry &e)
{
multimap<string, conf::value>::iterator	it, end_;
map<string, value_definer *>::iterator	vit;
	for (it = e.item_values.begin(), end_ = e.item_values.end(); it != end_; ++it) {
		vit = values.find(it->first);
		if (vit == values.end())
			continue;
		vit->second->set(e, it->second);
	}
	if (sefn)
		(*sefn)(e);
}

bool
conf_definer::validate(tree &t) const
{
bool	ret = true;
vector<block_definer *>::const_iterator	bit, bend;
vector<tree_entry>::iterator	tit, tend;
	for (tit = t.entries.begin(), tend = t.entries.end(); tit != tend; ++tit) {
		for (bit = blocks.begin(), bend = blocks.end(); bit != bend; ++bit) {
			if ((*bit)->name == tit->item_name) {
				tit->item_touched = true;
				ret = (*bit)->validate(*tit) && ret;
			}
		}
	}
	if (find_untouched(t))
		return false;
	return ret;
}

void
conf_definer::set(tree &t) const
{
vector<block_definer *>::const_iterator	bit, bend;
vector<tree_entry>::iterator	tit, tend;
	for (tit = t.entries.begin(), tend = t.entries.end(); tit != tend; ++tit) {
		for (bit = blocks.begin(), bend = blocks.end(); bit != bend; ++bit) {
			if ((*bit)->name == tit->item_name) {
				(*bit)->set(*tit);
			}
		}
	}
}

bool
parse_ip(string const &ip, pair<string, string> &result)
{
string::size_type       i;
in_addr                 a4;
in6_addr                a6;
        if (ip.empty())
                return false;
        result.first = ip;
        if (ip[0] == '[') {
                if ((i = ip.find_first_of(']')) == string::npos)
                        return false;
                result.first = ip.substr(1, i - 1);
                if (ip.size() > i + 1 && ip[i + 1] == ':')
                        result.second = ip.substr(i + 2);
        } else if ((i = ip.find_first_of(':')) != string::npos) {
                result.first = ip.substr(0, i);
                result.second = ip.substr(i + 1);
        }
        if (result.first.find_first_of(':') != string::npos) {
                if (inet_pton(AF_INET6, result.first.c_str(), &a6) <= 0)
                        return false;
        }
        return true;
}

} // namespace conf
