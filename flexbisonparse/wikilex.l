%{

/**
 **
 **  This file is part of the flex/bison-based parser for MediaWiki.
 **            This is the lexer - the input file for flex.
 **  See fb_defines.h on how to make it output debugging information.
 **
 ** This source file is licensed unter the GNU General Public License
 **               http://www.gnu.org/copyleft/gpl.html
 **                 Originally written 2004 by Timwi
 **/

#include <stdio.h>
#include <string.h>
#include "fb_defines.h"

/* Notice: We need to include parsetree.h first because wikiparse.tab.h will
 * attempt to use the 'Node' type defined therein. */
#include "parsetree.h"
#include "wikiparse.tab.h"

int yywrap(void) { return 1; }

%}

%s canbelist canbeheading cannotbelistorheadingorpre attributes
%x extension comment

%%

    char extension_buf [ 65536 ];
    char * extension_buf_ptr;
    char * extension_name = 0;
    ExtensionData ed;
    Node node;
    int i;

"<"[[:alnum:]]+">"              {
                                    extension_buf_ptr = extension_buf;
                                    i = strlen (yytext) - 2;
                                    extension_name = strncpy ((char*) malloc
                                        (i * sizeof (char)), yytext + 1, i);
                                    BEGIN (extension);
                                }
<extension>"</"[[:alnum:]]+">"  {
                                    i = strlen (extension_name);
                                    if (strncmp (extension_name, yytext+2, i))
                                    {
                                        strcat (extension_buf, yytext);
                                        extension_buf_ptr += strlen (yytext);
                                    }
                                    else
                                    {
                                        *extension_buf_ptr = '\0';
                                        BEGIN (INITIAL);
                                        yylval.node = newNodeE (ExtensionToken,
                                            newExtensionData (extension_name, extension_buf));
                                        debuglex ("EXTENSION ");
                                        return EXTENSION;
                                    }
                                }
<extension>.                    {   *extension_buf_ptr++ = yytext[0];   }
<extension><<EOF>>              {
                                    *extension_buf_ptr = '\0';
                                    yylval.node = newNodeE (ExtensionToken,
                                        newExtensionData (extension_name, extension_buf));
                                    debuglex ("EXTENSION ");
                                    return EXTENSION;
                                }

"<!---->"           { BEGIN (cannotbelistorheadingorpre); debuglex ("EMPTYCOMMENT "); return EMPTYCOMMENT; }
"<!--"              { BEGIN (comment); debuglex ("BEGINCOMMENT "); return BEGINCOMMENT; }
<comment>.[^-]*     { debuglex ("TEXT "); yylval.node = newNodeS (TextToken, strdup (yytext)); return TEXT; }
<comment>"-->"      { BEGIN (cannotbelistorheadingorpre); debuglex ("ENDCOMMENT "); return ENDCOMMENT; }

"\[\[:"             { BEGIN (cannotbelistorheadingorpre); debuglex ("OPENLINK "); return OPENLINK; }
"\[\["              { BEGIN (cannotbelistorheadingorpre); debuglex ("OPENDBLSQBR "); return OPENDBLSQBR; }
"\]\]"              { BEGIN (cannotbelistorheadingorpre); debuglex ("CLOSEDBLSQBR "); return CLOSEDBLSQBR; }
\|                  { BEGIN (cannotbelistorheadingorpre); debuglex ("PIPE "); return PIPE; }
\{\{\{\{\{          { BEGIN (cannotbelistorheadingorpre); debuglex ("OPENPENTUPLECURLY "); return OPENPENTUPLECURLY; }
\}\}\}\}\}          { BEGIN (cannotbelistorheadingorpre); debuglex ("CLOSEPENTUPLECURLY "); return CLOSEPENTUPLECURLY; }
\{\{\{              { BEGIN (cannotbelistorheadingorpre); debuglex ("OPENTEMPLATEVAR "); return OPENTEMPLATEVAR; }
\}\}\}              { BEGIN (cannotbelistorheadingorpre); debuglex ("CLOSETEMPLATEVAR "); return CLOSETEMPLATEVAR; }
\{\{                { BEGIN (cannotbelistorheadingorpre); debuglex ("OPENTEMPLATE "); return OPENTEMPLATE; }
\}\}                { BEGIN (cannotbelistorheadingorpre); debuglex ("CLOSETEMPLATE "); return CLOSETEMPLATE; }
'''''/[^']          { BEGIN (cannotbelistorheadingorpre); debuglex ("APO5 "); return APO5; }
'''/[^']            { BEGIN (cannotbelistorheadingorpre); debuglex ("APO3 "); return APO3; }
''/[^']             { BEGIN (cannotbelistorheadingorpre); debuglex ("APO2 "); return APO2; }
\n                  { BEGIN (INITIAL); debuglex ("NEWLINE\n"); return NEWLINE; }

^" "                { BEGIN(cannotbelistorheadingorpre); debuglex ("PRELINE "); return PRELINE; }
^\*[[:space:]]*     { BEGIN(canbelist); debuglex ("LISTBULLET "); return LISTBULLET; }
<canbelist>\*[[:space:]]*             { debuglex ("LISTBULLET "); return LISTBULLET; }
^\#[[:space:]]*     { BEGIN(canbelist); debuglex ("LISTNUMBERED "); return LISTNUMBERED; }
<canbelist>\#[[:space:]]*             { debuglex ("LISTNUMBERED "); return LISTNUMBERED; }

^"="+                           {
                                    BEGIN (canbeheading);
                                    yylval.num = strlen (yytext);
                                    debuglex2 ("HEADING(%d) ", yylval.num);
                                    return HEADING;
                                }
<canbeheading>"="+" "*          {
                                    yylval.num = 0;
                                    while (yytext [ yylval.num ] == '=')
                                        yylval.num++;
                                    debuglex2 ("ENDHEADING(%d) ", yylval.num);
                                    return ENDHEADING;
                                }

"{|"" "*                      { BEGIN(attributes); debuglex ("TABLEBEGIN "); return TABLEBEGIN; }
"||"" "*/[^\| ][^\|]*"|"[^\|] { BEGIN(attributes); debuglex ("TABLECELL ");  return TABLECELL;  }
"||"" "*      { BEGIN(cannotbelistorheadingorpre); debuglex ("TABLECELL ");  return TABLECELL;  }
"!!"" "*/[^\! ][^\!]*"!"[^\!] { BEGIN(attributes); debuglex ("TABLEHEAD ");  return TABLEHEAD;  }
"!!"" "*      { BEGIN(cannotbelistorheadingorpre); debuglex ("TABLEHEAD ");  return TABLEHEAD;  }

"|""-"+" "*                   { BEGIN(attributes); debuglex ("TABLEROW ");   return TABLEROW;   }
"|}"" "*      { BEGIN(cannotbelistorheadingorpre); debuglex ("TABLEEND ");   return TABLEEND;   }


<cannotbelistorheadingorpre,canbeheading>[^\|\n][^\<\>\[\]\{\}\n\'\|\=]*     {
    yylval.node = newNodeS (TextToken, strdup (yytext));
    debuglex2 ("TEXT(%s) ", yytext); return TEXT; }
<canbelist>[^ \|\*\#\n][^\<\>\[\]\{\}\n\'\|]*                 {
    BEGIN(cannotbelistorheadingorpre); yylval.node = newNodeS (TextToken, strdup (yytext));
    debuglex2 ("TEXT(%s) ", yytext); return TEXT; }
<INITIAL>[^ \|\*\#\n\=][^\<\>\[\]\{\}\n\'\|\=]*               {
    BEGIN(cannotbelistorheadingorpre); yylval.node = newNodeS (TextToken, strdup (yytext));
    debuglex2 ("TEXT(%s) ", yytext); return TEXT; }

%%
