# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id: Makefile 20916 2007-04-02 11:37:10Z river $

include ../rules.mk

PROGRAM	= mkcache
CPPFLAGS += -I.. -I../libsdstore
LDFLAGS += -L../libsdstore -L../libsixdeg
LIBS	+= -lmysqlclient_r -lz -lsdstore -lsixdeg $(BDBLIBS)
SRCS	= mkcache.cc
OBJS	= $(SRCS:.cc=.o)

include ../frag/cxxprog.mk
#DO NOT DELETE -- make depend needs it
mkcache.o: mkcache.cc /usr/include/mysql/mysql.h \
  /usr/include/mysql/mysql_com.h /usr/include/mysql/mysql_time.h \
  /usr/include/mysql/mysql_version.h /usr/include/mysql/typelib.h \
  /usr/include/mysql/my_list.h /usr/include/mysql/my_alloc.h \
  ../client/linksc.h ../libsdstore/bdb_adjacency_store.h \
  ../client/linksc.h ../defs.h
