#include <string.h>
#include <stdio.h>
#include <stdbool.h>

/* 

Modified on Jun 4, 2010 by hcatlin to 
now process en.m.wikipedia.org requests into
the mobile project, with the title being the
language code.

For instance, it might output "en.mw 1 1213 en"

This way, we aren't tracking every page title in
mobile.

*/

/*

#!/usr/bin/python

import re
import sys

dupes = re.compile('^(145\.97\.39\.|66\.230\.200\.|211\.115\.107\.|91\.198\.174\.)')
urlre = re.compile('^http://([^\.]+)\.([^\.]+).org/wiki/([^?]+)')

projects={"wikipedia":"","wiktionary":".d","wikinews":".n","wikimedia":".m","wikibooks":".b","wikisource":".s","mediawiki":".w","wikiversity":".v","wikiquote":".q" }

for line in sys.stdin:
	ip,undef,bytes,undef,url=line.split()[4:9]
	if dupes.match(ip): continue
	stuff=urlre.match(url)
	if stuff == None: continue
	language,project,title = stuff.groups()
	if project=="wikimedia" and language not in ["commons","meta","incubator","species"]: continue
	try: print language + projects[project] + " 1 " + bytes + " "  + title
	except: continue

*/

#define LINESIZE 4096
char *_sep, *_lasttok, *_firsttok;
#define TOKENIZE(x,y) _lasttok=NULL; _sep=y; _firsttok=strtok_r(x,y,&_lasttok);
#define FIELD strtok_r(NULL,_sep,&_lasttok)
#define TAIL _lasttok
#define HEAD _firsttok

char *wmwhitelist[] = {"commons","meta","incubator","species","strategy", "outreach", "usability", "quality"};
bool check_wikimedia(char *language) {
	char **p=wmwhitelist;
	for(;*p;p++) {
		if(!strcmp(*p,language))
			return true;
	}
	return false;
}

/* IP addresses from which duplicate requests originate */

char *dupes[] = {"145.97.39.","66.230.200.",
		"208.80.152.","208.80.153.",
		"208.80.154.","208.80.155.",
		"211.115.107.","91.198.174.",
		NULL};

bool check_ip(char *ip) {
	char **prefix=dupes;
	for (;*prefix;prefix++) {
		if(!strncmp(*prefix,ip,strlen(*prefix)))
			return false;
	}
	return true;
}

const struct project {
	char *full;
	char *suffix;
	bool (*filter)(char *);
} projects[] = {
		{"wikipedia","",NULL},
		{"wiktionary",".d",NULL},
		{"wikinews",".n",NULL},
		{"wikimedia",".m",check_wikimedia},
		{"wikibooks",".b",NULL},
		{"wikisource",".s",NULL},
		{"mediawiki",".w",NULL},
		{"wikiversity",".v",NULL},
		{"wikiquote",".q",NULL},
		{"m.wikipedia", ".mw", NULL},
		NULL
	}, *project;

struct info {
	char *ip;
	char *size;
	char *language;
	char *project;
	char *title;
	char *suffix;
} info;

bool parse_url(char *url, struct info *in) {
	if (!url)
		return false;
	char *host, *lang, *project, *dir;

	TOKENIZE(url,"/"); /* http: */
	host=FIELD;
	dir=FIELD;
	if (!dir)
		return false;
	if (strcmp(dir,"wiki"))
		return false; /* no /wiki/ part :( */
	in->title=TAIL;
	TOKENIZE(in->title,"?#");

	TOKENIZE(host,".");
	in->language=HEAD;
	in->project=FIELD;

	if(!strcmp(in->project,"m")) {
    	in->project = "m.wikipedia";
    	in->title = in->language;
    	return true;
	} else {
  	if(strcmp(TAIL,"org"))
  		return false;
  	if (in->language && in->project)
  		return true;
  	else
  		return false;
  }
}

bool check_project(struct info *in) {
	const struct project *pr=projects;
	for(;pr->full;pr++) {
		if(!strcmp(in->project,pr->full)) {
			in->suffix=pr->suffix;
			/* Project found, check if filter needed */
			if (pr->filter)
				return pr->filter(in->language);
			else
				return true;
		}
	}
	return false;
}

int main(int ac, char **av) {
	char line[LINESIZE];

	setuid(65534);
	setgid(65534);
	chroot("/tmp")

	char *undef,*ip,*url, *size;
	while(fgets(line,LINESIZE-1,stdin)) {
		bzero(&info,sizeof(info));
		/* Tokenize the log line */
		TOKENIZE(line," "); /* server */ 
				FIELD; /* id? */
				FIELD; /* timestamp */
				FIELD; /* ??? */
		info.ip=	FIELD; /* IP address! */
				FIELD; /* status */
		info.size=      FIELD; /* object size */
				FIELD;
		url=	    FIELD;
		if (!parse_url(url,&info))
			continue;
		if (!check_ip(info.ip))
			continue;
		if (!check_project(&info))
			continue;
		printf("%s%s 1 %s %s\n",info.language, info.suffix, info.size, info.title);
	}
}

