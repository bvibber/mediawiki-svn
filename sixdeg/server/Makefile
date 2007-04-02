# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id$

include ../rules.mk

PROGRAM	= linksd
CPPFLAGS += -I.. -I../libsixdeg -I../client -I../libsdstore
LDFLAGS += -L../libsixdeg -L../libsdstore
LIBS	+= -lsixdeg -lsdstore $(BDBLIBS)

SRCS	= \
	linksd.cc		\
	pathfinder.cc		\
	request_dispatcher.cc	\
	io.cc			\
	client.cc		\
	work_queue.cc		\

OBJS	= $(SRCS:.cc=.o)

include ../frag/cxxprog.mk
