SUBDIRS		= libtsutils watcherd acctexp listlogins lsexp whodo whinequota pwtool days2date date2days
MAKEFLAGS	= --no-print-directory

all clean depend install lint:
	@for d in $(SUBDIRS); do \
		echo "$@ ==> $$d"; \
		$(MAKE) -C $$d $@ || exit 1; \
		echo "$@ <== $$d"; \
	done

