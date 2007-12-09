/*
 Poor man's XML exporter. :)

 Author: Domas Mituzas ( http://dammit.lt/ )

 License: public domain (as if there's something to protect ;-)

 $Id: export.c 12389 2006-01-04 11:21:01Z midom $

*/
#include <sys/types.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <netinet/in.h>
#include <db4/db.h>
#include "collector.h"

void dumpData(FILE *fd, DB *db) {
	DBT key,data;
	DBC *c;
	
	char *p,*project,*page;

	struct wcstats *entry;

	bzero(&key,sizeof(key));
	bzero(&data,sizeof(data));
	
	db->cursor(db,NULL,&c,0);
	while(c->c_get(c, &key, &data, DB_NEXT )==0) {
		entry=data.data;
		p=key.data;
		/* project points to project! */	
		project=strsep(&p,":");
		
		/* Can just use p afterwards, but properly named variable sometimes helps */
		page=p;
		
		/* Get EVENT */
		fprintf(fd,"%s %s %llu %llu\n",
				project,(page?page:"-"),
				entry->wc_count, entry->wc_bytes);
	}
	c->c_close(c);
}

