include ../mk/rules.mk

BINDIR	?= /usr/local/bin
BINMODE	?= 755
OWNER	?= root
GROUP	?= bin
MANSECT	?= 1

MANDIR	?= /usr/local/man/man$(MANSECT)

all: $(PROG)
$(PROG): $(OBJS)
	$(CC) $(CFLAGS) $(LDFLAGS) $(OBJS) -o $@ $(LIBS)
lint:
	$(LINT) $(LINTFLAGS) $(INCLUDES) $(SRCS) $(LIBS)
clean:
	rm -f $(PROG) $(OBJS)

install: all realinstall $(INSTALLEXTRA)
realinstall:
	install -d -m 755 $(BINDIR)
	install -m $(BINMODE) -o $(OWNER) -g $(GROUP) $(PROG) $(BINDIR)
	if test -f $(PROG).$(MANSECT); then \
		install -d -m 755 $(MANDIR); \
		install -m 644 $(PROG).$(MANSECT) $(MANDIR); \
	fi

.PHONY: clean all install realinstall lint
