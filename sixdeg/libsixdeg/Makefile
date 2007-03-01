# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id$

CXX		= g++
CXXFLAGS	= -g -O2
CPPFLAGS	= -W -Wall -D_REENTRANT -I.. 

SRCS	= encode_decode.cc
OBJS	= $(SRCS:.cc=.o)

all: libsixdeg.a

libsixdeg.a: $(OBJS)
	ar crv $@ $<
	ranlib $@

%.o: %.cc
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) -c $<

clean:
	rm -f libsixdeg.a *.o

encode_decode.cc: encode_decode.h
