VERSION		= 1.8

PREFIX		?= /usr/local
CONFDIR		?= /etc/slayerd
SBINDIR		?= $(PREFIX)/sbin
MANDIR		?= $(PREFIX)/share/man/man8
GROUP		?= root

CXX		= g++
CXXFLAGS	= -O0 -g3 -ggdb
CPPFLAGS	= -DETCDIR=\"$(CONFDIR)\" -DPREFIX=\"$(PREFIX)\" 
LDFLAGS		=
OBJS		= slayerd.o process.o config.o util.o
LIBS		=

ifeq ($(shell uname),FreeBSD)
CPPFLAGS	+= -I/usr/local/include
LDFLAGS		+= -L/usr/local/lib
LIBS		+= -lkvm
GROUP		 = wheel
endif

ifeq ($(shell uname),SunOS)
CC		 = cc
CXX		 = CC
CFLAGS		 = -m64 -xO4 -g
CXXFLAGS	 = -m64 -xO4 -g -library=stlport4
LDFLAGS		+= -library=stlport4
CPPFLAGS	+= -I/opt/TSboost-1.38/include
LDFLAGS		+= -L/opt/TSboost-1.38/lib/amd64 -R/opt/TSboost-1.38/lib/amd64
OBJS		+= daemon.o
BOOST_TAG	 = -mt
endif

all: slayerd

slayerd: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $(OBJS) -o $@ -lboost_filesystem$(BOOST_TAG) -lboost_system$(BOOST_TAG) $(LIBS)

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

.PHONY: clean install all
.SUFFICES: .cc .o
process.o: process.cc proc_linux.cc proc_solaris.cc proc_freebsd.cc
