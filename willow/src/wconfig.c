/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wconfig: configuration.
 */

#ifdef __SUNPRO_C
# pragma ident "@(#)$Header$"
#endif

#include <sys/types.h>
#include <sys/socket.h>

#include <netinet/in.h>

#include <arpa/inet.h>

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <syslog.h>
#include <errno.h>
#include <strings.h>

#include "willow.h"
#include "wconfig.h"
#include "wbackend.h"
#include "wlog.h"
#include "confparse.h"

#define CONFIGFILE "./willow.conf"

int yyparse();

struct listener **listeners;
int nlisteners;
struct configuration config;

const char *current_file;

static void add_log_options(char *);

void
wconfig_init(const char *file)
{
	char	 line[1024];
	FILE	*cfg;
extern	FILE	*yyin;
	
	if (file == NULL)
		file = CONFIGFILE;
	current_file = file;

	if ((cfg = fopen(file, "r")) == NULL) {
		perror(file);
		exit(8);
	}
	wlog(WLOG_NOTICE, "loading configuration from %s", current_file);
	yyin = cfg;
	newconf_init();
	
	if (yyparse()) {
		wlog(WLOG_ERROR, "could not parse configuration file");
		exit(8);
	}
	if (nerrors) {
		wlog(WLOG_ERROR, "%d error(s) in configuration file.  cannot continue.", nerrors);
		exit(8);
	}
	if (!nlisteners) {
		wlog(WLOG_ERROR, "no listeners defined");
		exit(8);
	}
	if (!nbackends) {
		wlog(WLOG_ERROR, "no backends defined");
		exit(8);
	}
	
#if 0
	while (fgets(line, sizeof line, cfg)) {
		char *p = strchr(line, '#');
		char *s = line, *opt = s;
		
		++current_line;
		line[strlen(line) - 1] = '\0';
		if (p)
			*p = '\0';
		if (!*line)
			continue;
		(void)skip(&s);
		if (!strcmp(opt, "backend")) {
			if (!*s) {
				(void)fprintf(stderr, "%s:%d: no backend specified\n",
					file, current_line);
				exit(8);
			}
			add_backend(s);
			continue;
		} else if (!strcmp(opt, "backend_file")) {
			if (!*s) {
				(void)fprintf(stderr, "%s:%d: no file specified\n",
					file, current_line);
				exit(8);
			}
			backend_file(s);
			continue;
		} else if (!strcmp(opt, "listen")) {
			if (!*s) {
				(void)fprintf(stderr, "%s:%d: no address specified\n",
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
				(void)fprintf(stderr, "%s:%d: no log level specified\n",
					file, current_line);
				exit(8);
			}
			if (atoi(s) > WLOG_MAX) { 
				(void)fprintf(stderr, "%s:%d: invalid log level\n",
					file, current_line);
				exit(8);
			}
		} else if (!strcmp(opt, "access_log")) {
			if (!*s) {
				(void)fprintf(stderr, "%s:%d: no filename specified\n",
						file, current_line);
				exit(8);
			}
		} else {
			(void)fprintf(stderr, "%s:%d: unknown configuration option \"%s\"\n",
				file, current_line, opt);
			exit(8);
		}
	}
#endif
	(void)fclose(cfg);
}

int 
add_listener(addr, port)
	const char *addr;
	int port;
{
struct	listener	*nl;

	if (port < 0 || port > 65535) {
		conf_report_error("invalid listener port %d", port);
		nerrors++;
		return -1;
	}
	
	if ((nl = wcalloc(1, sizeof(*nl))) == NULL)
		outofmemory();

	if ((listeners = wrealloc(listeners, sizeof(struct listener *) * ++nlisteners)) == NULL)
		outofmemory();
	
	nl->port = port;
	nl->name = wstrdup(addr);
	nl->addr.sin_family = AF_INET;
	nl->addr.sin_port = htons(nl->port);
	nl->addr.sin_addr.s_addr = inet_addr(nl->name);
	listeners[nlisteners - 1] = nl;
	wlog(WLOG_NOTICE, "listening on %s:%d", addr, port);
}

int
add_cachedir(dir, size)
	const char *dir;
	int size;
{
	if (size < 1) {
		conf_report_error("invalid cache size %d\n", size);
		nerrors++;
		return -1;
	}
	
	config.caches = wrealloc(config.caches, sizeof(*config.caches) * (config.ncaches + 1));
	config.caches[config.ncaches].dir = wstrdup(dir);
	config.caches[config.ncaches].maxsize = size;
	wlog(WLOG_NOTICE, "cache dir \"%s\", size %d bytes",
			config.caches[config.ncaches].dir,
			config.caches[config.ncaches].maxsize);
	config.ncaches++;
}
