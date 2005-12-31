# Makefile for profile collector package
# $Id$
#
#MacOSX Fink library paths 
#CFLAGS+=-I/sw/include/
#LDFLAGS+=-L/sw/lib/

LDFLAGS+=-ldb
CFLAGS+=-Wall -g

all: collector exporter

collector: collector.h collector.c export.c

exporter: export.c collector.h exporter.c

#export: collector.h export.c

clean:
	rm -f collector exporter
