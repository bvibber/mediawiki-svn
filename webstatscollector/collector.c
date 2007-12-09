/*
 This is a daemon, that sits on (haha, hardcoded) port 3815,
 receives profiling events from squid log aggregators. 
 and places them into BerkeleyDB file. \o/

 Then it should rotate everything once an hour, 
 producing per-hourly aggregates for further filtering. 

 Author: Domas Mituzas ( http://dammit.lt/ )

 License: public domain (as if there's something to protect ;-)

 $Id: collector.c 12389 2006-01-04 11:21:01Z midom $
*/
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/wait.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <signal.h>
#include <poll.h>
#include <errno.h>
#include <fcntl.h>
#include <netinet/in.h>
#include <db4/db.h>
#include <pthread.h>
#include "collector.h"

void hup();
void alarmed();
void die();
void child();
void truncatedb();


DB * initEmptyDB();
void handleMessage(char *,ssize_t );
void handleConnection(int);
void produceDump();
void increaseStatistics(DB *, char *, struct wcstats * );

int needdump=0;

int main(int ac, char **av) {

	ssize_t l;
	char buf[2000];
	int r;


	/* Socket variables */
	int s, exp;
	u_int yes=1;
	int port;
	struct sockaddr_in me, them;
	socklen_t sl;
	
	struct pollfd fds[2];
	
	/*Initialization*/{
		port=3815;
		bzero(&me,sizeof(me));
		me.sin_family= AF_INET;
		me.sin_port=htons(port);
	
		s=socket(AF_INET,SOCK_DGRAM,0);
		bind(s,(struct sockaddr *)&me,sizeof(me));

		exp=socket(AF_INET,SOCK_STREAM,0);
		setsockopt(exp,SOL_SOCKET,SO_REUSEADDR,&yes,sizeof(yes));
		bind(exp,(struct sockaddr *)&me,sizeof(me));
		listen(exp,10);

		bzero(&fds,sizeof(fds));

		fcntl(s,F_SETFL,O_NONBLOCK);
		fcntl(exp,F_SETFL,O_NONBLOCK);

		fds[0].fd = s; fds[0].events |= POLLIN;
		fds[1].fd = exp, fds[1].events |= POLLIN;

		db=initEmptyDB();
		aggr=initEmptyDB();
		
		signal(SIGALRM,alarmed);
		signal(SIGHUP,hup);
		signal(SIGINT,die);
		signal(SIGTERM,die);
		signal(SIGCHLD,child);
		signal(SIGUSR1,truncatedb);
		
		/* Schedule the dumps */
		alarm(PERIOD-(time(NULL)%PERIOD));
	}
	/* Loop! loop! loop! */
	for(;;) {
		r=poll(fds,2,-1);
		
		if (needdump)
			produceDump();
		
		/* Process incoming UDP queue */
		while(( fds[0].revents & POLLIN ) && 
			((l=recvfrom(s,&buf,1500,0,NULL,NULL))!=-1)) {
				if (l==EAGAIN)
					break;
				handleMessage((char *)&buf,l);
			}
				
		/* Process incoming TCP queue - for testing data collection only */
		while((fds[1].revents & POLLIN ) && 
			((r=accept(exp,(struct sockaddr *)&them,&sl))!=-1)) {
				if (r==EWOULDBLOCK)
					break;
				handleConnection(r);
		}
	}
	return(0);
}

/* Decides what to do with incoming UDP message */
void handleMessage(char *buf,ssize_t l) {
	char *p,*pp;
	char project[128];
	char title[1024];
	char keytext[1200];
	int r;
	
	struct wcstats incoming;
	/* project count bytesize page */
	const char msgformat[]="%127s %llu %llu %1023[^\n]"; 
	
	buf[l]=0;
	pp=buf;
	
	while((p=strsep(&pp,"\r\n"))) {
		if (p[0]=='\0')
			continue;
		if (!strcmp("-truncate",p)) {
			truncatedb();
			return;
		}
		bzero(&incoming,sizeof(incoming));
		r=sscanf(p,msgformat,(char *)&project,
			&incoming.wc_count,
			&incoming.wc_bytes,
			(char *)&title);
		if (r<4)
			continue;
		snprintf(keytext,1499,"%s:%s",project,title);
		
		increaseStatistics(db,keytext,&incoming);
		increaseStatistics(aggr,project,&incoming);
	}
}

/* Create empty database object - creates file-system backed anonymous BDB handle */
DB * initEmptyDB() {
	/* Do note - this isn't using global 'db' object */
	DB *db;
	db_create(&db,NULL,0);
	db->open(db,NULL,".temp.db",NULL,DB_BTREE,DB_CREATE|DB_TRUNCATE,0);
	unlink(".temp.db");
	return db;	
}

/* Bump up statistics in specified DB for specified key */
void increaseStatistics(DB *db, char *keytext, struct wcstats *incoming ) {
	struct wcstats *old;
	DBT key,data;
	
	bzero(&key,sizeof(key));
	key.data=keytext;
	key.size=strlen(keytext);
	
	bzero(&data,sizeof(data));
		
	if (db->get(db,NULL,&key,&data,0)==0) {
		/* Update old stuff */
		old=data.data;
		old->wc_count   += incoming->wc_count;
		old->wc_bytes   += incoming->wc_bytes;
		db->put(db,NULL,&key,&data,0);	
	} else {
		/* Put in fresh data */
		data.data=incoming;
		data.size=sizeof(*incoming);
		db->put(db,NULL,&key,&data,0);
	}
}

void statsDumper(struct dumperjob * job) {
	DB *db;
	FILE *outfile;
	char filename[1024];
	char tfilename[1024];
	struct tm dumptime;
	db=job->db;
	gmtime_r(&job->time,&dumptime);
	snprintf(filename,1000,"%s-%04d%02d%02d-%02d%02d%02d",
		job->prefix, dumptime.tm_year+1900, dumptime.tm_mon+1, dumptime.tm_mday,
		dumptime.tm_hour, dumptime.tm_min, dumptime.tm_sec);
	
	snprintf(tfilename,1010,"%s.tmp",filename);
	outfile=fopen(tfilename,"a");
	if (outfile==NULL) {
		fprintf(stderr,"Problem opening file '%s': %d\n",tfilename,errno); die();
	}
	dumpData(outfile,db);
	fclose(outfile);
	rename(tfilename,filename);
	db->close(db,DB_NOSYNC);
}

/* The dumps stuff */
void produceDump() {
	/* Teh logicz:
		1) Swap with empty databasez - pretty much atomic, can be done in main loop
		2) Spawn background threads to:
			2.1) Dump out the data
			2.2) Destroy old databases
		3) Set the alarm to next PERIOD-thetime%PERIOD - this will position the alarm at next nice period.
	*/
	
	pthread_t dumper,aggrdumper;
	static struct dumperjob dumperJob, aggrDumperJob;
	DB *olddb,*oldaggr;
	time_t dumptime;
	
	time(&dumptime);
	
	olddb=db;
	oldaggr=aggr;
	
	db=initEmptyDB();
	aggr=initEmptyDB();
	
	dumperJob.prefix= PREFIX "pagecounts";
	dumperJob.db=olddb;
	dumperJob.time=dumptime;
	
	pthread_create(&dumper,NULL,(void *)statsDumper, (void *)&dumperJob);
	
	aggrDumperJob.prefix= PREFIX "projectcounts";
	aggrDumperJob.db = oldaggr;
	aggrDumperJob.time=dumptime;

	pthread_create(&aggrdumper,NULL,(void *)statsDumper,(void *)&aggrDumperJob);
	
	alarm(PERIOD-(dumptime%PERIOD)+1);
}

/* TCP connection handling logic - unsafe dump of data in DB - should be avoided at large datasets */
void handleConnection(int c) {
	FILE *tmp;
	char buf[1024];
	int r;
	
	shutdown(c,SHUT_RD);
	
	tmp=tmpfile();
	dumpData(tmp,db);
	rewind(tmp);
	if (fork()) {
		fclose(tmp);
		close(c);
	} else {
		while(!feof(tmp)) {
			r=fread((char *)&buf,1,1024,tmp);
			write(c,(char *)&buf,r);
		}
		close(c);
		fclose(tmp);
		exit(0);
	}
}

/* Event handling - most of them are not used or should not be used due to reentry possibilities */

void alarmed() {
	needdump=1;
}

void hup() {
	/* Do nothing */
}

void die() {
	exit(0);
}

void child() {
	int status;
	wait(&status);
}

void truncatedb() {
	unsigned int count;
	db->truncate(db,NULL,&count,0);
}