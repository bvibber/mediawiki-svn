vpath %.i $(srcdir)/src

PHP_CFLAGS=`php-config --includes`
PHP_LDFLAGS=`php-config --ldflags`

CLEANFILES=mwp_wrap.c mwp.pm

lib_LTLIBRARIES = libmwp.la

libmwp_la_SOURCES = src/mwscriptbufferlistener.c	\
	mwp_wrap.c

libmwp_la_CFLAGS = $(TARGET_LANGUAGE_CFLAGS) $(GLIB_CFLAGS)

libmwp_la_LDFLAGS = $(TARGET_LANGUAGE_LDFLAGS) $(GLIB_LDFLAGS)

INCLUDES =  -I$(srcdir)/include -I$(top_srcdir)/include

include_HEADERS = $(top_builddir)/config.h

mwp_wrap.c: mwparser.i
	swig -$(TARGET_LANGUAGE) -Wall -o $@ $<


