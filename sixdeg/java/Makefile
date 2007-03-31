# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $URL: file:///home/river/s2s/linksd/Makefile $ %E% %U%

all: liblinksc_jni.so linksc.jar

liblinksc_jni.so: org/wikimedia/links/linksc.class linksc_jni.cc
	javah -classpath . org.wikimedia.links.linksc
	g++ -I.. -I../client -fPIC -O2 -shared -I/usr/lib/j2sdk1.5-sun/include -I/usr/lib/j2sdk1.5-sun/include/linux \
			linksc_jni.cc ../client/liblinksc.o -o liblinksc_jni.so 

org/wikimedia/links/linksc.class: org/wikimedia/links/linksc.java org/wikimedia/links/ErrorException.java
	javac org/wikimedia/links/linksc.java
org/wikimedia/links/NoFromArticleException.class: org/wikimedia/links/NoFromArticleException.java
	javac org/wikimedia/links/NoFromArticleException.java
org/wikimedia/links/NoToArticleException.class: org/wikimedia/links/NoToArticleException.java
	javac org/wikimedia/links/NoToArticleException.java
org/wikimedia/links/ConnectException.class: org/wikimedia/links/ConnectException.java
	javac org/wikimedia/links/ConnectException.java
org/wikimedia/links/ErrorException.class: org/wikimedia/links/ErrorException.java
	javac org/wikimedia/links/ErrorException.java
	
linksc.jar: org/wikimedia/links/linksc.class org/wikimedia/links/NoFromArticleException.class org/wikimedia/links/NoToArticleException.class org/wikimedia/links/ConnectException.class org/wikimedia/links/ErrorException.class
	rm -f linksc.jar
	zip -r linksc.jar  org/wikimedia/links/linksc.class org/wikimedia/links/NoFromArticleException.class org/wikimedia/links/NoToArticleException.class org/wikimedia/links/ConnectException.class org/wikimedia/links/ErrorException.class

clean:
	rm -f org/wikimedia/links/*.class linksc_jni.so linkc.jar *.so *.o org_wikimedia_links_linksc.h linksc.jar
