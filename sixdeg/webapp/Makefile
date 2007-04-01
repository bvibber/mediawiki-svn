# $Id$
#
# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.

all: sixdeg.war

clean:
	rm -f sixdeg.war

sixdeg.war: src/index.jsp src/main.css src/6deg.png src/WEB-INF/web.xml ../java/linksc.jar
	rm -f sixdeg.war
	cp ../java/linksc.jar src/WEB-INF/lib/
	(cd src; zip -Dr ../sixdeg.war . -x `find . | grep '\.svn'`)
