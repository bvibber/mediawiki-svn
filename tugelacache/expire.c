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
    char *prefix = NULL;
    char buf[300];
    size_t prefixlen = 0;
    int ret;
    int port = 0;
    struct in_addr addr;
    struct sockaddr_in sin;
    item *it;
    time_t oldest = 0;
    time_t now;
    int c;
    int len;
    int s;
    int verbose = 0;

    now = time(NULL);
    inet_aton("127.0.0.1", &addr);
    while ((c = getopt(argc, argv, "vf:o:P:H:p:")) != -1) {
	switch (c) {
	case 'f':
	    dbfile = optarg;
	    break;
	case 'o':
	    oldest = now - (86400 * atoi(optarg));
	    break;
	case 'p':
	    prefix = optarg;
	    prefixlen = strlen(prefix);
	    break;
	case 'P':
	    port = atoi(optarg);
	    break;
	case 'H':
	    bzero(&addr, sizeof(addr));
	    if (!inet_aton(optarg, &addr)) {
		fprintf(stderr, "Illegal address: %s\n", optarg);
		return 1;
	    }
	    break;
	case 'v':
	    verbose = 1;
	    break;
	default:
	    usage();
	    exit(1);
	}
    }

    db_create(&dbp, NULL, 0);
#if DB_VERSION_MAJOR < 4
    if ((ret = dbp->open(dbp,
                         dbfile, NULL, DB_BTREE, DB_RDONLY,
                         0664)) != 0) {
        dbp->err(dbp, ret, "%s", dbfile);
        exit(1);
    }
#else
#if DB_VERSION_MINOR > 0
    if ((ret = dbp->open(dbp,
                         NULL, dbfile, NULL, DB_BTREE, DB_RDONLY,
                         0664)) != 0) {
        dbp->err(dbp, ret, "%s", dbfile);
        exit(1);
    }
#else
    if ((ret = dbp->open(dbp,
                         dbfile, NULL, DB_BTREE, DB_RDONLY,
                         0664)) != 0) {
        dbp->err(dbp, ret, "%s", dbfile);
        exit(1);
    }
#endif
#endif


    if ((ret = dbp->cursor(dbp, NULL, &dbcp, 0)) != 0) {
	dbp->err(dbp, ret, "DB->cursor");
	exit(1);
    }

    if (port != 0) {
	s = socket(PF_INET, SOCK_STREAM, 0);
	bzero(&sin, sizeof(sin));
	sin.sin_family = AF_INET;
	sin.sin_port = htons(port);
	sin.sin_addr = addr;
	if (connect(s, (struct sockaddr *) &sin, sizeof(sin)) < 0) {
	    printf("could not connect\r\n");
	    exit(1);
	}
    } else {
	s = 1;			/* standard output */
    }


    memset(&key, 0, sizeof(key));
    memset(&data, 0, sizeof(data));
    data.flags = DB_DBT_PARTIAL;
    data.doff = 0;
    data.dlen = PARTSIZE;

    while ((ret = dbcp->c_get(dbcp, &key, &data, DB_NEXT)) == 0) {
	it = data.data;
	if (prefix
	    && (key.size < prefixlen
		|| strncmp(prefix, key.data, prefixlen)))
	    continue;
	if ((it->exptime && it->exptime <= now) || it->time < oldest) {
	    len = snprintf((char *)&buf, sizeof(buf), "delete %.*s\r\n",
			   (int) key.size, (char *) key.data);
	    if (port && verbose)
		printf("%.*s : ", (int) key.size,
		       (char *) key.data);
	    write(s, buf, len);
	    if (port) {
		len = read(s, &buf, 128);
		if (verbose)
		    printf("%.*s", len, buf);
	    }
	}
    }
    if (ret != DB_NOTFOUND) {
	dbp->err(dbp, ret, "DBcursor->get");
	exit(1);
    }
    exit(0);
}

void usage()
{
    printf("-P port	0 (default) means print to stdout\n");
    printf("-H ip	host ip of cache daemon\n");
    printf("-v		verbose session information\n");
    printf("-f file	database file\n");
    printf("-p prefix	key prefix\n");
    printf
	("-o days     remove items older than specified (default: no limit)\n");

}
