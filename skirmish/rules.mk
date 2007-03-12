ifeq ($(BUILD_MYSQL),YES)
INCLUDES	+= $(shell mysql_config --include)
LIBS		+= $(shell mysql_config --libs)
CPPFLAGS	+= -DSKIRMISH_MYSQL
DB_SRCS		+= mysql.cc
endif

ifeq ($(BUILD_ORACLE),YES)
INCLUDES	+= -I$(ORACLE_HOME)/rdbms/public
LIBS		+= -L$(ORACLE_HOME)/lib -lclntsh 
SUBDIRS		+= orapp
CPPFLAGS	+= -DSKIRMISH_ORACLE
DB_SRCS		+= ora.cc
endif

ifeq ($(BUILD_PG),YES)
INCLUDES	+= $(PG_INCLUDES)
LIBS		+= $(PG_LIBS) -lpq
CPPFLAGS	+= -DSKIRMISH_POSTGRES
DB_SRCS		+= pgsql.cc
endif

.cc.o:
	$(CXX) $(CPPFLAGS) $(INCLUDES) $(CXXFLAGS) -c $<

.SUFFIXES: .cc .ow
