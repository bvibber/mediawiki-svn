# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id$

CXX		= g++
CXXFLAGS	= -g -O2
CPPFLAGS	= -W -Wall -D_REENTRANT -I.. -I../libsixdeg
MYSQL_CPPFLAGS	= $(shell mysql_config --cflags)
MYSQL_LIBS	= $(shell mysql_config --libs_r)

LINKSD_SRCS	= linksd.cc pathfinder.cc request_dispatcher.cc
LINKSD_OBJS	= $(LINKSD_SRCS:.cc=.o)

all: linksd mkcache

linksd: $(LINKSD_OBJS) ../libsixdeg/libsixdeg.a
	$(CXX) -g -O2 -lmysqlclient -lz $(LINKSD_OBJS) -o linksd -L../libsixdeg -lsixdeg

mkcache: mkcache.o
	$(CXX) $(CXXFLAGS) mkcache.o -o mkcache $(MYSQL_LIBS)

%.o: %.cc
	$(CXX) $(CPPFLAGS) $(MYSQL_CPPFLAGS) $(CXXFLAGS) -c $<

clean:
	rm -f linksd *.o mkcache core

linksd.o: ../linksc.h pathfinder.h
pathfinder.o: pathfinder.h
mkcache.o: ../linksc.h
