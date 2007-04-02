# $Id$
#
# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.

include ../rules.mk

all: sixdeg.war

clean:
	rm -f sixdeg.war

DATA =\
	src/header.jsp		\
	src/footer.jsp		\
	src/path.jsp		\
	src/input.jsp		\
	src/error.jsp		\
	src/index.jsp		\
	src/main.css		\
	src/6deg.png		\
	src/WEB-INF/web.xml	\

JAVA_OBJS = \
	src/WEB-INF/classes/sixdeg/PathFinder.class

src/WEB-INF/classes/sixdeg/%.class: sixdeg/%.java
	$(JAVAC) -d src/WEB-INF/classes -classpath src/WEB-INF/lib/xwork-2.0.1.jar:src/WEB-INF/lib/struts2-core-2.0.6.jar:src/WEB-INF/lib/linksc.jar $<

sixdeg.war: $(DATA) ../java/linksc.jar $(JAVA_OBJS)
	rm -f sixdeg.war
	cp ../java/linksc.jar src/WEB-INF/lib/
	(cd src; zip -Dr ../sixdeg.war . -x `find . | grep '\.svn'`)
