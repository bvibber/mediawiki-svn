
AC_INIT(libmwparser, 0.3, andreas.jonsson@kreablo.se)
AC_COPYRIGHT([
Copyright 2010  Andreas Jonsson

This file is part of libmwparser

Libwikimodel is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or (at
your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
])

AM_INIT_AUTOMAKE(foreign)
AC_LANG(C)
AC_PROG_CC([xlc aCC gcc cc])

AC_PATH_PROG([PHP], [php])

AC_CONFIG_MACRO_DIR([m4])

AC_CHECK_PROG(ANTLR, [antlr3], [antlr3])
if test "x$ANTLR" = x; then
        AC_MSG_NOTICE([antlr3 wrapper not found, checking direct java invocation])
        AC_CHECK_PROG(JAVA, [java], [java])
        if test "x$JAVA" = x; then
                AC_MSG_FAILURE([java not found; check your java installation])
        else
                if $JAVA org.antlr.Tool > /dev/null 2>&1; then
                        AC_MSG_NOTICE([Direct java invocation working, using java org.antlr.Tool to invoke antlr])
                        ANTLR="$JAVA org.antlr.Tool"
                else
                        AC_MSG_FAILURE([antlr3 wrapper not found and direct java invocation failed; check your antlr3/java installation])
                fi
        fi
fi
AC_SUBST(ANTLR)

AC_CHECK_HEADER(antlr3.h, , AC_MSG_ERROR([antlr3.h not found]))
AC_CHECK_LIB([antlr3c], [antlr3BaseRecognizerNew], [ANTLR3C_LIBS="-lantlr3c"], AC_MSG_ERROR([ANTLR3 C runtime (libantlr3c) not found]))
AC_SUBST(ANTLR3C_LIBS)

AC_HEADER_STDC
AC_HEADER_STDBOOL
AC_HEADER_ASSERT
AC_CHECK_HEADERS([wchar.h])
AC_CHECK_SIZEOF([wchar_t], [], [#include<wchar.h>])
AC_C_BIGENDIAN

AC_CHECK_HEADER(tre/regex.h, AC_DEFINE([HAVE_TRE_REGEX_H], [1], [Header file for supporting wide character regexps.]), AC_MSG_ERROR([tre/regex.h not found]))
AC_CHECK_LIB([tre], [tre_regwcomp], [LIBREGEX="-ltre"], AC_MSG_ERROR([Regexp library for wide characters not found.]))

AC_SUBST(LIBREGEX)

AM_PROG_LIBTOOL
AC_PROG_LIBTOOL

AC_CONFIG_TESTDIR([tests])

AC_CONFIG_HEADERS([config.h])
AC_CONFIG_FILES([Makefile] [tests/Makefile] [tests/atlocal])
AC_OUTPUT

