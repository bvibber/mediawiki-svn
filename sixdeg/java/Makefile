# $Id$
#
# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.

include ../config.mk

CLASSES= \
	org/wikimedia/links/linksc.class 			\
	org/wikimedia/links/NoFromArticleException.class 	\
	org/wikimedia/links/NoToArticleException.class 		\
	org/wikimedia/links/ConnectException.class 		\
	org/wikimedia/links/ErrorException.class		\
	org/wikimedia/links/RequestEncoder.class		\
	org/wikimedia/links/RequestDecoder.class		\

all: liblinksc_jni.so linksc.jar

liblinksc_jni.so: org/wikimedia/links/linksc.class linksc_jni.cc
	$(JAVAH) -classpath . org.wikimedia.links.linksc
	$(CXX) $(CXXFLAGS) $(CPPFLAGS) -I.. -I../client -fPIC -shared \
			-I$(JAVA_HOME)/include -I$(JAVA_HOME)/include/linux \
			linksc_jni.cc ../client/liblinksc.o -o liblinksc_jni.so 

.java.class:
	$(JAVAC) $<

linksc.jar: $(CLASSES)
	rm -f linksc.jar
	zip -r linksc.jar org/wikimedia/links/*.class

clean:
	rm -f org/wikimedia/links/*.class linksc_jni.so linkc.jar *.so *.o org_wikimedia_links_linksc.h linksc.jar

.SUFFIXES: .java .class .o .so .cc .h .jar
