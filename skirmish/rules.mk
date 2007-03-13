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

ifeq ($(BUILD_ODBC),YES)
INCLUDES	+=
CPPFLAGS	+= $(shell odbc_config --cflags) -DSKIRMISH_ODBC
LIBS		+= $(shell odbc_config --libs)
DB_SRCS		+= odbc.cc
endif

ifeq ($(BUILD_MAXDB),YES)
INCLUDES	+= -I$(MAXDB_ROOT)/programs/sdk/sqldbc/incl
CPPFLAGS	+= -DSKIRMISH_MAXDB
LIBS		+= -L$(MAXDB_ROOT)/programs/lib -lSQLDBC
DB_SRCS		+= maxdb.cc
endif

ifeq ($(BUILD_SQLITE),YES)
INCLUDES	+= $(SQLITE_INCLUDES)
CPPFLAGS	+= -DSKIRMISH_SQLITE
LIBS		+= -lsqlite3
DB_SRCS		+= sqlite.cc
endif

.cc.o:
	$(CXX) $(CPPFLAGS) $(INCLUDES) $(CXXFLAGS) -c $<

.SUFFIXES: .cc .ow
