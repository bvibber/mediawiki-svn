# $Id$
#
# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.

include plat_frag.mk

MAKEFLAGS = --no-print-directory
SUBDIRS	= libsixdeg client java libsdstore mkcache server webapp

all clean depend:
	@for dir in $(SUBDIRS); do \
		echo "$@ ==> $$dir"; \
		$(MAKE) -C $$dir $@ || exit 1; \
	done
	@$(MAKE) $@-real

all-real:
clean-real:
depend-real:

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
