/* This code is in the public domain.
 * $Nightmare: nightmare/src/main/parser.y,v 1.2.2.1.2.1 2002/07/02 03:42:10 ejb Exp $
 * $Id$
 */

%{
#include <sys/types.h>
#include <sys/stat.h>

#include <netinet/in.h>
#include <arpa/inet.h>
#include <netdb.h>

#include <vector>
using std::vector;

#include <cstdlib>
#include <cstdarg>
#include <cstdio>

#define NEED_PARSING_TREE
#include "willow.h"
#include "confparse.h"

/*
 * Certain bison/gcc combinations result in compilation errors in gcc-specific
 * code.  Work around this by undefining here.
 */
#undef __GNUC_MINOR__

#define YY_NO_UNPUT

/* icc emits these with -w2 */
#ifdef __INTEL_COMPILER
# pragma warning (disable : 193)
#endif

static time_t conf_find_time(string const &);

static struct {
	const char *	name;
	const char *	plural;
	time_t 	val;
} conf_times[] = {
	{"second",     "seconds",    1},
	{"minute",     "minutes",    60},
	{"hour",       "hours",      60 * 60},
	{"day",        "days",       60 * 60 * 24},
	{"week",       "weeks",      60 * 60 * 24 * 7},
	{"fortnight",  "fortnights", 60 * 60 * 24 * 14},
	{"month",      "months",     60 * 60 * 24 * 7 * 4},
	{"year",       "years",      60 * 60 * 24 * 365},
	/* ok-- we now do sizes here too. they aren't times, but 
	   it's close enough */
	{"byte",	"bytes",	1},
	{"kb",		NULL,		1024},
	{"kbyte",	"kbytes",	1024},
	{"kilobyte",	"kilebytes",	1024},
	{"mb",		NULL,		1024 * 1024},
	{"mbyte",	"mbytes",	1024 * 1024},
	{"megabyte",	"megabytes",	1024 * 1024},
	{NULL, NULL, 0},
};

time_t conf_find_time(string const &name)
{
  int i;

  for (i = 0; conf_times[i].name; i++)
    {
      if (!strcasecmp(conf_times[i].name, name.c_str()) ||
	  (conf_times[i].plural && !strcasecmp(conf_times[i].plural, name.c_str())))
	return conf_times[i].val;
    }

  return 0;
}

/*ARGSUSED*/
static conf::value *
f_hostname(vector<conf::avalue> *)
{
#ifndef HOST_NAME_MAX
# define HOST_NAME_MAX 255	/* SUSv2 */
#endif
char		host[HOST_NAME_MAX] = { 0 };
conf::value	*ret;
conf::avalue	 aval;
	gethostname(host, sizeof(host));
	ret = new conf::value(conf::declpos::here());
	aval.av_type = conf::cv_qstring;
	aval.av_strval = host;
	ret->cv_values.push_back(aval);
	return ret;
}

static conf::value *
f_dns(vector<conf::avalue> *args)
{
struct addrinfo	*res, hints;
string		 aftype;
conf::value	*ret;
int		 i;
conf::avalue	 aval;
char		 tmp[64];
	ret = new conf::value(conf::declpos::here());
	aftype = (*args)[0].av_strval.c_str();
	memset(&hints, 0, sizeof(hints));
	if (aftype == "ipv4")
		hints.ai_family = AF_INET;
	else if (aftype == "ipv6")
		hints.ai_family = AF_INET6;
	else {
		return ret;
	}
	if ((i = getaddrinfo((*args)[0].av_strval.c_str(), "80", &hints, &res)) != 0) {
		conf::report_parse_error("getaddrinfo(%s): %s", 
			(*args)[0].av_strval.c_str(), gai_strerror(i));
		return ret;
	}
	
	/* format the address as an IP */
	aval.av_type = conf::cv_qstring;
	inet_ntop(res->ai_family, res->ai_addr->sa_data, tmp, sizeof(tmp));
	aval.av_strval = tmp;
	ret->cv_values.push_back(aval);
	freeaddrinfo(res);
	return ret;
}

typedef struct function_stru {
	const char	*name;
	conf::value	*(*execute)(vector<conf::avalue> *args);
	int		 args[3];	/* XXX */
} function_t;

static function_t functions[] = {
	{ "hostname",	f_hostname,	{ 0, 0, 0 }			},
	{ "dns",	f_dns,		{ conf::cv_qstring, conf::cv_string, 0 }	},
	{ NULL, NULL, { } }
};

static int
match_func_parms(function_t *f, vector<conf::avalue> *args)
{
size_t		i;
vector<conf::avalue>::const_iterator	it, end;
	it = args->begin();
	end = args->end();
	for (i = 0; f->args[i]; ++i) {
		if (i+1 > args->size()) {
			conf::report_parse_error("not enough arguments to function '%s' (got %d)", 
				f->name, i+1);
			return 0;
		}
		if (f->args[i] != (it)->av_type) {
			conf::report_parse_error("wrong type %d for argument %d to '%s' (expected %d)", 
				(it)->av_type, i + 1, f->name, f->args[i]);
			return 0;
		}
		it++;
	}
	if (args->size() > i) {
		conf::report_parse_error("too many arguments to function '%s'", f->name);
		return 0;
	}
	return 1;
}

static function_t*
find_function(string const &name, vector<conf::avalue> *args)
{
function_t	*f;
	for (f = functions; f->name; ++f)
		if (f->name == name)
		{
			if (match_func_parms(f, args))
				return f;
			else
				break;
		}
	return NULL;
}

static struct
{
	const char *word;
	int yesno;
} yesno[] = {
	{"yes",		1},
	{"no",		0},
	{"true",	1},
	{"false",	0},
	{"on",		1},
	{"off",		0},
	{NULL,		0}
};

static int
conf_get_yesno_value(string const &str)
{
int	i;
	for (i = 0; yesno[i].word; i++)
		if (str == yesno[i].word)
			return yesno[i].yesno;

	return -1;
}

%}

%union {
	long			 number;
	string			*string_;
	conf::avalue		*avalue;
	conf::value		*value;
	vector<conf::value>	*value_list;
	vector<conf::avalue>	*avalue_list;
	bool			 bool_;
}

%token TWODOTS VAR TEMPLATE FROM

%token <string_> QSTRING STRING VARNAME
%token <number> NUMBER

%type <string_>       qstring string varname astring from_clause key_clause
%type <number>       number timespec 
%type <avalue>       oneitem
%type <avalue_list>  single
%type <avalue_list>  itemlist
%type <value_list>   block_items optional_block
%type <value>        block_item
%type <bool_>        template_clause
%type <avalue_list>	func_args
%type <value>        function

%left '+'
%nonassoc poneitem
%nonassoc ptimespec
%start conf

%%

conf: | items

items: conf_item
	| items conf_item 
	;

conf_item: block
         ;

from_clause:
	  { $$ = NULL; }
	| FROM astring {
		$$ = $2;
	}
	;
key_clause:
	  { $$ = NULL; }
	| astring {
		$$ = $1;
	}
	;

template_clause:
	  { $$ = 0; }
	| TEMPLATE { $$ = 1; }
	;

semicolon:
	';'
	| { conf::report_parse_error("expected ';'"); }
	;

equals:
	'='
	| { conf::report_parse_error("expected '='"); }
	;

func_args:
	{ $$ = new vector<conf::avalue>; }
	| itemlist {
		$$ = $1;
		delete $1;
	}
	;

function:
	string '(' func_args ')'
	{
	function_t	*func;
		if ((func = find_function(*$1, $3)) == NULL) {
			conf::report_parse_error("undefined function %s", $1);
			$$ = new conf::value(conf::declpos::here());
		} else {
			$$ = func->execute($3);
		}
		delete $1;
	}
	;

optional_block:
	{
		$$ = NULL;
	}
	| '{' block_items '}'
	{
		$$ = $2;
	}
	;

block:	template_clause string key_clause from_clause optional_block semicolon
	{ 
	const char		*block_key;
	char			 nname[10];
	static int		 nseq;
	bool			 unnamed = false;
	conf::tree_entry	*e;
	vector<conf::value>::const_iterator	it, end;
		if ($3)
			block_key = $3->c_str();
		else {
			sprintf(nname, "__%d", nseq++);
			block_key = nname;
			unnamed = true;
		}
		if ($4) {
			if ((e = conf::new_tree_entry_from_template(conf::parsing_tree, *$2, block_key, *$4, 
			                                       conf::declpos::here(), unnamed, $1)) == NULL) {
				conf::report_parse_error("template block \"%s\" not found", $4);
				goto end;
			}
		} else {
			if ((e = conf::parsing_tree.find(*$2, block_key)) != NULL) {
				conf::report_parse_error("%s \"%s\" already defined at %s",
					$2->c_str(), block_key, e->item_pos.format().c_str());
				goto end;
			}
			e = conf::parsing_tree.find_or_new(*$2, block_key, conf::declpos::here(), unnamed, $1);
		}
		
		if ($5) for (it = $5->begin(), end = $5->end(); it != end; ++it) {
			e->add(*it);
		}
	end:
		delete $2;
		delete $3;
		delete $4;
		delete $5;
	}
	| VAR varname equals itemlist semicolon
	{
	conf::value	*value;
		value = new conf::value(conf::declpos::here());
		value->cv_name = $2->substr(1);
		value->cv_values = *$4;
		conf::add_variable(value);
		delete $4;
	}
	| error
	;

block_items:
	  block_items block_item 
	{
		$$ = new vector<conf::value>($1->begin(), $1->end());
		$$->push_back(*$2);
		delete $1;
		delete $2;
	}
	| block_item
	{
		$$ = new vector<conf::value>;
		$$->push_back(*$1);
		delete $1;
	}
	;

block_item:	
	string equals itemlist semicolon
	{
		$$ = new conf::value(conf::declpos::here());
		$$->cv_name = *$1;
		$$->cv_values = *$3;
		delete $1;
		delete $3;
	}
	;

/* 
 * "single" is a list of items. 
 */
itemlist: itemlist ',' single
	{
		$$ = $1;
		$$->insert($$->end(), $3->begin(), $3->end());
		delete $3;
	}
	| single
	{
		$$ = new vector<conf::avalue>;
		$$->insert($$->end(), $1->begin(), $1->end());
		delete $1;
	}
	;

single: oneitem
	{
		$$ = new vector<conf::avalue>;
		$$->push_back(*$1);
		delete $1;
	}
	| oneitem TWODOTS oneitem
	{
		$$ = new vector<conf::avalue>;
		/* "1 .. 5" meaning 1,2,3,4,5 - only valid for integers */
		if ($1->av_type != conf::cv_int || $3->av_type != conf::cv_int) {
			conf::report_parse_error("both arguments in '..' notation must be integers.");
			break;
		} else {
			int		 i;
			conf::avalue	 val;
			for (i = $1->av_intval; i <= $3->av_intval; i++) {
				val.av_type = conf::cv_int;
				val.av_intval = i;
				$$->push_back(val);
			}
		}
		delete $1;
		delete $3;
	}
	| varname
	 {
	string		 varname_;
	conf::value	*value;
		$$ = new vector<conf::avalue>;
		varname_ = $1->substr(1);
		value = conf::value_from_variable("", varname_, conf::declpos::here());
		if (value == NULL) {
			conf::report_parse_error("undefined variable %s", varname_.c_str());
		} else {
			$$->insert($$->begin(), value->cv_values.begin(), value->cv_values.end());
		}
		delete $1;
	}
	| single '+' single
	{
	int	flen, slen;
		flen = $1->size();
		slen = $3->size();
		if (flen == 1 && slen == 1) {
		conf::avalue	n;
			n.av_type = conf::cv_qstring;
			n.av_strval = (*$1)[0].av_strval + (*$3)[0].av_strval;
			$$->push_back(n);
		} else {
			conf::report_parse_error("do not know how to add these values (%d+%d)", flen, slen);
		}
		delete $1;
		delete $3;
	}
	;

oneitem: astring
            {
		$$ = new conf::avalue;
		$$->av_type = conf::cv_qstring;
		$$->av_strval = *$1;
		delete $1;
	    }
          | timespec 
            {
		$$ = new conf::avalue;
		$$->av_type = conf::cv_time;
		$$->av_intval = $1;
	    }
          | number
            {
		$$ = new conf::avalue;
		$$->av_type = conf::cv_int;
		$$->av_intval = $1;
	    }
          | string
            {
		/* a 'string' could also be a yes/no value .. 
		   so pass it as that, if so */
		int val = conf_get_yesno_value(*$1);

		$$ = new conf::avalue;

		if (val != -1) {
			$$->av_type = conf::cv_yesno;
			$$->av_intval = val;
		} else {
			$$->av_type = conf::cv_string;
			$$->av_strval = *$1;
		}
		delete $1;
            }
          ;

astring:
	  qstring { 
		$$ = $1;
	}
	| function {
	conf::value	*value;
		value = $1;
		if (!value->is_single(conf::cv_qstring)) {
			value->report_error("function in concatenation must return quoted string");
			$$ = new string("");
		} else {
			$$ = new string($1->cv_values[0].av_strval);
		}
		delete $1;
	}
	;

qstring:
	  QSTRING {
		$$ = $1;
	}
	| QSTRING QSTRING {
		$$ = new string (*$1 + *$2);
		delete $1;
		delete $2;
	}
	;
string: STRING { $$ = $1; } ;
number: NUMBER { $$ = $1; } ;
varname: VARNAME { $$ = $1; } ;

timespec:	number string 
         	{
		time_t	t;
			if ((t = conf_find_time(*$2)) == 0) {
				conf::report_parse_error("unrecognised time type/size \"%s\"", $2->c_str());
				t = 1;
			}
	    		delete $2;
			$$ = $1 * t;
		}
		| timespec timespec
		{
			$$ = $1 + $2;
		}
		;
