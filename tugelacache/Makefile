# $Id$
#
# It is not a real makefile, but sometimes it does work, and it is a place
# where you should change one thing or another :)

DBLIB=db41
LIBS=-l${DBLIB} -levent
DBLIBPATH=/usr/local/lib/${DBLIB}/
DBINCPATH=/usr/local/include/${DBLIB}/
DBFILE='"/var/db/dbcache"'

LDFLAGS= -L/usr/local/lib/ -L${DBLIBPATH}
CFLAGS = -O -pipe -I/usr/local/include -I${DBINCPATH} -DDBFILE=${DBFILE}
tugela: dbcached.o items.o assoc.o
	cc -o tugela ${LDFLAGS} dbcached.o items.o assoc.o ${LIBS}

clean:
	rm -f -- *.o tugela *~ *.bak *core
