/* $Id$
 
 Simple database cleanup script, gives out a feed of delete commands to standard output,
 suitable for redirection to tugela cache.

 TODO: Make it work with network. More run-time tuning params.

*/
#include <sys/types.h>
#include <sys/time.h>
#include <sys/socket.h>
#include <pwd.h>
#include <fcntl.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <netinet/in.h>
#include <netinet/tcp.h>
#include <arpa/inet.h>
#include <errno.h>
#include <time.h>
#include <event.h>
#include <db.h>
#include "dbcached.h"

#define PARTSIZE 40;

void usage();

int main(int argc, char **argv)
{
    DB *dbp;
    DBC *dbcp;
    DBT key, data;
    char *dbfile = DBFILE;
    int ret;
    item *it;
    time_t now;
    int c;

    while ((c = getopt(argc, argv, "f:h")) != -1) {
	switch (c) {
	case 'f':
	    dbfile = optarg;
	    break;
	case 'h':
	    usage();
	default:
	    usage();
	}
    }

    now = time(NULL);
    db_create(&dbp, NULL, 0);
    if ((ret = dbp->open(dbp, NULL, dbfile, NULL,
			 DB_BTREE, DB_RDONLY, 0644)) != 0) {
	dbp->err(dbp, ret, "%s", dbfile);
	exit(1);
    }

    if ((ret = dbp->cursor(dbp, NULL, &dbcp, 0)) != 0) {
	dbp->err(dbp, ret, "DB->cursor");
	exit(1);
    }

    memset(&key, 0, sizeof(key));
    memset(&data, 0, sizeof(data));
    data.flags = DB_DBT_PARTIAL;
    data.doff = 0;
    data.dlen = PARTSIZE;

    while ((ret = dbcp->c_get(dbcp, &key, &data, DB_NEXT)) == 0) {
	it = data.data;
	if (it->exptime && it->exptime <= now)
	    printf("delete %.*s\n",
		   (int) key.size, (char *) key.data, it->exptime);
    }
    if (ret != DB_NOTFOUND) {
	dbp->err(dbp, ret, "DBcursor->get");
	exit(1);
    }

}

void usage()
{
    printf("-f	database file\n");
}
