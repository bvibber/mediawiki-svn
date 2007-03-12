include rules.mk

SRCS =			\
	linereader.cc	\
	skirmish.cc	\
	db.cc		\
	$(DB_SRCS)

OBJS =	$(SRCS:.cc=.o)

all:
	for d in $(SUBDIRS); do		\
		$(MAKE) -I.. -C $$d all	;\
	done
	$(MAKE) skirmish

skirmish: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $(OBJS) -o $@ $(LIBS)

clean:
	for d in $(SUBDIRS); do			\
		$(MAKE) -I.. -C $$d clean	;\
	done
	rm -f $(OBJS) skirmish

distclean: clean
	for d in $(SUBDIRS); do			\
		$(MAKE) -I.. -C $$d distclean	;\
	done

linereader.o: linereader.cc linereader.h
skirmish.o: skirmish.cc linereader.h db.h
db.o: db.cc db.h mysqldb.h pgsql.h
mysql.o: mysql.cc mysqldb.h
pgsql.o: pgsql.cc pgsql.h
ora.o: ora.cc ora.h
