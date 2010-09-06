
AC_INIT(libmwparser, 0.4-SNAPSHOT, andreas.jonsson@kreablo.se)
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

AC_PROG_SWIG([1.3.40])

AX_PERL_EXT_FLAGS([PERLXS_CFLAGS], [PERLXS_LDFLAGS])
AC_SUBST([PERLXS_CFLAGS])
AC_SUBST([PERLXS_LDFLAGS])

PKG_CHECK_MODULES([GLIB], [glib-2.0 >= 2.24.1])

AC_ARG_VAR([TARGET_LANGUAGE], [Target language for wrapper library.])

if test "x${TARGET_LANGUAGE}" == x; then
   TARGET_LANGUAGE=php;
fi

if test "x$TARGET_LANGUAGE" == xperl; then
   TARGET_LANGUAGE_CFLAGS='-DTARGET_LANGUAGE_PERL $(PERLXS_CFLAGS)'
   TARGET_LANGUAGE_LDFLAGS='$(PERLXS_LDFLAGS)'
elif test "x$TARGET_LANGUAGE" == xphp; then
   TARGET_LANGUAGE_CFLAGS='-DTARGET_LANGUAGE_PHP $(PHP_CFLAGS)'
   TARGET_LANGUAGE_LDFLAGS='$(PHP_LDFLAGS)'
fi

AC_SUBST([TARGET_LANGUAGE_CFLAGS])

AM_PROG_LIBTOOL
AC_PROG_LIBTOOL

AC_CONFIG_TESTDIR([tests])

AC_CONFIG_HEADERS([config.h])
AC_CONFIG_FILES([Makefile] [tests/Makefile] [tests/atlocal] [wrapper/Makefile])
AC_OUTPUT

