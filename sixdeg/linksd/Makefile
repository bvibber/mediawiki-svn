# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id$

all: linksd mkcache

linksd: linksd.cc ../linksc.h
	g++ -D_REENTRANT -g -O2 -I.. -I/usr/include/mysql -lmysqlclient -lz linksd.cc -o linksd
mkcache: mkcache.cc ../linksc.h
	g++ -O2 mkcache.cc -o mkcache -I.. -I/usr/include/mysql -L/opt/mysql50/lib/mysql -lmysqlclient -lz

clean:
	rm -f linksd *.o mkcache core
