dnl $Id$
dnl config.m4 for extension wmerrors

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(wmerrors, for wmerrors support,
dnl Make sure that the comment is aligned:
dnl [  --with-wmerrors             Include wmerrors support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(wmerrors, whether to enable wmerrors support,
[  --enable-wmerrors           Enable wmerrors support])

if test "$PHP_WMERRORS" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-wmerrors -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/wmerrors.h"  # you most likely want to change this
  dnl if test -r $PHP_WMERRORS/$SEARCH_FOR; then # path given as parameter
  dnl   WMERRORS_DIR=$PHP_WMERRORS
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for wmerrors files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       WMERRORS_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$WMERRORS_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the wmerrors distribution])
  dnl fi

  dnl # --with-wmerrors -> add include path
  dnl PHP_ADD_INCLUDE($WMERRORS_DIR/include)

  dnl # --with-wmerrors -> check for lib and symbol presence
  dnl LIBNAME=wmerrors # you may want to change this
  dnl LIBSYMBOL=wmerrors # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $WMERRORS_DIR/lib, WMERRORS_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_WMERRORSLIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong wmerrors lib version or lib not found])
  dnl ],[
  dnl   -L$WMERRORS_DIR/lib -lm -ldl
  dnl ])
  dnl
  dnl PHP_SUBST(WMERRORS_SHARED_LIBADD)

  PHP_NEW_EXTENSION(wmerrors, wmerrors.c, $ext_shared)
fi
