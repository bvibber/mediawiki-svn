#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <pthread.h>

#include <deque>
#include <iostream>

#include <mysql.h>

#define BUF 1024*1024
#define QBUF 1024*1024*16
#define MAXDEPTH 100
#define MINDEPTH 30
#define NUMTHREADS 6

int main(int, char*[]);
void handle_stream(FILE *);
void dispatch(char *);
void async_dispatch(char *);
void * async_worker(void *);
void execute(MYSQL *, char *);
MYSQL *connect();
void usage();


pthread_cond_t canread = PTHREAD_COND_INITIALIZER;
pthread_cond_t canwrite = PTHREAD_COND_INITIALIZER;
pthread_mutex_t mutex = PTHREAD_MUTEX_INITIALIZER;

char *opt_username=NULL,*opt_password=NULL,*opt_hostname=NULL,*opt_database=NULL,*opt_charset=NULL;
int opt_port=0;

bool endofwork=false;
std::deque<char *> work_queue;

MYSQL *syncmysql;

int main (int argc, char **argv)
{
	pthread_t thr[NUMTHREADS];
	void * status;
	
	char c;
	static char *optiongroups[] = {"client","paramy",(char *)NULL};
	mysql_library_init(argc,argv,optiongroups);
	
	while ((c = getopt (argc,argv, "u:p:h:d:P:c:")) != -1) switch (c) {
		case 'u': opt_username=strdup(optarg); break;
		case 'p': opt_password=strdup(optarg); break;
		case 'h': opt_hostname=strdup(optarg); break;
		case 'd': opt_database=strdup(optarg); break;
		case 'c': opt_charset=strdup(optarg); break;
		case 'P': opt_port=atoi(optarg); break;
		default: usage();
	}
	
	for (int nthreads = NUMTHREADS; nthreads--; )
		pthread_create(&thr[nthreads], NULL, async_worker, NULL);

	syncmysql = connect();
	
	handle_stream(stdin);

	pthread_mutex_lock(&mutex);
	endofwork=true;
	pthread_cond_broadcast(&canread);
	pthread_mutex_unlock(&mutex);
	for (int nthreads = NUMTHREADS; nthreads--; )
		pthread_join(thr[nthreads], &status);
	return 0;
}

void handle_stream(FILE *in) {
	char *buffer=(char *)malloc(BUF);
	char *qbuf=(char *)malloc(BUF);
	char instring=0;
	char inescape=0;
	char atstart=1;
	size_t l;
	
	char *bpos, *qpos, c;
	
	qpos=qbuf;
	while(l=fread(buffer,1,BUF,in)) {
		bpos=buffer;
		while(l--) {
			c=*bpos++;
			
			/* skip starting whitespace */
			if (atstart && isspace(c))
				continue;
			else
				atstart=0;
			
			/* fill in query buffer */
			*qpos++=c;
			if(!instring && c==';') { 
				*qpos++=0; 
				dispatch(qbuf);
				qpos=qbuf; 
				atstart=1;
			}
			/* Starting quoted string */
			if(!instring && (c=='\'' || c=='"' || c=='`')) {
				instring=c;
				continue;
			}
			if (instring) {
				if (!inescape) {
					if(c=='\\') 
						inescape=1;
					else if (c==instring)
						instring=0;
				} else {
					inescape=0;
				}
			}
		}
	}
}

void dispatch(char *q) {
	if (!strncmp(q,"INSERT INTO",strlen("INSERT INTO")))
		async_dispatch(q);
	else
		execute(syncmysql,q);
}

void async_dispatch(char *q){
	pthread_mutex_lock(&mutex);
	if (work_queue.size()>MAXDEPTH)
		pthread_cond_wait(&canwrite, &mutex);
		
	work_queue.push_back(strdup(q));
	pthread_cond_signal(&canread);
	pthread_mutex_unlock(&mutex);
}

void * async_worker(void *)
{
	MYSQL *mysql = connect();
	
	for(;;) {
		pthread_mutex_lock(&mutex);
		while (work_queue.empty()) {
			if (endofwork) {
				pthread_mutex_unlock(&mutex);
				pthread_exit((void *)0);
			}
			pthread_cond_wait(&canread, &mutex);
		}
		
		if(work_queue.size()<MINDEPTH)
			pthread_cond_signal(&canwrite);
			
		char *query=work_queue.front();
		work_queue.pop_front();
		pthread_mutex_unlock(&mutex);
		
		/* WORK WORK WORK */
		execute(mysql,query);
		
		
		free(query);
	}
}

MYSQL *connect() {
	MYSQL *my = mysql_init(NULL);
	char buffer[1024];
	mysql_options(my,MYSQL_READ_DEFAULT_GROUP,"paramy");
	if (!mysql_real_connect(my,opt_hostname,opt_username,opt_password,opt_database,opt_port,NULL,0)) {
		fprintf(stderr, "Failed to connect to database: Error: %s\n",mysql_error(my));
		exit(-1);
	}
	if (opt_charset) {
		snprintf(buffer,1000,"/*!40101 SET CHARACTER_SET_CLIENT=%s, @saved_cs_client='%s' */",opt_charset, opt_charset);
		execute(my,buffer);
	}
	execute(my,"/*!40103 SET TIME_ZONE='+00:00' */");
	execute(my,"/*!40014 SET UNIQUE_CHECKS=0 */");
	execute(my,"/*!40014 SET FOREIGN_KEY_CHECKS=0 */");
	execute(my,"/*!40101 SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */");
	execute(my,"/*!40111 SET SQL_NOTES=0 */");
	
	return(my);
}

void execute(MYSQL * mysql,char *q) {
	if(mysql_query(mysql,q)) {
		fprintf(stderr,"Error occured: %s\nQuery:%s\n\n",mysql_error(mysql),q);
		exit(-1);
	}
}

void usage() {
	printf("Parallel MySQL dump loader\n"\
		"Usage: paramy [-u username] [-p password] [-h host] [-P port] [-d database] [-c charset]\n\n");
	exit(0);
}