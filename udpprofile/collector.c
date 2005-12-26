#include <sys/types.h>
#include <sys/socket.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <signal.h>
#include <netinet/in.h>
#include <db4/db.h>

void hup();
void die();

DB *db;

int main(int ac, char **av) {

	ssize_t l;
	char buf[2000];

	char *p,*pp;
	char hostname[128];
	char dbname[128];
	char task[1024];
	char keytext[1500];
	int r;


	DBT key,data;

	/* Socket variables */
	int s;
	struct sockaddr_in me;
	
	/* Stats variables, not that generic, are they? */
	struct pfstats {
		unsigned long pf_count;
		/* CPU time of event */
		double pf_cpu;
		double pf_cpu_sq;
		double pf_real;
		double pf_real_sq;
	} incoming,*old;
	/* db host count cpu cpusq real realsq eventdescription */
	const char msgformat[]="%127s %127s %ld %lf %lf %lf %lf %1023[^\n]"; 
	
	{
		/* initialization */
		bzero(&me,sizeof(me));
		me.sin_family= AF_INET;
		me.sin_port=htons(3811);
	
		s=socket(AF_INET,SOCK_DGRAM,0);
		bind(s,(struct sockaddr *)&me,sizeof(me));

		db_create(&db,NULL,0);
		db->open(db,NULL,"stats.db",NULL,DB_BTREE,DB_CREATE,0);


		bzero(&incoming,sizeof(incoming));

		signal(SIGHUP,hup);
		signal(SIGINT,die);
		signal(SIGTERM,die);
		daemon(1,0);
	}
	while((l=recvfrom(s,&buf,1500,0,NULL,NULL))) {
		buf[l]=0;	
		pp=buf;
		while((p=strsep(&pp,"\r\n"))) {
			if (p[0]=='\0')
				continue;
			r=sscanf(p,msgformat,(char *)&dbname,(char *)&hostname,
				&incoming.pf_count,&incoming.pf_cpu,&incoming.pf_cpu_sq,
				&incoming.pf_real,&incoming.pf_real_sq, (char *)&task);
			snprintf(keytext,1499,"%s:%s:%s",dbname,hostname,task);

			bzero(&key,sizeof(key));
			bzero(&data,sizeof(data));
			key.data=keytext;
			key.size=strlen(keytext);

			/* Add new values if exists, put in fresh structure if not */
			if (db->get(db,NULL,&key,&data,0)==0) {
				old=data.data;
				old->pf_count   += incoming.pf_count;
				old->pf_cpu     += incoming.pf_cpu;
				old->pf_cpu_sq  += incoming.pf_cpu_sq;
				old->pf_real    += incoming.pf_real;
				old->pf_real_sq += incoming.pf_real_sq;
				db->put(db,NULL,&key,&data,0);	
			} else {
				data.data=&incoming;
				data.size=sizeof(incoming);
				db->put(db,NULL,&key,&data,0);
			}
		}
	}
	return(0);
}

void hup() {
	db->sync(db,0);
}

void die() {
	db->sync(db,0);
	exit(0);
}
