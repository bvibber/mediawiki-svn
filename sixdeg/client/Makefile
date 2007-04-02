# $Id$
#
# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.

include ../rules.mk

LDFLAGS += -L../libsixdeg
LIBS 	+=  -lsixdeg

SRCS	=	linksc.cc	\
		liblinksc.cc	\

OBJS	= 	$(SRCS:.cc=.o)

all: linksc

linksc: linksc.o liblinksc.o linksc.h
	$(CXX) $(CXXFLAGS) $(LDFLAGS) linksc.o liblinksc.o -o linksc $(LIBS)

clean:
	rm -f linksc *.so *.o core
