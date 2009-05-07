VERSION		= 1.6

PREFIX		?= /usr/local
CONFDIR		?= /etc/slayerd
SBINDIR		?= $(PREFIX)/sbin
MANDIR		?= $(PREFIX)/share/man/man8

CXX		= g++
CXXFLAGS	= -O2 -g3 -ggdb
CPPFLAGS	= -DETCDIR=\"$(CONFDIR)\" -DPREFIX=\"$(PREFIX)\" -I/usr/local/include
LDFLAGS		= -L/usr/local/lib
SRCS		= slayerd.cc process.cc
OBJS		= $(SRCS:.cc=.o)

slayerd: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $(OBJS) -o $@ -lboost_filesystem

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
