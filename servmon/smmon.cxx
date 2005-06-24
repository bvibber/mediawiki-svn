/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smmon.hxx"
#include "smtmr.hxx"
#include "smcfg.hxx"
#include "smirc.hxx"
#include "smalrm.hxx"
#include "smlog.hxx"
#include "smsnmp.hxx"
#include "smmysql.hxx"

namespace smmon {

std::string
cfg::get_option(str server, str option, str deflt)
{
    try {
        return SMI(smcfg::cfg)->fetchstr("/monit/server/" + server + "/" + option);
    } catch (smcfg::nokey&) {
        return deflt;
    }
}

void
cfg::initialise(void)
{
	try {
		std::set<std::string> servers = SMI(smcfg::cfg)->fetchlist("/monit/servers");
		FE_TC_AS(std::set<std::string>, servers, i) {
			create_server(*i, get_option(*i, "type", "none"), false);
                        set_cluster(*i, get_option(*i, "cluster", "unknown"));
		}
	} catch (smcfg::nokey&) {}
	checker *c = new checker();
	c->run();
}

void
cfg::checker::chk1(void)
{
	try {
		b::try_mutex::scoped_try_lock m (chk_m);
		std::map<std::string, serverp>& serverlist = SMI(cfg)->servers();
		for(std::map<std::string, serverp>::const_iterator
			    it = serverlist.begin(), end = serverlist.end(); it != end; ++it) {
			it->second->check();
		}
	} catch (b::lock_error&) {
		SMI(smlog::log)->logmsg(0, SM$FAC_MONIT, SM$MSG_MTXFAIL);
		return;
	}
}

void
cfg::checker::start(void)
{
	for (;;)
		check_loop();
}

void
cfg::checker::check_loop(void)
{
static	int	lastirc = 0;
	int	interval, ircinterval;

	interval = SMI(smcfg::cfg)->fetchint("/monit/interval", 10);
	ircinterval = SMI(smcfg::cfg)->fetchint("/monit/ircinterval", 60);

	sleep(interval);
	chk1();

	std::time_t now = std::time(0);
	if ((now - ircinterval) <= lastirc)
		return;
	lastirc = now;

	/* IRC report, split by cluster */
	b::try_mutex::scoped_lock m (chk_m);
	std::map<std::string, serverp>& serverlist = SMI(cfg)->servers();

	/* for each cluster, a list of servers */
	std::map<std::string, std::set<serverp> > servers;

	for(std::map<std::string, serverp>::const_iterator
	    it = serverlist.begin(), end = serverlist.end(); it != end; ++it) {
		servers[it->second->cluster].insert(it->second);
	}

	/* for each set, print status */
	for(std::map<std::string, std::set<serverp> >::iterator it = servers.begin(),
	    end = servers.end(); it != end; ++it) {
		irc_print_cluster(it->first, it->second);
	}
}

void
cfg::checker::irc_print_cluster(str cluster, std::set<serverp>& hosts)
{
	int mysqltotal = 0;

	std::map<std::string, std::string> mysqlreps;
	std::string squidrep, mysqlrep;
	std::string mastername;

	/* mysql */
	FE_TC_AS(std::set<serverp>, hosts, it) {
		if ((*it)->type() != "MySQL")
			continue;
		b::shared_ptr<mysqlserver> s = b::dynamic_pointer_cast<mysqlserver>(*it);
		mysqlreps[s->name] = "\002" + s->name + "\002: " + s->fmt4irc() + " ";
		mysqltotal += s->qpsv;
	}

	/* do master first, then the rest */
	try {
		mastername = SMI(smcfg::cfg)->fetchstr("/monit/mysql/master");
		std::map<std::string,std::string>::iterator it = mysqlreps.find(mastername);
		if (it != mysqlreps.end()) {
			mysqlrep += it->second;
			mysqlreps.erase(it);
		}
	} catch (smcfg::nokey&) {}

	for (std::map<std::string, std::string>::iterator it = mysqlreps.begin(),
	     end = mysqlreps.end(); it != end; ++it)
		mysqlrep += it->second;

	mysqlrep += b::io::str(b::format(" total qps: %d") % mysqltotal);
	SMI(smirc::cfg)->conn()->msg(3, "\002mysql\002 ["+cluster+"]: " + mysqlrep);

	/* squids */
	int squidreqs = 0, squidhits = 0;
	FE_TC_AS(std::set<serverp>, hosts, i) {
		if ((*i)->type() != "Squid")
			continue;
		b::shared_ptr<squidserver> s = b::dynamic_pointer_cast<squidserver>(*i);
		squidrep += "\002" + s->name + "\002: ";
		squidrep += s->fmt4irc() + " ";
		squidreqs += s->rpsv;
		squidhits += s->hpsv;
	}

	float squidperc;
	if (squidhits && squidreqs)
		squidperc = (float(squidhits)/squidreqs)*100;
	else squidperc = 0;
	squidrep += b::io::str(b::format(
			"\002total:\002 \00311\002\002%d\003/\00303\002\002%d\003/"
			"\0036\002\002%.02f%%\003")
		% squidreqs % squidhits % squidperc);
	SMI(smirc::cfg)->conn()->msg(3, "\002squid\002 ["+cluster+"]: " + squidrep);
}

void
cfg::state_transition(str serv, server::state_t oldstate, server::state_t newstate)
{
	std::string oldstatename = server::statestring(oldstate),
		newstatename = server::statestring(newstate);
	SMI(smlog::log)->logmsg(10, SM$FAC_MONIT, SM$MSG_TRANSIT, serv, oldstatename, newstatename);
}

bool
cfg::knowntype(str type)
{
	return type == "squid" || type == "mysql" || type == "none";
}

bool
cfg::server_exists(str serv)
{
	return serverlist.find(serv) != serverlist.end();
}

void
cfg::create_server(str serv, str type, bool addconf)
{
	if (server_exists(serv)) return; /* XXX error? */
	try {
		server* s = server_fortype(type, serv);
		serverlist[serv] = serverp(s);
		if (addconf) {
			SMI(smcfg::cfg)->addlist("/monit/servers", serv);
			SMI(smcfg::cfg)->storestr("/monit/server/"+serv+"/type", type);
		}
	} catch (notype&) {
		/* XXX error? */
		return;
	}
}

void
cfg::remove_server(str serv)
{
    if (!server_exists(serv)) return;
    serverlist.erase(serv);
    SMI(smcfg::cfg)->dellist("/monit/servers", serv);
}

void
cfg::set_cluster(str serv, str cluster)
{
    if (!server_exists(serv))
        return;
    serverlist[serv]->cluster = cluster;
    SMI(smcfg::cfg)->storestr("/monit/server/" + serv + "/cluster", cluster);
}

server*
cfg::server_fortype(str type, str name)
{
	if (type == "squid") return new squidserver(name);
	if (type == "mysql") return new mysqlserver(name);
	if (type == "none") return new noneserver(name);
	throw notype();
}

} // namespace smmon
