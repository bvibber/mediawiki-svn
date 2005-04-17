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
#include <syslog.h>

#include "wconfig.h"
#include "wbackend.h"
#include "wlog.h"

#define CONFIGFILE "./willow.conf"

struct listener **listeners;
int nlisteners;
struct configuration config;

static const char *current_file;
static int current_line;

static void add_listener(char *);
static void add_cachedir(char *);
static void add_log_options(char *);

static char *skip(char **);

static char *
skip(s)
	char **s;
{
	char	*p;
	char	*r = *s;
	
	if ((p = strchr(*s, ' ')) == NULL) {
		*s += strlen(*s);
		return r;
	}
	
	*p++ = '\0';
	*s = p;
	return r;
}

void
wconfig_init(const char *file)
{
	char	 line[1024];
	FILE	*cfg;
	
	current_file = file;

	if (file == NULL)
		file = CONFIGFILE;
	
	if ((cfg = fopen(file, "r")) == NULL) {
		perror(file);
		exit(8);
	}
	
	while (fgets(line, sizeof line, cfg)) {
		char *p = strchr(line, '#');
		char *s = line, *opt = s;
		
		++current_line;
		line[strlen(line) - 1] = '\0';
		if (p)
			*p = '\0';
		if (!*line)
			continue;
		skip(&s);
		if (!strcmp(opt, "backend")) {
			if (!*s) {
				fprintf(stderr, "%s:%d: no backend specified\n",
					file, current_line);
				exit(8);
			}
			add_backend(s);
			continue;
		} else if (!strcmp(opt, "backend_file")) {
			if (!*s) {
				fprintf(stderr, "%s:%d: no file specified\n",
					file, current_line);
				exit(8);
			}
			backend_file(s);
			continue;
		} else if (!strcmp(opt, "listen")) {
			if (!*s) {
				fprintf(stderr, "%s:%d: no address specified\n",
					file, current_line);
				exit(8);
			}
			add_listener(s);
			continue;
		} else if (!strcmp(opt, "cache_dir")) {
			add_cachedir(s);
			continue;
		} else if (!strcmp(opt, "log")) {
			add_log_options(s);
		} else if (!strcmp(opt, "log_level")) {
			if (!*s) {
				fprintf(stderr, "%s:%d: no log level specified\n",
					file, current_line);
				exit(8);
			}
			if (atoi(s) > WLOG_MAX) { 
				fprintf(stderr, "%s:%d: invalid log level\n",
					file, current_line);
				exit(8);
			}
			logging.level = atoi(s);
		} else if (!strcmp(opt, "access_log")) {
			if (!*s) {
				fprintf(stderr, "%s:%d: no filename specified\n",
						file, current_line);
				exit(8);
			}
			if ((config.access_log = fopen(s, "a")) == NULL) {
				perror(s);
				exit(8);
			}
		} else {
			fprintf(stderr, "%s:%d: unknown configuration option \"%s\"\n",
				file, current_line, opt);
			exit(8);
		}
	}
	fclose(cfg);
}

static struct syslog_facility {
	char	 *name;
	int	  fac;
} syslog_facilities[] = {
	{"user", LOG_USER},
	{"mail", LOG_MAIL},
        {"daemon", LOG_DAEMON},
	{"auth", LOG_AUTH},
	{"lpr", LOG_LPR},
	{"news", LOG_NEWS},
	{"uucp", LOG_UUCP},
	{"cron", LOG_CRON},
	{"audit", LOG_AUDIT},
	{"local0", LOG_LOCAL0},	
	{"local1", LOG_LOCAL0},	
	{"local2", LOG_LOCAL0},	
	{"local3", LOG_LOCAL0},	
	{"local4", LOG_LOCAL0},	
	{"local5", LOG_LOCAL0},	
	{"local6", LOG_LOCAL0},	
	{"local7", LOG_LOCAL0},	
	{NULL, 0},
};

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
add_log_options(line)
	char *line;
{
	char *option = skip(&line);

	if (!*option) {
		fprintf(stderr, "%s:%d: must specify type\n", current_file, current_line);
		exit(8);
	}

	if (!strcmp(option, "file")) {
		if (!*line) {
			fprintf(stderr, "%s:%d: no log file specified\n",
				current_file, current_line);
			exit(8);
		}
		logging.file = malloc(strlen(line)+1);
		strcpy(logging.file, line);
	} else if (!strcmp(option, "syslog")) {
		struct syslog_facility *fac = syslog_facilities;

		logging.syslog = 1;
		if (!*line) {
			fprintf(stderr, "%s:%d: must specify facility\n",
					current_file, current_line);
			exit(8);
		}

		for (; fac->name; fac++) {
			if (!strcmp(fac->name, line)) {
				logging.facility = fac->fac;
				break;
			}
		}
		if (!fac->name) {
			fprintf(stderr, "%s:%d: uinrecognised facility '%s'\n",
					current_file, current_line, line);
			exit(8);
		}
	}
}
