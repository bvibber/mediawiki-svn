/**
 **  This file is part of the flex/bison-based parser for MediaWiki.
 **    You can change these defines to "printf" to have the lexer
 **         and/or the parser output debugging information.
 **
 ** This source file is licensed unter the GNU General Public License
 **               http://www.gnu.org/copyleft/gpl.html
 **                 Originally written 2004 by Timwi
 **/

/* - Lots of debug information

#define debuglex printf
#define debuglex2 printf
#define debuglex3 printf
#define debugf printf
#define debugpt_end debug_indent--;
#define debugpt(x) \
    debug_indent++; \
    printf ("%s", addSpaces ("", 2*debug_indent)); \
    printf (x);
#define debugpt2(x,y) \
    debug_indent++; \
    printf ("%s", addSpaces ("", 2*debug_indent)); \
    printf (x, y);
#define debugpt3(x,y,z) \
    debug_indent++; \
    printf ("%s", addSpaces ("", 2*debug_indent)); \
    printf (x, y, z);

/*/

#define debuglex(x)
#define debuglex2(x,y)
#define debuglex3(x,y,z)
#define debugf(x)
#define debugpt_end
#define debugpt(x)
#define debugpt2(x,y)
#define debugpt3(x,y,z)

/**/
