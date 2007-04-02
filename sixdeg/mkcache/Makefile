# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id: Makefile 20916 2007-04-02 11:37:10Z river $

include ../rules.mk

PROGRAM	= mkcache
CPPFLAGS += -I.. -I../libsdstore
LDFLAGS += -L../libsdstore
LIBS	+= -lmysqlclient_r -lz -lsdstore $(BDBLIBS)
SRCS	= mkcache.cc
OBJS	= $(SRCS:.cc=.o)

include ../frag/cxxprog.mk
