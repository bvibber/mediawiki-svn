CXX		= g++
CXXFLAGS	= -O2 -g3 -ggdb
LDFLAGS		=
SRCS		= slayerd.cc
OBJS		= $(SRCS:.cc=.o)

slayerd: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $^ -o $@ -lboost_filesystem

install: slayerd
	install -c -s -o root -g root -m 755 slayerd /usr/local/sbin

.cc.o:
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) -c $<

.SUFFICES: .cc .o
