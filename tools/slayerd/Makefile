VERSION		= 1.3

PREFIX		?= /usr/local
CONFDIR		?= /etc/slayerd
SBINDIR		?= $(PREFIX)/sbin
MANDIR		?= $(PREFIX)/share/man/man8

CXX		= g++
CXXFLAGS	= -O2 -g3 -ggdb
CPPFLAGS	= -DETCDIR=\"$(CONFDIR)\" -DPREFIX=\"$(PREFIX)\"
LDFLAGS		=
SRCS		= slayerd.cc
OBJS		= $(SRCS:.cc=.o)

slayerd: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $^ -o $@ -lboost_filesystem

install: slayerd
	install -d $(DESTDIR)$(SBINDIR)
	install -d $(DESTDIR)$(CONFDIR)
	install -d $(DESTDIR)$(MANDIR)
	install -c -s -o root -g root -m 755 slayerd $(DESTDIR)$(SBINDIR)
	install -c -o root -g root -m 640 slayerd.conf.example $(DESTDIR)$(CONFDIR)
	install -c -o root -g root -m 640 mailmessage $(DESTDIR)$(CONFDIR)
	install -c -o root -g root -m 644 slayerd.8 $(DESTDIR)$(MANDIR)

.cc.o:
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) -DVERSION=\"$(VERSION)\" -c $<

clean:
	rm -f slayerd slayerd.o

.PHONY: clean install
.SUFFICES: .cc .o
