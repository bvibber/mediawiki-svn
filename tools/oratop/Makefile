CXX=		CC
CFLAGS=		-xO3
CPPFLAGS=	-I$(ORACLE_HOME)/rdbms/public
LDFLAGS=	-R$(ORACLE_HOME)/lib -L$(ORACLE_HOME)/lib -locci -lclntsh -lcurses

oratop: oratop.o curswrap.o
	$(CXX) $(CFLAGS) oratop.o curswrap.o -o oratop $(LDFLAGS)
.cxx.o:
	$(CXX) $(CPPFLAGS) $(CFLAGS) -c $<
oratop.o: oratop.cxx curswrap.h
curswrap.o: curswrap.cxx curswrap.h
.SUFFIXES: .o .cxx
