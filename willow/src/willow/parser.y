/* @(#) $Header$ */
/* From: $Nightmare: nightmare/src/main/parser.y,v 1.2.2.1.2.1 2002/07/02 03:42:10 ejb Exp $ */
/* From: ircd_parser.y,v 1.281 2005/03/22 16:27:48 androsyn Exp */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * parser: configuration file parser.
 */

%{
#if defined __SUNPRO_CC || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include <sys/types.h>
#include <sys/stat.h>

#include <netinet/in.h>

#include <string.h>
#include <stdlib.h>
#include <stdarg.h>
#include <stdio.h>

#include "willow.h"
#include "confparse.h"

#define YY_NO_UNPUT

int yyparse();
int yyerror(const char *);
int yylex();

static time_t conf_find_time(const char*);
static void add_cur_list(int, const char *, int);

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

static time_t 
conf_find_time(name)
	const char *name;
{
	int	i;

	for (i = 0; conf_times[i].name; ++i) {
		if (!strcasecmp(conf_times[i].name, name) ||
		    (conf_times[i].plural && !strcasecmp(conf_times[i].plural, name)))
			return conf_times[i].val;
	}

	return 0;
}

static struct {
const	char	*word;
	int	 yesno;
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
conf_get_yesno_value(str)
	const char *str;
{
	int i;

	for (i = 0; yesno[i].word; i++)
		if (!strcasecmp(str, yesno[i].word))
			return yesno[i].yesno;

	return -1;
}

static void
free_cur_list(list)
	conf_parm_t *list;
{
	switch (list->type & CF_MTYPE) {
	case CF_STRING:
	case CF_QSTRING:
		wfree(list->v.string);
		break;
	case CF_LIST:
		free_cur_list(list->v.list);
		break;
	default: break;
	}

	if (list->next)
		free_cur_list(list->next);
}

		
conf_parm_t *	cur_list = NULL;

static void
add_cur_list_cpt(new)
	conf_parm_t *new;
{
	if (cur_list == NULL) {
		cur_list = wcalloc(1, sizeof(conf_parm_t));
		cur_list->type |= CF_FLIST;
		cur_list->v.list = new;
	} else {
		new->next = cur_list->v.list;
		cur_list->v.list = new;
	}
}

static void
add_cur_list(type, str, number)
	int type, number;
	const char *str;
{
	conf_parm_t *new;

	new = wmalloc(sizeof(conf_parm_t));
	new->next = NULL;
	new->type = type;

	switch(type) {
	case CF_INT:
	case CF_TIME:
	case CF_YESNO:
		new->v.number = number;
		break;
	case CF_STRING:
	case CF_QSTRING:
		new->v.string = wstrdup(str);
		break;
	}

	add_cur_list_cpt(new);
}


%}

%union {
	int 		 number;
	char 		*string;
	conf_parm_t	*conf_parm;
}

%token TWODOTS

%token <string> QSTRING STRING
%token <number> NUMBER

%type <string> qstring string
%type <number> number timespec 
%type <conf_parm> oneitem single itemlist

%start conf

%%

conf: 
	| conf conf_item 
	| error
	;

conf_item: block
         ;

block: string 
         { 
           conf_start_block($1, NULL);
	   wfree($1);
         }
       '{' block_items '}' ';' 
         {
	   if (conf_cur_block)
           	conf_end_block(conf_cur_block);
         }
     | string qstring 
         { 
           conf_start_block($1, $2);
	   wfree($1);
	   wfree($2);
         }
       '{' block_items '}' ';'
         {
	   if (conf_cur_block)
           	conf_end_block(conf_cur_block);
         }
     ;

block_items: block_items block_item 
           | block_item 
           ;

block_item:	string '=' itemlist ';'
		{
			conf_call_set(conf_cur_block, $1, cur_list, CF_LIST);
			free_cur_list(cur_list);
			cur_list = NULL;
			wfree($1);
		}
		;

itemlist: itemlist ',' single
	| single
	;

single: oneitem
	{
		add_cur_list_cpt($1);
	}
	| oneitem TWODOTS oneitem
	{
		/* "1 .. 5" meaning 1,2,3,4,5 - only valid for integers */
		if (($1->type & CF_MTYPE) != CF_INT ||
		    ($3->type & CF_MTYPE) != CF_INT) {
			conf_report_error("Both arguments in '..' notation must be integers.");
			break;
		} else {
			int i;

			for (i = $1->v.number; i <= $3->v.number; i++) {
				add_cur_list(CF_INT, 0, i);
			}
		}
	}
	;

oneitem: qstring
            {
		$$ = wmalloc(sizeof(conf_parm_t));
		$$->type = CF_QSTRING;
		$$->v.string = wstrdup($1);
		wfree($1);
	    }
          | timespec
            {
		$$ = wmalloc(sizeof(conf_parm_t));
		$$->type = CF_TIME;
		$$->v.number = $1;
	    }
          | number
            {
		$$ = wmalloc(sizeof(conf_parm_t));
		$$->type = CF_INT;
		$$->v.number = $1;
	    }
          | string
            {
		/* a 'string' could also be a yes/no value .. 
		   so pass it as that, if so */
		int val = conf_get_yesno_value($1);

		$$ = wmalloc(sizeof(conf_parm_t));

		if (val != -1) {
			$$->type = CF_YESNO;
			$$->v.number = val;
		} else {
			$$->type = CF_STRING;
			$$->v.string = wstrdup($1);
		}
		wfree($1);
            }
          ;

qstring: QSTRING { strcpy($$, $1); } ;
string: STRING { strcpy($$, $1); } ;
number: NUMBER { $$ = $1; } ;

timespec:	number string
         	{
			time_t t;

			if ((t = conf_find_time($2)) == 0) {
				conf_report_error("Unrecognised time type/size '%s'", $2);
				t = 1;
			}
	    
			$$ = $1 * t;
		}
		| timespec timespec
		{
			$$ = $1 + $2;
		}
		| timespec number
		{
			$$ = $1 + $2;
		}
		;
