/**
 **  This file is part of the flex/bison-based parser for MediaWiki.
 **    You can change these defines to "printf" to have the lexer
 **         and/or the parser output debugging information.
 **
 ** This source file is licensed unter the GNU General Public License
 **               http://www.gnu.org/copyleft/gpl.html
 **                 Originally written 2004 by Timwi
 **/


/* Change these to
    #define debuglex printf
    #define debuglex2 printf
   to have the lexer output all the tokens generated. */

#define debuglex(x)
#define debuglex2(x,y)


/* Change this one to
    #define debugf printf
   to have the parser output all reductions. */

#define debugf(x)
