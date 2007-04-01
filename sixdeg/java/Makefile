# $Id$
#
# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.

include ../config.mk

CLASSES= \
	org/wikimedia/links/ErrorException.class		\
	org/wikimedia/links/RequestEncoder.class		\
	org/wikimedia/links/RequestDecoder.class		\
	org/wikimedia/links/linksc.class 			\

all: linksc.jar

.java.class:
	$(JAVAC) $<

linksc.jar: $(CLASSES)
	rm -f linksc.jar
	zip -r linksc.jar org/wikimedia/links/*.class

clean:
	rm -f org/wikimedia/links/*.class linksc.jar

.SUFFIXES: .java .class .o .so .cc .h .jar
