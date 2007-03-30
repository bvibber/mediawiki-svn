include config.mk
include rules.mk

SRCS =			\
	terminal.cc	\
	skirmish.cc	\
	db.cc		\
	help.cc		\
	$(DB_SRCS)

OBJS	= $(SRCS:.cc=.o) help_text.o
VPATH	= bin

all: skirmish

skirmish: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $(foreach f,$(OBJS),bin/$(f)) -o bin/$@ $(LIBS)

mkhelp: mkhelp.cc
	$(CXX) $(CXXFLAGS) $(LDFLAGS) mkhelp.cc -o bin/mkhelp

help_text.o: mkhelp
	@bin/mkhelp
	$(CXX) $(CPPFLAGS) $(INCLUDES) $(CXXFLAGS) -c help_text.cc -o bin/help_text.o

clean:
	rm -f bin/* help_text.cc

distclean: clean

depend:
	@rm -f .depends
	@echo making dependencies...
	@$(CXX) -M -MG $(CXXFLAGS) $(INCLUDES) $(SRCS) > .depends

-include .depends
