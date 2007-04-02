# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id$

include ../rules.mk

SRCS	= encode_decode.cc
OBJS	= $(SRCS:.cc=.o)

all: libsixdeg.a

libsixdeg.a: $(OBJS)
	ar crv $@ $<
	ranlib $@

clean:
	rm -f libsixdeg.a *.o

encode_decode.cc: encode_decode.h
