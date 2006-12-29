.PHONY: all clean test rpm deb debclean

VERSION=0.0.3

DESTDIR?=

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
	rm -rf build
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

install:
	install -d "$(DESTDIR)/usr/lib/dbzip2"
	install -m 0644 \
  BitShifter.py \
  DistBits.py \
  dbzutil.so \
  sigcheck.py \
  "$(DESTDIR)/usr/lib/dbzip2/"
	install -m 0755 \
  dbzip2 \
  dbzip2d \
  "$(DESTDIR)/usr/lib/dbzip2/"
	install -d "$(DESTDIR)/usr/bin"
	ln -s ../lib/dbzip2/dbzip2 "$(DESTDIR)/usr/bin/dbzip2"
	ln -s ../lib/dbzip2/dbzip2d "$(DESTDIR)/usr/bin/dbzip2d"
	install -d "$(DESTDIR)/etc"
	install -m 0644 dbzip2.conf "$(DESTDIR)/etc/dbzip2.conf"
	install -d "$(DESTDIR)/etc/init.d"
	install -m 0755 dbzip2d.service "$(DESTDIR)/etc/init.d/dbzip2d"
	install -d "$(DESTDIR)/var/log"

rpm: dist dbzip2d.spec
	cp -p dbzip2-$(VERSION).tar.gz /usr/src/redhat/SOURCES/
	rpmbuild -ba dbzip2.spec

deb: dist debian/control debian/rules
	dpkg-buildpackage -rfakeroot

debclean: clean
	rm -rf debian/dbzip2
	rm -rf debian/tmp
