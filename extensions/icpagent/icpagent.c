/* ICP Reply agent */
/* $Id$ */
/* vim: tabstop=8 number
   */
#include <sys/types.h>
#include <sys/socket.h>
#include "queue.h"
#include <sys/time.h>
#include <netinet/in.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <fcntl.h>

struct icpheader {
    unsigned char opcode;	/* opcode */
    unsigned char version;	/* version number */
    unsigned short length;	/* total length (bytes) */
    u_int32_t reqnum;		/* req number (req'd for UDP) */
    u_int32_t flags;
    u_int32_t pad;
    u_int32_t shostid;		/* sender host id */
};

struct {
	time_t loadcheck;
	time_t idlecheck;
	time_t swapcheck;
	int swap;
	int load;
	int idle;
} stats;


typedef enum {
    ICP_INVALID,
    ICP_QUERY,
    ICP_HIT,
    ICP_MISS,
    ICP_ERR,
    ICP_SEND,
    ICP_SENDA,
    ICP_DATABEG,
    ICP_DATA,
    ICP_DATAEND,
    ICP_SECHO,
    ICP_DECHO,
    ICP_NOTIFY,
    ICP_INVALIDATE,
    ICP_DELETE,
    ICP_UNUSED15,
    ICP_UNUSED16,
    ICP_UNUSED17,
    ICP_UNUSED18,
    ICP_UNUSED19,
    ICP_UNUSED20,
    ICP_MISS_NOFETCH,
    ICP_DENIED,
    ICP_HIT_OBJ,
    ICP_END
} icp_opcode;

typedef enum {
    AGENT_REPLY,
    AGENT_SLEEP,
    AGENT_TESTLOAD,
    AGENT_TESTIDLE,
    AGENT_TESTSWAP,
    AGENT_END
} agent_opcode;

int s;				/* Global UDP socket */

TAILQ_HEAD(tailhead, entry) head = TAILQ_HEAD_INITIALIZER(head);
struct tailhead *headp;
struct entry {
    unsigned char operation;
    unsigned char opcode;
    struct sockaddr_in them;
    char *url;
    unsigned long reqnum;
    struct timeval tv;
    TAILQ_ENTRY(entry) entries;
} *np;

void freeentry(struct entry *);
void sendreply(struct entry *);
void insertentry(struct entry *);

void freeentry(struct entry *e)
{
    if (e) {
	if (e->url)
	    free(e->url);
	free(e);
    }
}

void sendreply(struct entry *e)
{
    char msgbuf[16384];
    int slen, length;
    struct icpheader *header;

    length = sizeof(struct icpheader) + strlen(e->url);
    header = (struct icpheader *) msgbuf;
    bzero(header, sizeof(struct icpheader));
    header->opcode = e->opcode;
    header->length = htons(length);
    header->version = 2;
    header->reqnum = htonl(e->reqnum);
    strncpy(msgbuf + sizeof(struct icpheader), e->url, 2048);
    slen =
	sendto(s, msgbuf, length, 0, (struct sockaddr *) &e->them,
	       sizeof(e->them));

}

void queuereply(struct sockaddr_in them, unsigned char opcode, char *url,
		unsigned long reqnum, int delay)
{
    /* This function takes requestor IP address, 
       operation code, optional url,reqnum and delay 
       in miliseconds and queues an entry or  
       passes it for immediate processing */
    if (delay==-1) return;
    struct entry *e;

    e = malloc(sizeof(struct entry));
    if (url)
	e->url = strdup(url);
    e->opcode = opcode;
    memcpy(&e->them, &them, sizeof(them));
    e->reqnum = reqnum;
    if (delay == 0) {
	sendreply(e);
	freeentry(e);
	return;
    } else {
	gettimeofday(&e->tv, NULL);
	e->tv.tv_sec += delay / 1000;
	e->tv.tv_usec += (delay % 1000) * 1000;
	insertentry(e);
    }
}

void insertentry(struct entry *e)
{
    struct timeval tv;

    TAILQ_FOREACH_REVERSE(np, &head, tailhead, entries) {
	if (e->tv.tv_sec < np->tv.tv_sec)
	    continue;
	if ((e->tv.tv_sec == np->tv.tv_sec) &&
	    (e->tv.tv_usec <= np->tv.tv_sec))
	    continue;
	TAILQ_INSERT_AFTER(&head, np, e, entries);
	return;
    }
    TAILQ_INSERT_HEAD(&head, e, entries);
}

void processqueue()
{
    struct timeval tv;
    struct entry *e = NULL;

    gettimeofday(&tv, NULL);
    TAILQ_FOREACH(np, &head, entries) {
	if (e) {
	    TAILQ_REMOVE(&head, e, entries);
	    freeentry(e);
	}
	if (np->tv.tv_sec > tv.tv_sec)
	    return;
	if (np->tv.tv_sec == tv.tv_sec && np->tv.tv_usec > tv.tv_usec)
	    return;
	e = np;
	if (e->operation == AGENT_END)
	    exit(0);
	else if (e->operation == AGENT_REPLY)
	    sendreply(e);
	else if (e->operation == AGENT_SLEEP)
	    sleep(e->reqnum);

    }
    if (e) {
	TAILQ_REMOVE(&head, e, entries);
	freeentry(e);
    }

}

main(int ac, char **av)
{
    int rsize, rlen;
    struct sockaddr_in me, them;
    char msgbuf[16384];
    struct icpheader header;
    char *url;
    struct timeval wait;
    fd_set readfds;
    int port=3130;
    int remotemanage=0;
    int c;
    icp_opcode opcode=ICP_HIT;

    while ((c = getopt(ac,av,"mp:")) != -1) {
        switch (c) {
            case 'p':
                port=atoi(optarg);
                break;
            case 'm':
                remotemanage=1;
                break;
            default:
                fprintf(stderr, "Usage: icpagent [-p port] -m\n");
                exit(-1);
        }
    }
    TAILQ_INIT(&head);

    s = socket(PF_INET, SOCK_DGRAM, 0);
    bzero(&me, sizeof(me));
    me.sin_family = AF_INET;
    me.sin_port = htons(port);

    if (bind(s, (struct sockaddr *) &me, sizeof(me)) < 0) {
	printf("Unable to bind a socket");
	exit(-1);
    }
    for (;;) {
	bzero(&them, sizeof(them));
	rsize = sizeof(them);
	processqueue();
	FD_ZERO(&readfds);
	FD_SET(s,&readfds);
        wait.tv_sec = 0;
        wait.tv_usec = 1000;
	select(s+1,&readfds,NULL,NULL,(TAILQ_EMPTY(&head)?NULL:&wait));
	if (FD_ISSET(s,&readfds)) {
	rlen =
	    recvfrom(s, msgbuf, sizeof(msgbuf), 0,
			 (struct sockaddr *) &them, &rsize);
	    memcpy(&header, msgbuf, sizeof(header));
	    header.length = ntohs(header.length);
	    header.reqnum = ntohl(header.reqnum);
	    header.flags = ntohl(header.flags);
	    header.pad = ntohl(header.pad);
	    url = msgbuf + sizeof(header) + 4;
            msgbuf[rlen]='\0';
	    if (rlen != header.length)
		continue;
            if (remotemanage && !strcmp(url,"agent://enable"))
                opcode=ICP_HIT;
            else if (remotemanage && !strcmp(url,"agent://disable"))
                opcode=ICP_MISS;
            queuereply(them, opcode, url, header.reqnum, delay());
	}
    }
}
