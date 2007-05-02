include config.mk
include rules.mk

PREFIX?=	/usr/local
BINDIR?=	/usr/local/bin
INSTALL?=	install

SRCS =			\
	terminal.cc	\
	skirmish.cc	\
	db.cc		\
	help.cc		\
	$(DB_SRCS)

OBJS	= $(SRCS:.cc=.o) help_text.o
VPATH	= bin

all: skirmish

skirmish: $(foreach f,$(OBJS),bin/$(f))
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $(foreach f,$(OBJS),bin/$(f)) -o bin/$@ $(LIBS)

bin/mkhelp: mkhelp.cc
	$(CXX) $(CXXFLAGS) $(LDFLAGS) mkhelp.cc -o bin/mkhelp

help_text.cc: bin/mkhelp
	@bin/mkhelp

bin/help_text.o: bin/mkhelp help_text.cc
	$(CXX) $(CPPFLAGS) $(INCLUDES) $(CXXFLAGS) -c help_text.cc -o bin/help_text.o

install:
	$(INSTALL) -d -m 755 $(BINDIR)
	$(INSTALL) -o root -g bin -m 755 bin/skirmish $(BINDIR)

clean:
	rm -f bin/* help_text.cc

distclean: clean

depend:
	@rm -f .depends
	@echo making dependencies...
	@$(CXX) -M -MG $(CXXFLAGS) $(INCLUDES) $(SRCS) > .depends

-include .depends
