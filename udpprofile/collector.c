/*
 This is a daemon, that sits on (haha, hardcoded) port 3811,
 receives profiling events from mediawiki ProfilerSimpleUDP,
 and places them into BerkeleyDB file. \o/

 Author: Domas Mituzas ( http://dammit.lt/ )

 License: public domain (as if there's something to protect ;-)

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
#include "collector.h"

void hup();
void die();
void child();

void handleMessage(char *,ssize_t );
void handleConnection(int);

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
		port=3811;
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

		db_create(&db,NULL,0);
		db->open(db,NULL,"stats.db",NULL,DB_BTREE,DB_CREATE,0);
		
		signal(SIGHUP,hup);
		signal(SIGINT,die);
		signal(SIGTERM,die);
		signal(SIGCHLD,child);
		daemon(1,0);
	}
	/* Loop! loop! loop! */
	for(;;) {
		r=poll(fds,2,-1);
		
		/* Process incoming UDP queue */
		while(( fds[0].revents & POLLIN ) && 
			((l=recvfrom(s,&buf,1500,0,NULL,NULL))!=-1)) {
				if (l==EAGAIN)
					break;
				handleMessage((char *)&buf,l);
			}
				
		/* Process incoming TCP queue */
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
	char hostname[128];
	char dbname[128];
	char task[1024];
	char keytext[1500];
	int r;
	
	struct pfstats incoming,*old;
	/* db host count cpu cpusq real realsq eventdescription */
	const char msgformat[]="%127s %127s %ld %lf %lf %lf %lf %1023[^\n]"; 
	

	DBT key,data;

	buf[l]=0;	
	pp=buf;
	
	while((p=strsep(&pp,"\r\n"))) {
		if (p[0]=='\0')
			continue;
		bzero(&incoming,sizeof(incoming));
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
			/* Update old stuff */
			old=data.data;
			old->pf_count   += incoming.pf_count;
			old->pf_cpu     += incoming.pf_cpu;
			old->pf_cpu_sq  += incoming.pf_cpu_sq;
			old->pf_real    += incoming.pf_real;
			old->pf_real_sq += incoming.pf_real_sq;
			db->put(db,NULL,&key,&data,0);	
		} else {
			/* Put in fresh data */
			data.data=&incoming;
			data.size=sizeof(incoming);
			db->put(db,NULL,&key,&data,0);
		}
	}	
	
}

void handleConnection(int c) {
	FILE *tmp;
	char buf[1024];
	int r;
	
	shutdown(c,SHUT_RD);
	
	tmp=tmpfile();
	dumpData(tmp);
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

/* Event handling */
void hup() {
	db->sync(db,0);
}

void die() {
	db->sync(db,0);
	exit(0);
}

void child() {
	int status;
	wait(&status);
}
