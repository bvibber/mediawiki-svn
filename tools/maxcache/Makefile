all: maxcache

CFLAGS:=$(CFLAGS) -ggdb3 -Wall

maxcache: maxcache.o App.o CacheEntry.o Cache.o
	g++ $^ $(CFLAGS) -lboost_system -o $@

%.o : %.cpp
	g++ -c $(CFLAGS) $< -o $@
