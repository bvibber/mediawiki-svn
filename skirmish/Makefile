SRCS =			\
	linereader.cc	\
	skirmish.cc	\
	db.cc		\
	mysql.cc	\
	pgsql.cc	\
	ora.cc		\

OBJS =	$(SRCS:.cc=.o)

all:
	$(MAKE) -C orapp all
	$(MAKE) all

skirmish: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $(OBJS) -o $@ $(LIBS)

clean:
	$(MAKE) -C orapp clean
	rm -f $(OBJS) skirmish
distclean: clean
	$(MAKE) -C orapp distclean

include rules.mk

linereader.o: linereader.cc linereader.h
skirmish.o: skirmish.cc linereader.h db.h
db.o: db.cc db.h mysqldb.h pgsql.h
mysql.o: mysql.cc mysqldb.h
pgsql.o: pgsql.cc pgsql.h
ora.o: ora.cc ora.h
