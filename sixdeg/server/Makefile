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
#DO NOT DELETE -- make depend needs it
linksd.o: linksd.cc ../client/linksc.h pathfinder.h request_dispatcher.h \
  work_queue.h io.h client.h ../libsixdeg/encode_decode.h \
  ../libsdstore/bdb_adjacency_store.h ../client/linksc.h ../defs.h \
  ../libsixdeg/log.h
pathfinder.o: pathfinder.cc pathfinder.h ../client/linksc.h \
  ../libsdstore/bdb_adjacency_store.h ../client/linksc.h
request_dispatcher.o: request_dispatcher.cc request_dispatcher.h \
  work_queue.h pathfinder.h ../client/linksc.h \
  ../libsixdeg/encode_decode.h client.h
io.o: io.cc io.h ../libsixdeg/log.h
client.o: client.cc client.h ../libsixdeg/encode_decode.h
work_queue.o: work_queue.cc work_queue.h ../libsixdeg/log.h
