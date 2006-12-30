.PHONY: all clean test rpm deb debclean

VERSION=0.0.3

PACKAGE=dbzip2-$(VERSION)
TARBALL=$(PACKAGE).tar.gz

DESTDIR?=

DEBFILES=\
debian/changelog\
debian/compat\
debian/control\
debian/copyright\
debian/dbzip2.substvars\
debian/dirs\
debian/docs\
debian/README.Debian\
debian/rules

DISTDIRS=\
debian

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
sigcheck.py\
$(DEBFILES)

all: dbzutil.so

clean:
	rm -rf build
	rm -rf debbuild
	rm -rf tarbuild
	rm -f dbzutil.so
	rm -f $(TARBALL)

test: dbzutil.so
	python dbzutil-test.py
	python BitShifter.py

dbzutil.so: dbzutil-module.c dbzutil-setup.py
	python dbzutil-setup.py build_ext -i

dist: $(TARBALL)

$(TARBALL): $(DISTFILES)
	rm -rf tarbuild
	mkdir tarbuild
	mkdir tarbuild/$(PACKAGE)
	cd tarbuild/$(PACKAGE) && mkdir $(DISTDIRS)
	for x in $(DISTFILES); do cp -p $$x tarbuild/$(PACKAGE)/$$x; done
	cd tarbuild && tar zcvf ../$(TARBALL) $(PACKAGE)/
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

rpm: $(TARBALL) dbzip2d.spec
	cp -p $(TARBALL) /usr/src/redhat/SOURCES/
	rpmbuild -ba dbzip2.spec

deb: $(TARBALL) $(DEBFILES)
	rm -rf debbuild
	mkdir debbuild
	cd debbuild && tar zxvf ../$(TARBALL)
	cd debbuild/$(PACKAGE) && dpkg-buildpackage -rfakeroot
	rm -rf debbuild/$(PACKAGE)

debclean: clean
