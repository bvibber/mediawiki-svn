.PHONY: all clean test rpm

VERSION=0.0.3

DISTFILES=\
BitShifter.py\
DistBits.py\
Makefile\
README\
dbzip2\
dbzip2.conf\
dbzip2.spec\
dbzip2d\
dbzip2d.service\
dbzutil-module.c\
dbzutil-setup.py\
dbzutil-test.py\
piperate\
sigcheck.py

all: dbzutil.so

clean:
	rm -f dbzutil.so
	rm -f dbzip2-$(VERSION).tar.gz

test: dbzutil.so
	python dbzutil-test.py
	python BitShifter.py

dbzutil.so: dbzutil-module.c dbzutil-setup.py
	python dbzutil-setup.py build_ext -i

dist: dbzip2-$(VERSION).tar.gz

dbzip2-$(VERSION).tar.gz: $(DISTFILES)
	rm -rf tarbuild
	mkdir tarbuild
	mkdir tarbuild/dbzip2-$(VERSION)
	cp -p $(DISTFILES) tarbuild/dbzip2-$(VERSION)
	cd tarbuild && tar zcvf ../dbzip2-$(VERSION).tar.gz dbzip2-$(VERSION)/
	rm -rf tarbuild

rpm: dist dbzip2d.spec
	cp -p dbzip2-$(VERSION).tar.gz /usr/src/redhat/SOURCES/
	rpmbuild -ba dbzip2.spec
