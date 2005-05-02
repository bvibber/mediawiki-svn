%{
/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * lexer: configuration file lexer.
 */
 
#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include <string.h>
#include <errno.h>

#include "willow.h"
#include "confparse.h"
#include "y.tab.h"

int lineno = 1;

int
yywrap(void)
{
	return 1;
}
%}

%x COMMENT

ws	[ \t]*
number	[0-9][0-9]*
comment	#.*
qstring	\"[^\"\n]*[\"]
string	[a-zA-Z_\~][a-zA-Z0-9_-]*

%%

^[ \t]*"/*"	{	BEGIN COMMENT;	}
^[ \t]*"/*".*"*/"[ \t]*\n { lineno++; }

<COMMENT>"*/"[ \t]*\n	{	BEGIN 0; lineno++; break; }
<COMMENT>"*/"		{	BEGIN 0; break; }
<COMMENT>\n		{	lineno++; break; }
<COMMENT>.\n		{	lineno++; break; }
<COMMENT>.		{ break; }
\n		{	lineno++;	}
{ws}		;
{comment}	;
{number}	{	char  *s;
			yylval.number = strtol(yytext, &s, 0);
			if (*s) {
				conf_report_error("invalid number '%s': %s", yytext, strerror(errno));
				break;
			}
			return NUMBER;
		}
{string}	{	yylval.string = wstrdup(yytext);
			return STRING;
		}
{qstring}	{	yylval.string = wstrdup(yytext + 1);
			yylval.string[yyleng - 2] = '\0';
			return QSTRING;
		}
.		{	return yytext[0];
		}
