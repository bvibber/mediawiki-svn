# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id$

include ../config.mk
MYSQL_CPPFLAGS	= $(shell mysql_config --cflags)
MYSQL_LIBS	= $(shell mysql_config --libs_r)
BDB_LIBS	= -ldb-4.4
 
CPPFLAGS	+= -D_REENTRANT -I.. -I../libsixdeg -I../client $(MYSQL_CPPFLAGS)

LINKSD_SRCS	= linksd.cc pathfinder.cc request_dispatcher.cc io.cc client.cc work_queue.cc bdb_adjacency_store.cc
LINKSD_OBJS	= $(LINKSD_SRCS:.cc=.o)

MKCACHE_BDB_SRCS	= mkcache_bdb.cc bdb_adjacency_store.cc
MKCACHE_BDB_OBJS	= $(MKCACHE_BDB_SRCS:.cc=.o)

all: linksd mkcache mkcache_bdb

linksd: $(LINKSD_OBJS) ../libsixdeg/libsixdeg.a
	$(CXX) $(CXXFLAGS) $(LINKSD_OBJS) -o linksd -L../libsixdeg -lsixdeg $(BDB_LIBS) $(MYSQL_LIBS)

mkcache: mkcache.o
	$(CXX) $(CXXFLAGS) mkcache.o -o mkcache $(MYSQL_LIBS)

mkcache_bdb: $(MKCACHE_BDB_OBJS)
	$(CXX) $(CXXFLAGS) $(MKCACHE_BDB_OBJS) -o mkcache_bdb $(BDB_LIBS) $(MYSQL_LIBS)

clean:
	rm -f linksd *.o mkcache mkcache_bdb core

linksd.o: linksd.cc ../client/linksc.h pathfinder.h request_dispatcher.h \
  work_queue.h io.h client.h ../libsixdeg/encode_decode.h
pathfinder.o: pathfinder.cc pathfinder.h bdb_adjacency_store.h
mkcache.o: ../client/linksc.h
io.o: io.cc io.h
client.o: client.cc client.h ../libsixdeg/encode_decode.h
request_dispatcher.o: request_dispatcher.cc request_dispatcher.h \
  work_queue.h pathfinder.h ../libsixdeg/encode_decode.h client.h
work_queue.o: work_queue.cc work_queue.h
mkcache_bdb.o: mkcache_bdb.cc ../client/linksc.h bdb_adjacency_store.h
bdb_adjacency_store.o: bdb_adjacency_store.cc bdb_adjacency_store.h \
  ../client/linksc.h

