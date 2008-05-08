CXX=		c++
CC=		cc
CPPFLAGS=	
CXXFLAGS=	-g -O2
CFLAGS=		-g -O2
LDFLAGS=	
LIBS=		-lsocket -lnsl

OBJECTS=	imgserv.o

imgserv: $(OBJECTS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $(OBJECTS) -o $@ $(LIBS)

.cxx.o:
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) -c $<

.SUFFIXES: .c .cxx .o

clean: 
	rm -f $(OBJECTS) imgserv
