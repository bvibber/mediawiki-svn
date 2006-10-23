/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wconfig: configuration.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <sys/types.h>
#include <sys/socket.h>

#include <netinet/in.h>
#include <arpa/inet.h>
#include <syslog.h>

#include <cstdlib>
#include <cstdio>
#include <cstring>
#include <cerrno>
#include <climits>
#include <netdb.h>

#include "willow.h"
#include "wconfig.h"
#include "wbackend.h"
#include "wlog.h"
#include "whttp.h"
#include "wnet.h"
#include "confparse.h"
#include "radix.h"

using namespace conf;

#define CONFIGFILE SYSCONFDIR "/willow.conf"

vector<listener *> listeners;
struct configuration config;

static void
set_backend(conf::tree_entry &e)
{
value const	*val;
int		 port = 80, family = -1;
	if ((val = e/"port") != NULL)
		port = CONF_AINTVAL(*val);
	if ((val = e/"aftype") != NULL)
		if (val->cv_values[0].av_strval == "ipv6")
			family = AF_INET6;
		else	family = AF_INET;
	add_backend(e.item_key, port, family);
}

static void
set_listen(conf::tree_entry &e)
{
value const	*val;
int		 port = 80;
struct listener	*nl;
int		 i;
addrinfo	 hints, *res, *r;
char		 portstr[6];
	if ((val = e/"port") != NULL)
		port = CONF_AINTVAL(*val);
	sprintf(portstr, "%d", port);
	std::memset(&hints, 0, sizeof(hints));
	hints.ai_socktype = SOCK_STREAM;
	if ((val = e/"aftype") != NULL)
		if (val->cv_values[0].av_strval == "ipv6")
			hints.ai_family = AF_INET6;
		else	hints.ai_family = AF_INET;

	if ((i = getaddrinfo(e.item_key.c_str(), portstr, &hints, &res)) != 0) {
		wlog(WLOG_ERROR, "resolving %s: %s", e.item_key.c_str(), gai_strerror(i));
		return;
	}

	for (r = res; r; r = r->ai_next) {
		nl = new listener;
		nl->port = port;
		nl->name = e.item_key;
		nl->host = wnet::straddr(r->ai_addr, r->ai_addrlen);
		memcpy(&nl->addr, r->ai_addr, r->ai_addrlen);
		listeners.push_back(nl);
		wlog(WLOG_NOTICE, "listening on %s[%s]:%d", 
		     e.item_key.c_str(), nl->host.c_str(), port);
	}
	freeaddrinfo(res);
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

static bool
v_carp_hash(tree_entry &e, value &v)
{
	if (!v.is_single(cv_string)) {
		v.report_error("expected single unquoted string");
		return false;
	}
string	&s = v.cv_values[0].av_strval;
	if (s != "carp" && s != "simple") {
		v.report_error("carp-hash must be \"carp\" or \"simple\"");
		return false;
	}
	return true;
}

static void
s_carp_hash(tree_entry &e, value &v)
{
string	&s = v.cv_values[0].av_strval;
	if (s == "carp")
		config.carp_hash = configuration::carp_hash_carp;
	else	config.carp_hash = configuration::carp_hash_simple;
}

static bool
v_udp_log(tree_entry &e, value &v)
{
value	*val;
bool	 ret = true;
	if ((val = e/"udp-host") == NULL) {
		v.report_error("udp-host must be specified for UDP logging");
		ret = false;
	}
	return ret;
}

static bool
v_aftype(tree_entry &e, value &v)
{
	if (!v.is_single(cv_string)) {
		v.report_error("aftype must be single unquoted string");
		return false;
	}
string	&s = v.cv_values[0].av_strval;
	if (s != "ipv4" && s != "ipv6") {
		v.report_error("aftype must be \"ipv4\" or \"ipv6\"");
		return false;
	}
	return true;
}

static void
radix_from_list(tree_entry &e, radix *rad)
{
value		*val;
radix_node	*r;
int		 immed = 0;
	if ((val = e/"apply-at") != NULL)
		if (val->cv_values[0].av_strval == "connect")
			immed = RFL_CONNECT;

	if ((val = e/"allow") != NULL) {
	vector<avalue>::iterator	it = val->cv_values.begin(),
					end = val->cv_values.end();
		for (; it != end; ++it) {
			r = radix_add(rad, it->av_strval.c_str());
			r->flags = RFL_ALLOW | immed;
		}
	}

	if ((val = e/"deny") != NULL) {
	vector<avalue>::iterator	it = val->cv_values.begin(),
					end = val->cv_values.end();
		for (; it != end; ++it) {
			r = radix_add(rad, it->av_strval.c_str());
			r->flags = RFL_DENY | immed;
		}
	}
}

static void
set_access_v4(tree_entry &e)
{
	config.v4_access = new radix;
	radix_from_list(e, config.v4_access);
}

static void
set_access_v6(tree_entry &e)
{
	config.v6_access = new radix;
	radix_from_list(e, config.v6_access);
}

static void
stats_access_v4(tree_entry &e, value &v)
{
	stats.v4_access = new radix;
vector<avalue>::iterator	it = v.cv_values.begin(),
				end = v.cv_values.end();
	for (; it != end; ++it)
		radix_add(stats.v4_access, it->av_strval.c_str());
}

static void
stats_access_v6(tree_entry &e, value &v)
{
	stats.v6_access = new radix;
vector<avalue>::iterator	it = v.cv_values.begin(),
				end = v.cv_values.end();
	for (; it != end; ++it)
		radix_add(stats.v6_access, it->av_strval.c_str());
}

static bool
radix_prefix(tree_entry &e, value &v)
{
vector<avalue>::iterator	it = v.cv_values.begin(),
				end = v.cv_values.end();
prefix	p;
	for (; it != end; ++it) {
		if (it->av_type != cv_qstring) {
			v.report_error("access prefix must be a list of quoted strings");
			return false;
		}

		if (prefix_fromstring(it->av_strval.c_str(), &p) == NULL) {
			v.report_error("%s: cannot parse network address mask", it->av_strval.c_str());
			return false;
		}

		if ((p.family == AF_INET && p.prefixlen > 32)
		    || (p.family == AF_INET6 && p.prefixlen > 128)) {
			v.report_error("%s: prefix length is too long for address family", it->av_strval.c_str());
			return false;
		}
	}
	return true;
}

bool
v_apply_at(tree_entry &e, value &v)
{
	if (!v.is_single(cv_string)) {
		v.report_error("apply-at must be single unquoted string");
		return false;
	}
string	&s = v.cv_values[0].av_strval;
	if (s != "connect" && s != "request") {
		v.report_error("expected \"connect\" or \"request\"");
		return false;
	}
	return true;
}

bool
read_config(string const &file)
{
conf_definer	 conf;
tree		*t;
conf
	.block("log")
		.value("level",		simple_range(0, 3),		set_int(logging.level))
		.value("file",		nonempty_qstring,		set_qstring(logging.file))
		.value("syslog",	simple_yesno,			set_yesno(logging.syslog))
		.value("facility",	func(validate_log_facility),	func(set_log_facility))
		.value("access-log",	nonempty_qstring,		set_qstring(config.access_log))
		.value("log-sample",	simple_range(1, INT_MAX),	set_int(config.udplog_sample))
		.value("udp-log",	simple_yesno,			set_yesno(config.udp_log))
		.value("udp-port",	simple_range(0, 65535),		set_int(config.udplog_port))
		.value("udp-host",	nonempty_qstring,		set_string(config.udplog_host))

	.block("cache")
		.value("expire-every",		simple_time,		set_time(config.cache_expevery))
		.value("expire-threshold",	simple_range(0, 100),	set_int(config.cache_expthresh))
		.value("compress",		simple_yesno,		set_yesno(config.compress))
		.value("compress-level",	simple_range(1, 9),	set_int(config.complevel))
		.value("backend-retry",		simple_time,		set_time(config.backend_retry))
		.value("cache-private",		simple_yesno,		set_yesno(config.cache_private))
		.value("use-carp",		simple_yesno,		set_yesno(config.use_carp))
		.value("carp-hash",		func(v_carp_hash),	func(s_carp_hash))

	.block("stats")
		.value("port",		simple_range(0, 65535),		set_int(config.stats_port))
		.value("interval",	simple_range(1, INT_MAX),	set_int(stats.interval))
		.value("allow-v4",	func(radix_prefix),		func(stats_access_v4))
		.value("allow-v6",	func(radix_prefix),		func(stats_access_v6))
		.value("enable",	simple_yesno,			set_yesno(config.udp_stats))
		.value("listen",	nonempty_qstring,		set_string(config.stats_host))

	.block("cache-dir", require_name)
		.end(func(set_cache))
		.value("size", simple_time, ignore)

	.block("listen", require_name)
		.end(func(set_listen))
		.value("port",		simple_range(1, 65535), ignore)
		.value("aftype",	func(v_aftype),		ignore)

	.block("backend", require_name)
		.end(func(set_backend))
		.value("port",		simple_range(1, 65535), ignore)
		.value("aftype",	func(v_aftype),		ignore)

	.block("access-v4")
		.end(func(set_access_v4))
		.value("allow",		func(radix_prefix),	ignore)
		.value("deny",		func(radix_prefix),	ignore)
		.value("apply-at",	func(v_apply_at),	ignore)

	.block("access-v6")
		.end(func(set_access_v6))
		.value("allow",		func(radix_prefix),	ignore)
		.value("deny",		func(radix_prefix),	ignore)
		.value("apply-at",	func(v_apply_at),	ignore)
	;

	if ((t = conf::parse_file(file)) == NULL)
		return false;
	if (!conf.validate(*t))
		return false;

	/*
	 * Defaults
	 */
	stats.interval = DEFAULT_STATS_INTERVAL;
	config.stats_port = DEFAULT_STATS_PORT;
	conf.set(*t);
	whttp_reconfigure();
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
