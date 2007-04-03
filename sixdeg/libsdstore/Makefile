# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id$

include ../rules.mk

LIBRARY	= libsdstore.a
SRCS	= bdb_adjacency_store.cc
OBJS	= $(SRCS:.cc=.o)

include ../frag/cxxlib.mk
#DO NOT DELETE -- make depend needs it
bdb_adjacency_store.o: bdb_adjacency_store.cc bdb_adjacency_store.h \
  ../client/linksc.h ../libsixdeg/log.h
