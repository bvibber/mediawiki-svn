.PHONY: all clean test rpm deb debclean

VERSION=0.0.3

INSTALL_PREFIX?=

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

install:
	install -d "$(INSTALL_ROOT)/usr/lib/dbzip2"
	install -m 0644 \
  BitShifter.py \
  DistBits.py \
  dbzutil.so \
  sigcheck.py \
  "$(INSTALL_ROOT)/usr/lib/dbzip2/"
	install -m 0755 \
  dbzip2 \
  dbzip2d \
  "$(INSTALL_ROOT)/usr/lib/dbzip2/"
	install -d "$(INSTALL_ROOT)/usr/bin"
	ln -s ../lib/dbzip2/dbzip2 "$(INSTALL_ROOT)/usr/bin/dbzip2"
	ln -s ../lib/dbzip2/dbzip2d "$(INSTALL_ROOT)/usr/bin/dbzip2d"
	install -d "$(INSTALL_ROOT)/etc"
	install -m 0644 dbzip2.conf "$(INSTALL_ROOT)/etc/dbzip2.conf"
	install -d "$(INSTALL_ROOT)/etc/init.d"
	install -m 0755 dbzip2d.service "$(INSTALL_ROOT)/etc/init.d/dbzip2d"
	install -d "$(INSTALL_ROOT)/var/log"

rpm: dist dbzip2d.spec
	cp -p dbzip2-$(VERSION).tar.gz /usr/src/redhat/SOURCES/
	rpmbuild -ba dbzip2.spec

deb: dist debian/control debian/rules debian/postinst debian/prerm
	dpkg-buildpackage -rfakeroot

debclean: clean
	rm -rf debian/dbzip2
	rm -rf debian/tmp
