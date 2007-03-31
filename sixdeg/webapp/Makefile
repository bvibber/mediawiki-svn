# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $URL: file:///home/river/s2s/linksd/Makefile $ %E% %U%

all: sixdeg.war

clean:
	rm -f sixdeg.war

sixdeg.war: src/index.jsp src/main.css src/6deg.png src/WEB-INF/web.xml
	rm -f sixdeg.war
	(cd src; zip -Dr ../sixdeg.war . -x `find . | grep '\.svn'`)
