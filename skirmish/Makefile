include config.mk
include rules.mk

SRCS =			\
	terminal.cc	\
	skirmish.cc	\
	db.cc		\
	$(DB_SRCS)

OBJS	= $(SRCS:.cc=.o)
VPATH	= bin

all: skirmish

skirmish: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $^ -o bin/$@ $(LIBS)

clean:
	rm -f $(OBJS) skirmish

distclean: clean

depend:
	@rm -f .depends
	@echo making dependencies...
	@$(CXX) -M -MG $(CXXFLAGS) $(INCLUDES) $(SRCS) > .depends

-include .depends
