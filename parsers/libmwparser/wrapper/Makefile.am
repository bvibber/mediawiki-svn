vpath %.i $(srcdir)/src

PHP_CFLAGS=`php-config --includes`
PHP_LDFLAGS=`php-config --ldflags`
PHP_LIBS=`php-config --libs`

CLEANFILES=mwp_wrap.c mwp.pm mwp.php php_mwp.h

lib_LTLIBRARIES = libmwp.la

libmwp_la_SOURCES =				\
	src/mwlinkresolvercallback.c		\
	src/mwscriptbufferlistener.c		\
	src/mwparser.i				\
	include/mwlinkresolvercallback.h 	\
	include/mwscriptbuf.h


nodist_libmwp_la_SOURCES = mwp_wrap.c

libmwp_la_CFLAGS = $(TARGET_LANGUAGE_CFLAGS) $(GLIB_CFLAGS) -Wall -Wno-unused

libmwp_la_LDFLAGS = $(TARGET_LANGUAGE_LDFLAGS) $(GLIB_LDFLAGS) -lmwparser -release $(PACKAGE_VERSION)

libmwp_la_LIBADD = 

INCLUDES =  -I$(srcdir)/include -I$(top_srcdir)/include


include_HEADERS = $(top_builddir)/config.h

mwp_wrap.c: mwparser.i
	swig -$(TARGET_LANGUAGE) $(INCLUDES) -Wall -o $@ $<

install-data-hook:
	if test x$(TARGET_LANGUAGE) = xphp ; then $(MKDIR_P) $(datarootdir)/php/MediaWiki && $(INSTALL_SCRIPT) mwp.php $(datarootdir)/php/MediaWiki; fi
