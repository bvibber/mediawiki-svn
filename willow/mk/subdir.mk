# @(#) $Header$
#
# Run make in subdirectories.

default: all

all install lint clean depend:
	@for dir in $(SUBDIRS); do \
		echo "$@ ==> $$dir" ;\
		cd $$dir && $(MAKE) $@ || exit 1 ;\
		echo "$@ <== $$dir" ;\
		cd .. ;\
	done
