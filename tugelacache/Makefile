# $Id$
#
# It is not a real makefile, but sometimes it does work, and it is a place
# where you should change one thing or another :)

# Default path for a database
DBFILE='"/var/db/dbcache"'

# change it into your installed db3/db4{0,1,2} library name
DBLIB=db41

# FreeBSD file locations
LIBS=-l${DBLIB} -levent
DBLIBPATH=/usr/local/lib/${DBLIB}/
DBINCPATH=/usr/local/include/${DBLIB}/

LDFLAGS= -L/usr/local/lib/ -L${DBLIBPATH}
CFLAGS = -O -pipe -I/usr/local/include -I${DBINCPATH} -DDBFILE=${DBFILE}
tugela: dbcached.o items.o assoc.o
	cc -o tugela ${LDFLAGS} dbcached.o items.o assoc.o ${LIBS}

clean:
	rm -f -- *.o tugela *~ *.bak *core
