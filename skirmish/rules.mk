CXX		= icpc
CXXFLAGS	= -g3 -w2 -wd383,304,981,444
CPPFLAGS	=
LIBS		= -lreadline -lcurses

MYSQL_INCLUDES	= $(shell mysql_config --include)
MYSQL_LIBS	= $(shell mysql_config --libs)

PG_INCLUDES	=
PG_LIBS		= -lpq

ORA_INCLUDES	= -I$(ORACLE_HOME)/rdbms/public
ORA_LIBS	= -L$(ORACLE_HOME)/lib -lclntsh -Lorapp -lorapp

INCLUDES	= $(MYSQL_INCLUDES) $(PG_INCLUDES) $(ORA_INCLUDES) -Iorapp
LIBS		+= $(MYSQL_LIBS) $(PG_LIBS) $(ORA_LIBS)

.cc.o:
	$(CXX) $(CPPFLAGS) $(INCLUDES) $(CXXFLAGS) -c $<

.SUFFIXES: .cc .o

