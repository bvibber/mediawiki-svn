# $Id$
#
# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.

all:
	$(MAKE) -C libsixdeg all
	$(MAKE) -C client all
	$(MAKE) -C java all
	$(MAKE) -C server all
	$(MAKE) -C webapp all
	$(MAKE) all-real

all-real:

clean:
	rm -f linksc.jar
	$(MAKE) -C libsixdeg clean
	$(MAKE) -C server clean
	$(MAKE) -C client clean
	$(MAKE) -C java clean
	$(MAKE) -C webapp clean

dist:
	$(MAKE) clean
	(cd .. && find linksd -name '.*') | tail +1l >../exclude
	cd .. && tar cvfX linksd.tar exclude linksd
	cd .. && gzip -f linksd.tar
