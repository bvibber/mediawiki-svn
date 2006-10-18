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

using namespace conf;

#define CONFIGFILE SYSCONFDIR "/willow.conf"

vector<listener *> listeners;
struct configuration config;

static void
set_backend(conf::tree_entry &e)
{
value const	*val;
int		 port = 80;
	if ((val = e/"port") != NULL)
		port = CONF_AINTVAL(*val);
	add_backend(e.item_key, port);
}

static void
set_listen(conf::tree_entry &e)
{
value const	*val;
int		 port = 80;
struct listener	*nl = new listener;
	if ((val = e/"port") != NULL)
		port = CONF_AINTVAL(*val);
	listeners.push_back(nl);
	
	nl->port = port;
	nl->name = e.item_key;
	nl->addr.sin_family = AF_INET;
	nl->addr.sin_port = htons(nl->port);
	nl->addr.sin_addr.s_addr = inet_addr(nl->name.c_str());
	wlog(WLOG_NOTICE, "listening on %s:%d", e.item_key.c_str(), port);
}

static bool
validate_log_facility(tree_entry &e, value &v)
{
	return true;
}

static void
set_log_facility(tree_entry &e, value &v)
{
}

static void
set_cache(tree_entry &e)
{
value	*v;
	v = e/"size";
	config.caches = (cachedir *)wrealloc(config.caches, sizeof(*config.caches) * (config.ncaches + 1));
	config.caches[config.ncaches].dir = wstrdup(e.item_key.c_str());
	config.caches[config.ncaches].maxsize = v->cv_values[0].av_intval;
	wlog(WLOG_NOTICE, "cache dir \"%s\", size %d bytes",
			config.caches[config.ncaches].dir,
			config.caches[config.ncaches].maxsize);
	config.ncaches++;
}

extern int parse_error;

bool
read_config(string const &file)
{
conf_definer	 conf;
tree		*t;
conf
	.block("log")
		.value("level",		simple_range(0, 3),		set_int(logging.level))
		.value("file",		nonempty_qstring(),		set_qstring(logging.file))
		.value("syslog",	simple_yesno(),			set_yesno(logging.syslog))
		.value("facility",	func(validate_log_facility),	func(set_log_facility))
		.value("access-log",	nonempty_qstring(),		set_qstring(config.access_log))

	.block("cache")
		.value("expire-every",		simple_time(),		set_time(config.cache_expevery))
		.value("expire-threshold",	simple_range(0, 100),	set_int(config.cache_expthresh))
		.value("compress",		simple_yesno(),		set_yesno(config.compress))
		.value("compress-level",	simple_range(1, 9),	set_int(config.complevel))
		.value("backend-retry",		simple_time(),		set_time(config.backend_retry))
		.value("cache-private",		simple_yesno(),		set_yesno(config.cache_private))
		.value("use-carp",		simple_yesno(),		set_yesno(config.use_carp))

	.block("cache-dir", require_name)
		.end(func(set_cache))
		.value("size", simple_time(), ignore())

	.block("listen", require_name)
		.end(func(set_listen))
		.value("port", simple_range(1, 65535), ignore())

	.block("backend", require_name)
		.end(func(set_backend))
		.value("port", simple_range(1, 65535), ignore())
	;

	if ((t = conf::parse_file(file)) == NULL)
		return false;
	if (!conf.validate(*t))
		return false;
	conf.set(*t);
	global_conf_tree = *t;
	return true;
}

void
wconfig_init(const char *file)
{
int	nerrors = 0;
	if (file == NULL)
		file = CONFIGFILE;
	conf::current_file = file;

	wlog(WLOG_NOTICE, "loading configuration from %s", conf::current_file.c_str());
	if (!read_config(file)) {
		wlog(WLOG_ERROR, "cannot load configuration");
		nerrors++;
	}
	
	if (!listeners.size()) {
		wlog(WLOG_ERROR, "no listeners defined");
		nerrors++;
	}
	if (!backends.size()) {
		wlog(WLOG_ERROR, "no backends defined");
		nerrors++;
	}
	if (nerrors) {
		wlog(WLOG_ERROR, "%d error(s) in configuration file.  cannot continue.", nerrors);
		exit(8);
	}
}
