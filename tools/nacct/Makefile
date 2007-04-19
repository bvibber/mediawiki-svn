CXX		= g++
CXXFLAGS	= -O2 -g3 -ggdb
LDFLAGS		=
SRCS		= nacct.cc
OBJS		= $(SRCS:.cc=.o)

nacct: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $^ -o $@

.cc.o:
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) -c $<

.SUFFICES: .cc .o
