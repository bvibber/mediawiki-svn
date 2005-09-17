INSTALL_TARGET?=`php-config --extension-dir`
PRODUCT=utfnormal
VERSION=0.0.1

CXX?=g++

# For Linux
SHARED = -shared

TMPDIST=$(PRODUCT)-$(VERSION)
DISTFILES=Makefile \
  $(PRODUCT).spec \
  $(PRODUCT).cpp $(PRODUCT).i \
  $(PRODUCT)_wrap.cpp php_$(PRODUCT).h \
  test.php


php_$(PRODUCT).so : $(PRODUCT).cpp $(PRODUCT)_wrap.cpp
	$(CXX) -O2 `php-config --includes` -licuuc $(SHARED) -o php_$(PRODUCT).so $(PRODUCT).cpp $(PRODUCT)_wrap.cpp

$(PRODUCT)_wrap.cpp : $(PRODUCT).i
	swig -Wall -php4 -c++ $(PRODUCT).i

install : php_$(PRODUCT).so
	install -d "$(INSTALL_TARGET)"
	install -m 0755 php_$(PRODUCT).so "$(INSTALL_TARGET)"

uninstall :
	rm -f "$(INSTALL_TARGET)"

clean :
	rm -f php_$(PRODUCT).so
	rm -f $(PRODUCT)_wrap.cpp
	rm -f $(PRODUCT).php
	rm -f php_$(PRODUCT).h

test : php_$(PRODUCT).so
	php test.php

distclean : clean
	rm -rf $(TMPDIST)
	rm -f $(TMPDIST).tar.gz

dist : $(DISTFILES) Makefile
	rm -rf $(TMPDIST)
	mkdir $(TMPDIST)
	for x in $(DISTFILES); do cp -p $$x $(TMPDIST)/$$x; done
	tar zcvf $(TMPDIST).tar.gz $(TMPDIST)

rpm : dist
	cp $(TMPDIST).tar.gz /usr/src/redhat/SOURCES
	cp $(PRODUCT).spec /usr/src/redhat/SPECS/$(PRODUCT)-$(VERSION).spec
	cd /usr/src/redhat/SPECS && rpmbuild -ba $(PRODUCT)-$(VERSION).spec
