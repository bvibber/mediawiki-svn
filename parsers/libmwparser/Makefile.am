vpath %.c $(top_srcdir)/src
vpath %.g $(top_srcdir)/src
vpath %.php $(top_srcdir)/src
vpath %.h $(top_srcdir)/include

AUTOMAKE_OPTIONS = gnu
AC_LIBTOOLFLAGS =
## --silent
ACLOCAL_AMFLAGS = -I m4

SUBDIRS = tests wrapper

ANTLR_LEXER  = mwWikitextLexer
ANTLR_PARSER = mwWikitextParser
ANTLR_LEXER_DELEGATES =

ANTLR_GRAMMARS = $(ANTLR_LEXER:=.g) $(ANTLR_PARSER:=.g)

ANTLR_SOURCES = $(ANTLR_LEXER:=.c) $(ANTLR_PARSER:=.c) $(ANTLR_LEXER:=.h) $(ANTLR_PARSER:=.h)

ANTLR_LEXER_DELEGATE_SOURCES =

CLEANFILES = $(ANTLR_LEXER_DELEGATE_SOURCES) $(ANTLR_LEXER_DELEGATES:=.tokens) $(ANTLR_SOURCES) mwlexerpredicates.h mwlexerpredicatedefs.inc

lib_LTLIBRARIES = libmwparser.la

nodist_libmwparser_la_SOURCES = $(ANTLR_SOURCES) $(ANTLR_LEXER_DELEGATE_SOURCES)

mwlexercontext.lo mwLexer.lo: mwlexerpredicates.h
mwlexerpredicates.h: mwlexerpredicatedefs.inc
mwlexerpredicates.h: mwlexerpredicates.php mwlexerpredicatetable.php
	$(PHP) $< > $@

mwlexerpredicatedefs.inc: mwlexerpredicatedefs.php mwlexerpredicatetable.php
	$(PHP) $< > $@


.PHONY: debug_echo
debug_echo:
	echo $(nodist_libmwparser_la_SOURCES)
	echo $(libmwparser_la_OBJECTS)

libmwparser_la_SOURCES		=		\
	include/mwattributes.h			\
	include/mwbasicevents.h			\
	include/mwconfig.h			\
	include/mwformats.h			\
	include/mwheadings.h			\
	include/mwhtml.h			\
	include/mwkeyvalue.h			\
	include/mwlexercontext.h		\
	include/mwlinks.h			\
	include/mwlistener.h			\
	include/mwparser.h			\
	include/mwparsercontext.h		\
	include/mwtables.h			\
        include/mwtagext.h			\
	include/mwutils.h			\
	include/tracingcontext.h		\
	src/mwattributes.c			\
	src/mwbasicevents.c			\
	src/mwformats.c				\
	src/mwheadings.c			\
	src/mwhtml.c				\
	src/mwlexercontext.c			\
	src/mwlinks.c				\
        src/mwparser.c				\
	src/mwparsercontext.c			\
        src/mwtagext.c				\
	src/mwtables.c				\
	src/tracingcontext.c

src/mwparsercontext.c src/mwparser.c: mwWikitextParser.h
src/mwlexercontext.c mwparser.c: mwWikitextLexer.h

EXTRA_DIST = $(ANTLR_LEXER:%=src/%.g) $(ANTLR_PARSER:%=src/%.g) $(ANTLR_LEXER_DELEGATES:%=src/%.g)	\
	src/mwlexerpredicates.php									\
	src/mwlexerpredicatetable.php									\
	src/mwlexerpredicatedefs.php									\
	antlr.patch

libmwparser_la_LIBS = $(GLIB_LIBS)

libmwparser_la_CFLAGS = $(GLIB_CFLAGS) -std=gnu99

libmwparser_la_LDFLAGS = -release 0.1 -version-info 0:0:0

INCLUDES = -I$(top_srcdir)/include

include_HEADERS = config.h

ANTLR_PRODUCTS = 

ANTLR_OPTIONS = 

SUFFIXES = .g .u

%.tokens %.c %Lexer.c %Parser.c %Lexer.h %Parser.h %.h: %.g
	$(ANTLR) -fo $(top_builddir) $(ANTLR_OPTIONS) $<

%.u: %.g
	$(ANTLR) -fo $(top_builddir) -depend $< > $@
	@echo -n "ANTLR_PRODUCTS += " > $@.tmp
	@grep : $@ | cut -d : -f 1 | tr -d ' ' | { while read f; do test "$$f" != "$<" && echo -n "$$f "; done } >> $@.tmp
	@cat $@.tmp >> $@
	@rm $@.tmp

clean-local:
	-rm $(ANTLR_PRODUCTS)
	-rm $(ANTLR_GRAMMARS:.g=.u)

-include $(ANTLR_GRAMMARS:.g=.u)
