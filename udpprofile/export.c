/*
 Poor man's XML exporter. :)

 Author: Domas Mituzas ( http://dammit.lt/ )

 License: public domain (as if there's something to protect ;-)

*/
#include <sys/types.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <netinet/in.h>
#include <db4/db.h>

int main(int ac, char **av) {
	DB *db;
	DBT key,data;
	DBC *c;
	
	char *p, oldhost[128]="",olddb[128]="",*pp;
	int indb=0,inhost=0;

	/* Stats variables, not that generic, are they? */
	struct pfstats {
		unsigned long pf_count;
		/* CPU time of event */
		double pf_cpu;
		double pf_cpu_sq;
		double pf_real;
		double pf_real_sq;
	} *entry;

	bzero(&key,sizeof(key));
	bzero(&data,sizeof(data));
	
	db_create(&db,NULL,0);
	db->open(db,NULL,"stats.db",NULL,DB_BTREE,0,0);
	db->cursor(db,NULL,&c,0);
	printf("<pfdump>\n");
	while(c->c_get(c, &key, &data, DB_NEXT )==0) {
		entry=data.data;
		p=key.data;
		/* Get DB */	
		pp=strsep(&p,":");
		if (strcmp(pp,olddb)) {
			if (indb) {
				printf("</db>");
				inhost=0;
			}
			printf("<db name=\"%s\">\n",pp);
			strcpy(olddb,pp);
			indb++;
		}
		/* Get Host/Context */
		pp=strsep(&p,":");
		if (strcmp(pp,oldhost)) {
			if (inhost)
				printf("</host>\n");
			printf("<host name=\"%s\">\n",pp);
			strcpy(oldhost,pp);
			inhost++;
		}
		/* Get EVENT */
		printf("<event>\n" \
				"<eventname><![CDATA[%.*s]]></eventname>\n" \
				"<stats count=\"%lu\">\n" \
				"<cputime total=\"%lf\" totalsq=\"%lf\" />\n" \
				"<realtime total=\"%lf\" totalsq=\"%lf\" />\n" \
				"</stats></event>\n",
				key.size - ((void *)p-(void *)key.data),p,
				entry->pf_count, entry->pf_cpu, entry->pf_cpu_sq,
				entry->pf_real, entry->pf_real_sq);

	}
	printf("</host>\n</db>\n</pfdump>\n");
	return 0;
}

