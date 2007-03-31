# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $URL: file:///home/river/s2s/linksd/Makefile $ %E% %U%

all: linksc

linksc: linksc.o liblinksc.o linksc.h
	g++ -O2 linksc.o liblinksc.o -o linksc -L../libsixdeg -lsixdeg
linksc.o: linksc.cc linksc.h
	g++ -I../libsixdeg  -O2 -c linksc.cc 
liblinksc.o: liblinksc.cc linksc.h
	g++ -I../libsixdeg -fPIC -O2 -g -Ilibsixdeg -c liblinksc.cc

clean:
	rm -f linksc *.so *.o core
