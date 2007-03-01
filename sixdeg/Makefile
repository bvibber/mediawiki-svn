# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $URL: file:///home/river/s2s/linksd/Makefile $ %E% %U%

all:
	$(MAKE) -C libsixdeg all
	$(MAKE) -C linksd all
	$(MAKE) all-real

all-real: linksc liblinksc_jni.so linksc.jar sixdeg.war

linksc: linksc.o liblinksc.o linksc.h libsixdeg/libsixdeg.a
	g++ -O2 linksc.o liblinksc.o -o linksc -Llibsixdeg -lsixdeg
linksc.o: linksc.cc linksc.h
	g++ -O2 -c linksc.cc 
liblinksc.o: liblinksc.cc linksc.h
	g++ -fPIC -O2 -g -Ilibsixdeg -c liblinksc.cc

liblinksc_jni.so: org/wikimedia/links/linksc.class liblinksc.o linksc_jni.cc
	javah -classpath . org.wikimedia.links.linksc
	g++ -fPIC -O2 -shared -I/usr/lib/j2sdk1.5-sun/include -I/usr/lib/j2sdk1.5-sun/include/linux \
			linksc_jni.cc liblinksc.o -o liblinksc_jni.so 

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
	rm -f linksc org/wikimedia/links/*.class linksc_jni.so linkc.jar *.so *.o mkcache org_wikimedia_links_linksc.h sixdeg.war linksc.jar core
	$(MAKE) -C sixdeg clean
	$(MAKE) -C linksd clean

sixdeg.war: linksc.jar webapp/src/index.jsp liblinksc_jni.so webapp/src/main.css webapp/src/6deg.png webapp/src/WEB-INF/web.xml
	rm -f sixdeg.war
	(cd webapp/src; zip -Dr ../../sixdeg.war . -x `find . | grep '\.svn'`)
testdeploy: sixdeg.war
	/opt/SUNWappserver/appserver/bin/asadmin deploy --user kate --port 7070 --force --precompilejsp --contextroot sdtest --name sdtest sixdeg.war
deploy: sixdeg.war
	/opt/SUNWappserver/appserver/bin/asadmin deploy --user kate --port 7070 --force --precompilejsp sixdeg.war
dist:
	$(MAKE) clean
	(cd .. && find linksd -name '.*') | tail +1l >../exclude
	cd .. && tar cvfX linksd.tar exclude linksd
	cd .. && gzip -f linksd.tar
.KEEP_STATE:
