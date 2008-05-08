CC			= cc
CXX			= CC
JAVA			= /usr/java
CPPFLAGS		= -DNO_CRC -I.. -I$(JAVA)/include
CPPFLAGS_solaris	= -I$(JAVA)/include/solaris
CPPFLAGS_linux		= -I$(JAVA)/include/linux
CFLAGS			= -g
LDFLAGS			=
LIBS			= -lz

SRCS	= \
	../pngreader.c	\
	../pngcmd.c	\
	../pngresize.c	\
	../pngutil.c	\
	../pngwriter.c

default:
	@echo "Please make one of the following targets:"
	@echo "  solaris          Solaris shared library with Studio"
	@echo "  solaris-gcc      Solaris shared library with gcc"

pngds/PNGResizer.class:
	$(JAVA)/bin/javac pngds/PNGResizer.java

pngds_PNGResizer.h: pngds/PNGResizer.class
	$(JAVA)/bin/javah -jni pngds.PNGResizer
	
solaris: pngds_PNGResizer.h
	cc -KPIC -G $(CFLAGS) $(CPPFLAGS) $(CPPFLAGS_solaris) $(SRCS) PNGResizer.c -o libpngds.so $(LIBS)
	
solaris-gcc: pngds_PNGResizer.h
	gcc -fPIC -shared $(CFLAGS) $(CPPFLAGS) $(CPPFLAGS_solaris) $(SRCS) PNGResizer.c -o libpngds.so $(LIBS)

linux: pngds_PNGResizer.h
	gcc -fPIC -shared $(CFLAGS) $(CPPFLAGS) $(CPPFLAGS_linux) $(SRCS) PNGResizer.c -o libpngds.so $(LIBS)
clean:
	rm -f libpngds.so pngds/PNGResizer.class pngds_PNGResizer.h *.o

.PHONY: jni clean solaris solaris-gcc
