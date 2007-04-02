# $Id$
#
# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.

include plat_frag.mk

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

plat_frag.mk:
	@echo ""
	@echo "You need to configure the source tree before you build:"
	@echo ""
	@echo "   make -f <platform>.mk"
	@echo ""
	@echo "Recognised platforms:"
	@echo ""
	@echo "   solaris           - Solaris"
	@echo "   solaris-gcc       - Solaris with GCC compiler"
	@echo "   linux             - GNU/Linux"
	@echo ""
	@echo "Then copy local.mk.sample to local.mk and edit it for your site."
	@exit 1
