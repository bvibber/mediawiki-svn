/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wconfig: configuration.
 */

#include <sys/types.h>
#include <sys/socket.h>

#include <netinet/in.h>

#include <arpa/inet.h>

#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "wconfig.h"
#include "wbackend.h"

#define CONFIGFILE "./willow.conf"

struct listener **listeners;
int nlisteners;

static void add_listener(char *);
static void add_cachedir(char *);

static void 
add_listener(addr)
	char *addr;
{
struct	listener	*nl;
	char		*port, *host = addr;

	if ((nl = malloc(sizeof(*nl))) == NULL) {
		fputs("out of memory\n", stderr);
		abort();
	}

	memset(nl, 0, sizeof(struct listener));

	if ((listeners = realloc(listeners, sizeof(struct listener *) * ++nlisteners)) == NULL) {
		fputs("out of memory\n", stderr);
		abort();
	}
	
	if ((port = strchr(host, ':')) != NULL) {
		*port++ = '\0';
		nl->port = atoi(port);
	} else
		nl->port = 80;
	nl->name = strdup(host);
	nl->addr.sin_family = AF_INET;
	nl->addr.sin_port = htons(nl->port);
	nl->addr.sin_addr.s_addr = inet_addr(nl->name);
	listeners[nlisteners - 1] = nl;
}

static void
add_cachedir(line)
	char *line;
{
	fprintf(stderr, "add cache_dir: %s\n", line);
}

static void
skip(s)
	char **s;
{
	char	*p;
	
	if ((p = strchr(*s, ' ')) == NULL)
		return;
	
	*p++ = '\0';
	*s = p;
}

void
wconfig_init(const char *file)
{
	char	 line[1024];
	FILE	*cfg;
	int	 linenum = 0;
	
	if (file == NULL)
		file = CONFIGFILE;
	
	if ((cfg = fopen(file, "r")) == NULL) {
		perror(file);
		exit(8);
	}
	
	while (fgets(line, sizeof line, cfg)) {
		char *p = strchr(line, '#');
		char *s = line, *opt = s;
		
		++linenum;
		line[strlen(line) - 1] = '\0';
		if (p)
			*p = '\0';
		if (!*line)
			continue;
		skip(&s);
		if (!strcmp(opt, "backend")) {
			if (!*s) {
				fprintf(stderr, "%s:%d: no backend specified\n",
					file, linenum);
				exit(8);
			}
			add_backend(s);
			continue;
		} else if (!strcmp(opt, "backend_file")) {
			if (!*s) {
				fprintf(stderr, "%s:%d: no file specified\n",
					file, linenum);
				exit(8);
			}
			backend_file(s);
			continue;
		} else if (!strcmp(opt, "listen")) {
			if (!*s) {
				fprintf(stderr, "%s:%d: no address specified\n",
					file, linenum);
				exit(8);
			}
			add_listener(s);
			continue;
		} else if (!strcmp(opt, "cache_dir")) {
			add_cachedir(s);
			continue;
		} else {
			fprintf(stderr, "%s:%d: unknown configuration option \"%s\"\n",
				file, linenum, opt);
			exit(8);
		}
	}
	fclose(cfg);
}
