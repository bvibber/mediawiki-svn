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

/* This file defines debuglex and debuglex2. */
#include "fb_defines.h"

/* Notice: We need to include parsetree.h first because wikiparse.tab.h will
 * attempt to use the 'Node' type defined therein. */
#include "parsetree.h"
#include "wikiparse.tab.h"

/* Tells flex to stop processing input when EOF is reached. */
int yywrap(void) { return 1; }

int encodeTableRowInfo (char* input, int initleng)
{
    int i = 1;
    while (input[i] == '-') i++;
    return (((i-1) << 16) | (initleng-i));
}

#define COMMONTOKENS \
    if (YY_START != inattributeapo && YY_START != inattributeq && YY_START != canbeheading) \
        BEGIN (cannotbelistorheadingorpre);

#define APOSTROPHE_CATASTROPHE(len,debugstr,token)  \
    if (yytext[len] == '\'')                        \
    {                                               \
        yyless (1);                                 \
        yylval.node = newNodeS (TextToken, "'");    \
        debuglex2 ("TEXT(%s) ", "'");               \
        return TEXT;                                \
    }                                               \
    else                                            \
    {                                               \
        yyless (len);                               \
        debuglex (debugstr);                        \
        return token;                               \
    }

%}

/* inclusive start conditions */
%s canbelist canbeheading cannotbelistorheadingorpre attributes
%s inattributeapo inattributeq startattribute

/* exclusive start conditions */
%x extension comment

%%

    char* extension_name = 0;
    int i;


"<"[[:alnum:]]+">"              {
                                    fb_create_new_buffer (256); /* for the contents */
                                    i = strlen (yytext)-1;
                                    extension_name = (char*) malloc (i * sizeof (char));
                                    memcpy (extension_name, yytext + 1, --i);
                                    extension_name[i] = '\0';
                                    BEGIN (extension);
                                }
<extension>"</"[[:alnum:]]+">"  {
                                    i = strlen (extension_name);
                                    if (strncmp (extension_name, yytext+2, i))
                                        fb_write_to_buffer (yytext);
                                    else
                                    {
                                        BEGIN (cannotbelistorheadingorpre);
                                        yylval.node = newNodeN (ExtensionToken,
                                            extension_name, fb_get_buffer(), 0, 0);
                                        debuglex ("EXTENSION ");
                                        return EXTENSION;
                                    }
                                }
<extension>.[^<>]*              {   fb_write_to_buffer (yytext);    }
<extension><<EOF>>              {
                                    BEGIN (cannotbelistorheadingorpre);
                                    yylval.node = newNodeN (ExtensionToken,
                                        extension_name, fb_get_buffer(), 0, 0);
                                    debuglex ("EXTENSION ");
                                    return EXTENSION;
                                }

"<!--"              { BEGIN (comment); debuglex ("BEGINCOMMENT "); return BEGINCOMMENT; }
<comment>.[^-]*     { debuglex ("TEXT "); yylval.node = newNodeS (TextToken, strdup (yytext)); return TEXT; }
<comment>"-->"      { BEGIN (cannotbelistorheadingorpre); debuglex ("ENDCOMMENT "); return ENDCOMMENT; }

    /* For the table-related tokens, we need to remember enough information so that we can
     * reliably turn things back into text. */
"{|"" "*    { BEGIN(attributes); debuglex ("TABLEBEGIN ");   yylval.num = yyleng-2;       return TABLEBEGIN;    }
"||"" "*    { BEGIN(attributes); debuglex ("TABLECELL ");    yylval.num = 2*(yyleng-2);   return TABLECELL;     }
^"|"" "*    { BEGIN(attributes); debuglex ("TABLECELL ");    yylval.num = 2*(yyleng-1)+1; return TABLECELL;     }
"!!"" "*    { BEGIN(attributes); debuglex ("TABLEHEAD ");    yylval.num = 2*(yyleng-2);   return TABLEHEAD;     }
^"!"" "*    { BEGIN(attributes); debuglex ("TABLEHEAD ");    yylval.num = 2*(yyleng-1)+1; return TABLEHEAD;     }
"|+"" "*    { BEGIN(attributes); debuglex ("TABLECAPTION "); yylval.num = yyleng-2;       return TABLECAPTION;  }
"|""-"+" "* { BEGIN(attributes); debuglex ("TABLEROW ");     yylval.num = encodeTableRowInfo (yytext, yyleng); return TABLEROW; }
"|}"        { BEGIN(cannotbelistorheadingorpre); debuglex ("TABLEEND "); return TABLEEND; }

<attributes>[-a-zA-Z:_]+" "* {
        debuglex2 ("ATTRIBUTE(%s) ", yytext);
        yylval.ad = newAttributeDataFromStr (yytext);
        return ATTRIBUTE;
    }
<attributes>"="" "*          {
        debuglex2 ("EQUALS(%d) ", yyleng-1);
        yylval.num = yyleng-1;
        BEGIN (startattribute);
        return EQUALS;
    }

<startattribute>\'      { BEGIN (inattributeapo); yylval.num = 0;        debuglex ("ATTRAPO(0) ");             return ATTRAPO; }
<startattribute>\"      { BEGIN (inattributeq);   yylval.num = 0;        debuglex ("ATTRQ(0) ");               return ATTRQ;   }
<inattributeapo>\'" "*  { BEGIN (attributes);     yylval.num = yyleng-1; debuglex2 ("ATTRAPO(%d) ", yyleng-1); return ATTRAPO; }
<inattributeq>\"" "*    { BEGIN (attributes);     yylval.num = yyleng-1; debuglex2 ("ATTRQ(%d) ",   yyleng-1); return ATTRQ;   }

"\[\[:"             { COMMONTOKENS; debuglex ("OPENLINK "); return OPENLINK; }
"\[\["              { COMMONTOKENS; debuglex ("OPENDBLSQBR "); return OPENDBLSQBR; }
"\]\]"              { COMMONTOKENS; debuglex ("CLOSEDBLSQBR "); return CLOSEDBLSQBR; }
\|                  { COMMONTOKENS; debuglex ("PIPE "); return PIPE; }
\{\{\{\{\{          { COMMONTOKENS; debuglex ("OPENPENTUPLECURLY "); return OPENPENTUPLECURLY; }
\}\}\}\}\}          { COMMONTOKENS; debuglex ("CLOSEPENTUPLECURLY "); return CLOSEPENTUPLECURLY; }
\{\{\{\{            { COMMONTOKENS; debuglex ("OPENTEMPLATE "); yyless (2); return OPENTEMPLATE; }
\}\}\}\}            { COMMONTOKENS; debuglex ("CLOSETEMPLATE "); yyless (2); return CLOSETEMPLATE; }
\{\{\{              { COMMONTOKENS; debuglex ("OPENTEMPLATEVAR "); return OPENTEMPLATEVAR; }
\}\}\}              { COMMONTOKENS; debuglex ("CLOSETEMPLATEVAR "); return CLOSETEMPLATEVAR; }
\{\{                { COMMONTOKENS; debuglex ("OPENTEMPLATE "); return OPENTEMPLATE; }
\}\}                { COMMONTOKENS; debuglex ("CLOSETEMPLATE "); return CLOSETEMPLATE; }
'''''               { COMMONTOKENS; debuglex ("APO5 "); return APO5; }
'''                 { COMMONTOKENS; debuglex ("APO3 "); return APO3; }
''                  { COMMONTOKENS; debuglex ("APO2 "); return APO2; }
'''''.              { COMMONTOKENS; APOSTROPHE_CATASTROPHE (5, "APO5 ", APO5); }
'''.                { COMMONTOKENS; APOSTROPHE_CATASTROPHE (3, "APO3 ", APO3); }
''.                 { COMMONTOKENS; APOSTROPHE_CATASTROPHE (2, "APO2 ", APO2); }
\n                  { BEGIN (INITIAL); debuglex ("NEWLINE\n"); return NEWLINE; }
^" "*\n             { BEGIN (INITIAL); debuglex ("NEWLINE\n"); return NEWLINE; }
\r                  { /* ignore this one */ debuglex ("<13> "); }

^" "                { BEGIN(cannotbelistorheadingorpre); debuglex ("PRELINE "); return PRELINE; }
^\*[ \t]*           { BEGIN(canbelist); debuglex ("LISTBULLET "); return LISTBULLET; }
<canbelist>\*[ \t]*                   { debuglex ("LISTBULLET "); return LISTBULLET; }
^\#[ \t]*           { BEGIN(canbelist); debuglex ("LISTNUMBERED "); return LISTNUMBERED; }
<canbelist>\#[ \t]*                   { debuglex ("LISTNUMBERED "); return LISTNUMBERED; }

^"="+                           {
                                    BEGIN (canbeheading);
                                    yylval.num = strlen (yytext);
                                    debuglex2 ("HEADING(%d) ", yylval.num);
                                    return HEADING;
                                }
<canbeheading>"="+" "*\r\n      |
<canbeheading>"="+" "*\n        {
                                    yylval.num = 0;
                                    while (yytext [ yylval.num ] == '=')
                                        yylval.num++;
                                    yylval.num |= (yyleng-yylval.num) << 16;
                                    debuglex2 ("ENDHEADING(%d) ", yylval.num);
                                    return ENDHEADING;
                                }

<cannotbelistorheadingorpre,canbeheading>[^\!\|\r\n][^\<\>\[\]\{\}\r\n\'\|\=\!]*    |
<cannotbelistorheadingorpre,canbeheading>\!                                         |

<inattributeapo>[^\'\|\r\n][^\<\>\[\]\{\}\r\n\'\|\=\!]*                             |

<inattributeq>[^\"\|\r\n][^\<\>\[\]\{\}\r\n\'\"\|\=\!]*                             {
    yylval.node = newNodeS (TextToken, strdup (yytext));
    debuglex2 ("TEXT(%s) ", yytext);
    return TEXT;                                                                    }

<canbelist>[^ \!\|\*\#\r\n][^\<\>\[\]\{\}\r\n\'\|\!]*                               |
<canbelist>\!                                                                       |

<attributes>[^-a-zA-Z:_\r\n\|\=][^\<\>\[\]\{\}\r\n\'\|\!]*                          |

<INITIAL>[^ \!\|\*\#\r\n\=][^\<\>\[\]\{\}\r\n\'\|\=\!]*                             |
<INITIAL>\!                                                                         {
    BEGIN(cannotbelistorheadingorpre);
    yylval.node = newNodeS (TextToken, strdup (yytext));
    debuglex2 ("TEXT(%s) ", yytext);
    return TEXT;                                                                    }

<startattribute>[^ \t\r\n\'\"\|][^ \t\r\n\|]*" "*                                   {
    BEGIN (attributes);
    yylval.node = newNodeS (TextToken, strdup (yytext));
    debuglex2 ("TEXT(%s) ", yytext);
    return TEXT;                                                                    }
%%
