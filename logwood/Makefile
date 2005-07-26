# $Id$
MYSQL_CONFIG=	/usr/local/mysql50/bin/mysql_config

CC=		cc
CXX=		c++

CPPFLAGS=	-Wall 					\
		$(shell pkg-config --cflags glib-2.0)	\
		$(shell $(MYSQL_CONFIG) --include)	\
		$(shell pcre-config --cflags)
CFLAGS=		-g -march=opteron -mcpu=opteron
LDFLAGS=	$(shell pkg-config --libs glib-2.0)	\
		$(shell $(MYSQL_CONFIG) --libs_r)	\
		$(shell pcre-config --libs)

logwood: logwood.o lwsql.o
	$(CXX) $(CFLAGS) logwood.o lwsql.o -o logwood $(LDFLAGS)
.c.o:
	$(CC) $(CPPFLAGS) $(CFLAGS) -c $<
.cxx.o:
	$(CXX) $(CPPFLAGS) $(CFLAGS) -c $<
logwood.o: logwood.cxx lwsql.hxx
lwsql.o: lwsql.cxx lwsql.hxx
clean:
	rm logwood logwood.o
.PHONY: clean
.SUFFIXES: .c .o .cxx
