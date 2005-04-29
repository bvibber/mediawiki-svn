/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wconfig: configuration.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
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

void
wconfig_init(const char *file)
{
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
		nerrors++;
	}
	if (!nlisteners) {
		wlog(WLOG_ERROR, "no listeners defined");
		nerrors++;
	}
	if (!nbackends) {
		wlog(WLOG_ERROR, "no backends defined");
		nerrors++;
	}
	if (nerrors) {
		wlog(WLOG_ERROR, "%d error(s) in configuration file.  cannot continue.", nerrors);
		exit(8);
	}
	
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
	return 0;
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
	return 0;
}
