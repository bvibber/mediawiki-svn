# $Id$
#
# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.

include ../config.mk

all: linksc

linksc: linksc.o liblinksc.o linksc.h
	$(CXX) $(CXXFLAGS) linksc.o liblinksc.o -o linksc -L../libsixdeg -lsixdeg
linksc.o: linksc.cc linksc.h
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) -I.. -I../libsixdeg -c linksc.cc 
liblinksc.o: liblinksc.cc linksc.h
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) $(PICFLAGS) -I.. -I../libsixdeg -c liblinksc.cc

clean:
	rm -f linksc *.so *.o core
