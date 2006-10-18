/*
 * willow config lexer
 * This source code is in the public domain.
 * River Tarnell <river@attenuate.org>
 */
/* $Id: idl.l 2619 2006-10-13 15:46:38Z river $ */

%option noyywrap
%option nounput

%{
#include <iostream>
#include <string>
#include <cerrno>
#include <cstdio>

using std::strerror;
using std::string;
using std::fopen;

#include "confparse.h"
#include "y.tab.h"

struct func_attrs;

int yylex(void);

#define MAXINCLUDE 10
struct statestack_stru {
	YY_BUFFER_STATE		state;
	string			file;
	int			lineno;
} statestack[MAXINCLUDE + 1];
int inclevel;
static int oldpos;

#undef YY_INPUT

#define YY_FATAL_ERROR(msg) conf::catastrophic_error(msg)

%}

ws        [ ]*
digit     [0-9]
number    {digit}{digit}*
qstring   \"[^\"\n]*[\"\n]
string    [a-zA-Z_\~][a-zA-Z0-9_-]*
include   %{ws}include{ws}(\<.*\>|\".*\")
pragma    %{ws}pragma[ \t]{ws}.*$
if        %{ws}if[ \t]{ws}[a-zA-Z_][a-zA-Z0-9_]*$
else      %{ws}else{ws}$
endif     %{ws}endif{ws}$


%x COMMENT IGNORING
%%

^.*		{
			conf::linebuf.assign(yytext, yyleng);
			oldpos = conf::curpos = 0; 
			yyless(0); 
		}
<IGNORING>{if}  { conf::report_parse_error("%%if may not be nested"); }
<IGNORING>{endif} { BEGIN 0; }
<IGNORING>{else} { BEGIN 0; }
<IGNORING>.     ;
<IGNORING>\n    { conf::lineno++; }

"/*"		{ BEGIN COMMENT; }
<COMMENT>\*\/	{ BEGIN 0; }
<COMMENT>.	;
<COMMENT>\n	{ conf::lineno++; conf::curpos = 0; }
\/\/.*$		;
\n		conf::lineno++;
{include}	{
		FILE	*f;
		char	*s, *t, c;
		string	 file, path;
			s = yytext + yyleng - 1;
			c = *s;
			t = s - 1;
			while (*t != c)
				t--;
			file.assign(t + 1, s);
			if (conf::find_include(file) == false) {
				conf::catastrophic_error("cannot locate include file");
			} else if ((f = fopen(file.c_str(), "r")) == NULL) {
			string	error = "cannot open include: ";
				error += file; error += " ";
				error += strerror(errno);
				conf::catastrophic_error(error.c_str());
			} else {
				if (inclevel + 1 == MAXINCLUDE) {
					conf::catastrophic_error("maximum include depth reached");
				} else {
					statestack[inclevel].state = YY_CURRENT_BUFFER;
					statestack[inclevel].lineno = conf::lineno;
					statestack[inclevel].file = conf::current_file;
					inclevel++;
					statestack[inclevel].state = yy_create_buffer(f, YY_BUF_SIZE);
					conf::current_file = file;
					conf::lineno = 1;
					yy_switch_to_buffer(statestack[inclevel].state);
				}
			}
		}
<<EOF>>		{
			if (inclevel) {
				yy_delete_buffer(statestack[inclevel].state);
				yy_switch_to_buffer(statestack[inclevel - 1].state);
				inclevel--;
				conf::current_file = statestack[inclevel].file;
				conf::lineno = statestack[inclevel].lineno;
			} else
				yyterminate();
		}
				
\t		{ oldpos = conf::curpos; conf::curpos += 8; }
{ws}            { oldpos = conf::curpos; conf::curpos += yyleng; }
var		{ oldpos = conf::curpos; conf::curpos += yyleng; return VAR; }
template	{ oldpos = conf::curpos; conf::curpos += yyleng; return TEMPLATE; }
from		{ oldpos = conf::curpos; conf::curpos += yyleng; return FROM; }
\.\.		{ oldpos = conf::curpos; conf::curpos += yyleng; return TWODOTS; }

{qstring}	{
			yylval.string_ = new std::string(yytext + 1, yyleng - 2);
			oldpos = conf::curpos; conf::curpos += yyleng;
			return QSTRING;
		}
{string}	{
			yylval.string_ = new std::string(yytext, yyleng);
			oldpos = conf::curpos; conf::curpos += yyleng;
			return STRING;
		}
{number}	{
			yylval.number = atoi(yytext);
			oldpos = conf::curpos; conf::curpos += yyleng;
			return NUMBER;
		}
.               { oldpos = conf::curpos; conf::curpos++; return yytext[0]; }
%%
