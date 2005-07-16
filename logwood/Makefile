# $Id$
MYSQL_CONFIG=	/usr/local/bin/mysql_config

CC=		cc
CPPFLAGS=	-Wall 					\
		$(shell pkg-config --cflags glib-2.0)	\
		$(shell $(MYSQL_CONFIG) --include)	\
		$(shell pcre-config --cflags)
CFLAGS=		-g -march=opteron -mcpu=opteron
LDFLAGS=	$(shell pkg-config --libs glib-2.0)	\
		$(shell $(MYSQL_CONFIG) --libs_r)	\
		$(shell pcre-config --libs)

logwood: logwood.o
	$(CC) $(CFLAGS) logwood.o -o logwood $(LDFLAGS)
.c.o:
	$(CC) $(CPPFLAGS) $(CFLAGS) -c $<
clean:
	rm logwood logwood.o
.PHONY: clean
