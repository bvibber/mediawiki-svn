/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* config: configuration.						*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

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
#include <pthread.h>
#include <set>
#include <fstream>
using std::ifstream;
using std::set;
using std::back_inserter;

#include "loreley.h"
#include "config.h"
#include "backend.h"
#include "log.h"
#include "http.h"
#include "net.h"
#include "confparse.h"
#include "access.h"
#include "format.h"

using namespace conf;

map<wsocket *, listener *> sock2lsn;
set<int> used_pools;

#define CONFIGFILE SYSCONFDIR "/loreley.conf"

vector<listener *> listeners;
struct configuration config;

static void
set_backend(conf::tree_entry &e)
{
value const	*val;
int		 port = 80, family = AF_UNSPEC, gn = 0;
string		 group = "<default>";
map<string, int>::iterator it;

	if ((val = e/"port") != NULL)
		port = boost::get<scalar_q>(val->cv_values[0]).value();
	if ((val = e/"aftype") != NULL)
		if (boost::get<u_string>(val->cv_values[0]).value() == "ipv6")
			family = AF_INET6;
		else	family = AF_INET;

	if ((val = e/"group") != NULL) {
		group = boost::get<q_string>(val->cv_values[0]).value();

		it = poolnames.find(group);
		if (it == poolnames.end()) {
			conf::report_error(boost::get<q_string>(val->cv_values[0]),
				format("backend group %s does not exist")
				% group);
			return;
		} else
			gn = it->second;
	}

	used_pools.insert(gn);
	bpools.find(gn)->second.add(e.item_key, port, family);
}

static void
set_listen(conf::tree_entry &e)
{
value const	*val;
int		 port = 80;
struct listener	*nl;
int		 gn = 0;
string		 group;
int		 fam = AF_UNSPEC;
addrlist	*res;

	if ((val = e/"port") != NULL)
		port = boost::get<scalar_q>(val->cv_values[0]).value();

	if ((val = e/"aftype") != NULL)
		if (boost::get<u_string>(val->cv_values[0]).value() == "ipv6")
			fam = AF_INET6;
		else	fam = AF_INET;

	if ((val = e/"group") != NULL) {
	map<string, int>::iterator	it;
		group = boost::get<q_string>(val->cv_values[0]).value();

		it = poolnames.find(group);
		if (it == poolnames.end()) {
			val->report_error(format("backend group %s does not exist")
				% group);
			return;
		} else
			gn = it->second;
	}

	try {
		res = addrlist::resolve(e.item_key, port, st_stream, fam);
	} catch (socket_error &ex) {
		wlog.error(format("resolving %s: %s") % e.item_key % ex.what());
		return;
	}

addrlist::iterator	it = res->begin(), end = res->end();
	for (; it != end; ++it) {
		nl = new listener;
		nl->nconns = 0;

		try {
			nl->sock = it->makesocket("HTTP listener", prio_accept);
		} catch (socket_error &ex) {
			wlog.error(format("creating listener %s: %s")
				% e.item_key % ex.what());
			delete nl;
			delete res;
			return;
		}
		WDEBUG(format("listener %d has group %d") % nl->sock % gn);
		sock2lsn[nl->sock] = nl;
		used_pools.insert(gn);

		nl->port = port;
		nl->name = e.item_key;
		nl->group = gn;
		listeners.push_back(nl);
		wlog.notice(format("listening on %s%s (group %d)")
		     % e.item_key % it->straddr() % gn);
	}
	delete res;
}

map<string, int> log_levels = map_list_of
	("auth", LOG_AUTH)
#ifdef LOG_AUTHPRIV
	("authpriv", LOG_AUTHPRIV)
#endif
	("cron", LOG_CRON)
	("daemon", LOG_DAEMON)
#ifdef LOG_FTP
	("ftp", LOG_FTP)
#endif
	("kern", LOG_KERN)
	("local0", LOG_LOCAL0)
	("local1", LOG_LOCAL1)
	("local2", LOG_LOCAL2)
	("local3", LOG_LOCAL3)
	("local4", LOG_LOCAL4)
	("local5", LOG_LOCAL5)
	("local6", LOG_LOCAL6)
	("local7", LOG_LOCAL7)
	("lpr", LOG_LPR)
	("mail", LOG_MAIL)
	("news", LOG_NEWS)
	("syslog", LOG_SYSLOG)
	("user", LOG_USER)
	("uucp", LOG_UUCP)
	;

static bool
validate_log_facility(tree_entry &, value &v)
{
	if (!v.is_single<u_string>()) {
		v.report_error("expected single unquoted string");
		return false;
	}

string	lev = v.get<u_string>(0).value();
	if (log_levels.find(lev) == log_levels.end()) {
		v.report_error(format("log level \"%s\" does not exist") % lev);
		return false;
	}

	return true;
}

static void
set_log_facility(tree_entry &, value &v)
{
	wlog.syslog(true, log_levels.find(v.get<u_string>(0).value())->second);
}

static bool
v_udp_log(tree_entry &e, value &v)
{
bool	 ret = true;
	if (e/"udp-host" == NULL) {
		v.report_error("udp-host must be specified for UDP logging");
		ret = false;
	}

	if (!v.is_single<bool_q>()) {
		v.report_error("udp-log must be yes/no");
		ret = false;
	}

	return ret;
}

static bool
v_aftype(tree_entry &, value &v)
{
	if (!v.is_single<u_string>()) {
		v.report_error("aftype must be single unquoted string");
		return false;
	}
string const	&s = v.get<u_string>(0).value();
	if (s != "ipv4" && s != "ipv6") {
		v.report_error("aftype must be \"ipv4\" or \"ipv6\"");
		return false;
	}
	return true;
}

static void
radix_from_list(tree_entry &e, access_list &rad)
{
value		*val;
int		 flags = 0;
	if ((val = e/"apply-at") != NULL)
		if (val->get<u_string>().value() == "connect")
			flags |= http_deny_connect;

	if ((val = e/"log") != NULL)
		if (val->get<bool_q>().value())
			flags |= http_log_denied;

	WDEBUG(format("radix_from_list: flags=%d") % flags);

	if ((val = e/"allow") != NULL) {
	vector<avalue_t>::iterator	it = val->cv_values.begin(),
					end = val->cv_values.end();
		for (; it != end; ++it)
			rad.allow(boost::get<q_string>(*it).value(), flags);
	}

	if ((val = e/"deny") != NULL) {
	vector<avalue_t>::iterator	it = val->cv_values.begin(),
					end = val->cv_values.end();
		for (; it != end; ++it)
			rad.deny(boost::get<q_string>(*it).value(), flags);
	}
}

static void
set_access(tree_entry &e)
{
	radix_from_list(e, config.access);
}

static void
stats_access(tree_entry &, value &v)
{
vector<avalue_t>::iterator	it = v.cv_values.begin(),
				end = v.cv_values.end();
	for (; it != end; ++it)
		stats.access.allow(boost::get<q_string>(*it).value());
}

static void
force_backend_access(tree_entry &, value &v)
{
vector<avalue_t>::iterator	it = v.cv_values.begin(),
				end = v.cv_values.end();
	for (; it != end; ++it)
		config.force_backend.allow(boost::get<q_string>(*it).value(), 1);
}

static bool
radix_prefix(tree_entry &, value &v)
{
vector<avalue_t>::iterator	it = v.cv_values.begin(),
				end = v.cv_values.end();
	for (; it != end; ++it) {
		if (!is_type<q_string>(*it)) {
			v.report_error("access prefix must be a list of quoted strings");
			return false;
		}

		try {
		prefix	p(boost::get<q_string>(*it).value());
		} catch (invalid_prefix& e) {
			v.report_error(format("%s: %s")
				% boost::get<q_string>(*it).value()
				% e.what());
			return false;
		}
	}
	return true;
}

bool
v_apply_at(tree_entry &, value &v)
{
	if (!v.is_single<u_string>()) {
		v.report_error("apply-at must be single unquoted string");
		return false;
	}
string const	&s = boost::get<u_string>(v.cv_values[0]).value();
	if (s != "connect" && s != "request") {
		v.report_error("expected \"connect\" or \"request\"");
		return false;
	}
	return true;
}

bool
v_lb_type(tree_entry &, value &v)
{
	if (!v.is_single<u_string>()) {
		v.report_error("lb-type must be single unquoted string");
		return false;
	}
string const	&s = boost::get<u_string>(v.cv_values[0]).value();
	if (s != "rr" && s != "carp" && s != "carp-host") {
		v.report_error("expected \"rr\", \"carp\" or \"carp-host\"");
		return false;
	}
	return true;
}

void
set_backend_group(tree_entry &e)
{
int	gn, fogroup = -1;
string	group;
map<string, int>::iterator	it;
	group = e.item_key;

	it = poolnames.find(group);
	if (it == poolnames.end()) {
		gn = nbpools;
		poolnames[group] = nbpools++;
	} else
		gn = it->second;

lb_type	 lbtype = lb_rr;
value	*v;
	if ((v = e/"lb-type") != NULL) {
	string const	&s = v->get<u_string>(0).value();
		if (s == "rr")
			lbtype = lb_rr;
		else if (s == "carp")
			lbtype = lb_carp;
		else if (s == "carp-host")
			lbtype = lb_carp_hostonly;
	}

	if ((v = e/"failover-group") != NULL) {
		if ((it = poolnames.find(v->get<q_string>(0).value())) == poolnames.end()) {
			v->report_error("failover-group does not exist");
		} else {
			fogroup = it->second;
		}
	}

	WDEBUG(format("adding backend %d type = %d") % gn % (int) lbtype);
	bpools.insert(make_pair(gn, backend_pool(e.item_key, lbtype, fogroup)));

	if ((v = e/"hosts") != NULL) {
	vector<avalue_t>::iterator hit = v->cv_values.begin(), hend = v->cv_values.end();
		for (; hit != hend; ++hit)
			host_to_bpool[imstring(boost::get<q_string>(*hit).value())] = gn;
		used_pools.insert(gn);
	}
}

bool
v_hosts(tree_entry &, value &v)
{
vector<avalue_t>::iterator it = v.cv_values.begin(), end = v.cv_values.end();
	for (; it != end; ++it)
		if (!is_type<q_string>(*it)) {
			v.report_error("hosts must be a list of quoted strings");
			return false;
		}
	return true;
}

void
set_cache_dir(tree_entry &e)
{
	config.cachedirs.push_back(cachedir(e.item_key));
}

void
set_htcp_keys(tree_entry &e, value &v)
{
string const 	&file = v.get<q_string>(0).value();
ifstream	f(file.c_str());
	if (!f.is_open()) {
		v.report_error(format("cannot open HTCP key file %s: %s")
			% file % strerror(errno));
		return;
	}

string	s;
int	line = 0;
	while (getline(f, s)) {
	string			name, key;
	string::size_type	i;
		++line;
		if ((i = s.find(' ')) == string::npos) {
			v.report_error(format("%s(%d): syntax error")
				% file % line);
			continue;
		}
		name = s.substr(0, i);
		key = s.substr(i + 1);

		if (key.size() != 683) {
			v.report_error(format("%s(%d): key has wrong length")
				% file % line);
			continue;
		}

	ustring		bkey;
	unbase64_string it(key.begin());
		for (size_t i = 0; i < 64; ++i) {
			bkey.push_back(*it++);
		}
		config.htcp_keys[name] = bkey;
	}
}

void
set_log_level(tree_entry &e, value &v)
{
	wlog.level(log_level(v.get<scalar_q>(0).value()));
}

void
set_log_file(tree_entry &e, value &v)
{
	wlog.file(v.get<q_string>(0).value());
}

bool
read_config(string const &file)
{
conf_definer	 conf;
tree		*t;
conf
	.block("log")
		.value("level",		simple_range(0, 3),		func(set_log_level))
		.value("file",		nonempty_qstring,		func(set_log_file))
		.value("syslog-facility",	
					func(validate_log_facility),	func(set_log_facility))
		.value("access-log",	nonempty_qstring,		set_qstring(config.access_log))
		.value("log-sample",	simple_range(1, INT_MAX),	set_int(config.log_sample))
		.value("udp-log",	func(v_udp_log),		set_yesno(config.udp_log))
		.value("udp-port",	simple_range(0, 65535),		set_int(config.udplog_port))
		.value("udp-host",	nonempty_qstring,		set_qstring(config.udplog_host))

	.block("cache")
		.value("cache-memory",		simple_size,		set_size(config.cache_memory))
		.value("max-entity-size",	simple_size,		set_size(config.max_entity_size))
		.value("master-state",		nonempty_qstring,	set_qstring(config.cache_master))
		.value("htcp-listen",		ip_address_list,	add_ip(config.htcp_hosts))
		.value("htcp-keys",		nonempty_qstring,	func(set_htcp_keys))
		.value("htcp-sig-required",	simple_yesno,		set_yesno(config.htcp_sigrequired))

	.block("cache-dir", require_name)
		.end(func(set_cache_dir))

	.block("http")
		.value("compress",		simple_yesno,		set_yesno(config.compress))
		.value("compress-level",	simple_range(1, 9),	set_int(config.complevel))
		.value("backend-retry",		simple_time,		set_time(config.backend_retry))
		.value("cache-private",		simple_yesno,		set_yesno(config.cache_private))
		.value("msie-http11-hack",	simple_yesno,		set_yesno(config.msie_hack))
		.value("default-host",		nonempty_qstring,	set_qstring(config.default_host))
		.value("force-backend",		func(radix_prefix),	func(force_backend_access))
		.value("backend-keepalive",	simple_yesno,		set_yesno(config.backend_keepalive))
		.value("client-keepalive",	simple_yesno,		set_yesno(config.client_keepalive))
		.value("keepalive-max",		simple_range(0),	set_int(config.keepalive_max))
		.value("x-follow-redirect",	simple_yesno,		set_yesno(config.x_follow))
		.value("max-redirects",		simple_range(1),	set_int(config.max_redirects))

	.block("server")
		.value("threads",	simple_range(1, 1024),	set_int(config.nthreads))
		.value("admin",		nonempty_qstring,	set_qstring(config.admin))
		.value("use-dio",	simple_yesno,		set_yesno(config.use_dio))

	.block("stats")
		.value("interval",	simple_range(1, INT_MAX),	set_aint(stats.interval))
		.value("allow",		func(radix_prefix),		func(stats_access))
		.value("enable",	simple_yesno,			set_yesno(config.udp_stats))
		.value("listen",	ip_address_list,		add_ip(config.stats_hosts))

	.block("listen", require_name)
		.end(func(set_listen))
		.value("port",		simple_range(1, 65535), ignore)
		.value("aftype",	func(v_aftype),		ignore)
		.value("group",		nonempty_qstring,	ignore)

	.block("backend-group", require_name)
		.end(func(set_backend_group))
		.value("lb-type",		func(v_lb_type),	ignore)
		.value("hosts",			func(v_hosts),		ignore)
		.value("failover-group",	nonempty_qstring,	ignore)

	.block("backend", require_name)
		.end(func(set_backend))
		.value("port",		simple_range(1, 65535), ignore)
		.value("aftype",	func(v_aftype),		ignore)
		.value("group",		nonempty_qstring,	ignore)

	.block("access")
		.end(func(set_access))
		.value("allow",		func(radix_prefix),	ignore)
		.value("deny",		func(radix_prefix),	ignore)
		.value("apply-at",	func(v_apply_at),	ignore)
		.value("log",		simple_yesno,		ignore)
	;

	if ((t = conf::parse_file(file)) == NULL)
		return false;
	if (!conf.validate(*t))
		return false;

	/*
	 * Defaults
	 */
	stats.interval = DEFAULT_STATS_INTERVAL;
	config.nthreads = 1;
	config.admin = "nobody@example.com";
	poolnames["<default>"] = 0;
	bpools.insert(make_pair(0, backend_pool("<default>", lb_rr)));
	config.backend_keepalive = true;
	config.client_keepalive = true;
	config.keepalive_max = 0;
	config.max_redirects = 1;
	config.use_dio = false;
	config.x_follow = false;
	config.cache_memory = 0;

	conf.set(*t);
	whttp_reconfigure();
	if (config.access.empty()) {
		WDEBUG("access is empty...");
		config.access.allow("0.0.0.0/0");
		config.access.allow("::0/0");
	}
	global_conf_tree = *t;
	return true;
}

void
wconfig_init(const char *file)
{
int	nerrors = 0;
	if (file == NULL)
		file = CONFIGFILE;

	wlog.notice(format("loading configuration from %s") % file);

	if (!read_config(file)) {
		wlog.error("cannot load configuration");
		exit(8);
	}
	
	if (!listeners.size()) {
		wlog.error("no listeners defined");
		nerrors++;
	}
	if (!bpools.size()) {
		wlog.error("no backends defined");
		nerrors++;
	}

	for (map<int, backend_pool>::iterator it = bpools.begin(), end = bpools.end();
	     it != end; ++it) {
		if (!it->second.size() && used_pools.find(it->first) != used_pools.end()) {
			wlog.error(format(
				"backend group \"%s\" is used but has no backends")
				% it->second.name());
			nerrors++;
		}
	}

	if (nerrors) {
		wlog.error(format(
			"%d error(s) in configuration file.  cannot continue.")
			% nerrors);
		exit(8);
	}
}
