VERSION		= 1.7

PREFIX		?= /usr/local
CONFDIR		?= /etc/slayerd
SBINDIR		?= $(PREFIX)/sbin
MANDIR		?= $(PREFIX)/share/man/man8
GROUP		?= root

CXX		= g++
CXXFLAGS	= -O0 -g3 -ggdb
CPPFLAGS	= -DETCDIR=\"$(CONFDIR)\" -DPREFIX=\"$(PREFIX)\" -I/usr/local/include
LDFLAGS		= -L/usr/local/lib
SRCS		= slayerd.cc process.cc config.cc util.cc
OBJS		= $(SRCS:.cc=.o)
LIBS		=

ifeq ($(shell uname),FreeBSD)
LIBS += -lkvm
GROUP = wheel
endif

all: slayerd

slayerd: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $(OBJS) -o $@ -lboost_filesystem $(LIBS)

install: slayerd
	install -d $(DESTDIR)$(SBINDIR)
	install -d $(DESTDIR)$(CONFDIR)
	install -d $(DESTDIR)$(MANDIR)
	install -c -s -o root -g $(GROUP) -m 755 slayerd $(DESTDIR)$(SBINDIR)
	install -c -o root -g $(GROUP) -m 640 slayerd.conf.example $(DESTDIR)$(CONFDIR)
	install -c -o root -g $(GROUP) -m 640 mailmessage $(DESTDIR)$(CONFDIR)
	install -c -o root -g $(GROUP) -m 644 slayerd.8 $(DESTDIR)$(MANDIR)

.cc.o:
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) -DVERSION=\"$(VERSION)\" -c $<

clean:
	rm -f slayerd *.o

.PHONY: clean install
.SUFFICES: .cc .o
