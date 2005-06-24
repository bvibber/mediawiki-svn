/* $Header$ */

#include "smserver.hxx"
#include "smutl.hxx"
#include "smcfg.hxx"
#include "smnet.hxx"
#include "smalrm.hxx"
#include "smmon.hxx"
#include "smsnmp.hxx"

server::server(str name_)
: name(name_)
, state(state_unknown)
, nups(0)
, ndowns(0)
, flapstate(0)
{}

xomitr::xomitr() : v(0), l(0) {}

uint32_t
xomitr::val(uint32_t newval)
{
	std::time_t then = l;
	if (v == 0) v = newval;
	std::time_t now = std::time(0);
	l = now;
	uint64_t q = v, nv = newval;
	v = nv;
	if (nv < q)
		nv = uint64_t(4294967296LL) + nv;
	return (nv - q) / (now - then);
}

void
server::_stdchecks(void)
{
	smnet::clntp s (new smnet::clnt);
	s->setblocking(true);
	s->svc("8576");
	s->node(name);
	std::vector<u_char> data;
	try {
		s->connect();
		s->rd(data);
	} catch (std::exception&) {
		return;
	}
	std::stringstream strm;
	std::copy(data.begin(), data.end(), std::ostream_iterator<u_char>(strm));
	std::string part, spc, blocksz;
	while (strm >> part >> spc >> blocksz) {
		uint64_t space = lexical_cast<uint64_t>(spc) * lexical_cast<uint64_t>(blocksz);
		space /= 1024 * 1024; /* MB */
		SMI(smalrm::mgr)->value(name, "disk free for "+name+":" + part + " (MB)", space);
	}
}

void
server::check(void)
{
	state_t oldstate = state, newstate;
	nups = ndowns = 0;
	_check();
	_stdchecks();

	if (nups && ndowns) {
		/* some checks succeeded and others failed.
		   place server in fast-flap state */
		newstate = state_fast_flap;
	} else if (nups) {
		/* all services up. */
		newstate = state_up;
	} else if (ndowns) {
		/* all services down */
		newstate = state_down;
	} else {
		/* no services either up or down.  this probably means
		   we don't know about any checks for this server type,
		   so leave it as unknown. */
		newstate = state_unknown;
	}

	/*
	 * ignore any transition from unknown. these are normally
	 * uninteresting.
	 */
	if (newstate != oldstate && oldstate != state_unknown) {
		/*
		 * remove any outdated flaps from the list, and insert
		 * a new one.
		 *
		 * this gives us the number of flaps over the last 5 minutes.
		 */
		std::time_t now = std::time(0), last = now - (60 * 5);

		FE_TC_AS(std::set<std::time_t>, flaps, i) {
			if (*i < last)
				flaps.erase(i);
			else break;
		}
		flaps.insert(std::time(0));
		int nflaps = flaps.size();

		if (nflaps > 2) {
			/* flapping */
			if (!flapstate)
				SMI(smmon::cfg)->state_transition(name, oldstate,
					state_slow_flap);
			newstate = state_slow_flap;
			flapstate = 1;
		} else {
			/* only 1 change in last 5 mins, not flapping */
			SMI(smmon::cfg)->state_transition(name, oldstate, newstate);
			flapstate = 0;
		}
	}
	state = newstate;
}

bool
server::is(server::state_t s) const
{
	return state == s;
}

void
server::markup(void)
{
	nups++;
}

void
server::markdown(void)
{
	ndowns++;
}

std::string
server::statestring(state_t s)
{
	switch (s) {
	case state_up:
		return "UP";
	case state_down:
		return "DOWN";
	case state_fast_flap:
		return "FAST-FLAP";
	case state_slow_flap:
		return "FLAP";
	case state_unknown:
		return "UNKNOWN";
	default:
		return "<unknown state>";
	}
}


void
squidserver::_check(void) {
	snmpclient c(name, 3401);
	uint32_t requests, hits;
	try {
		requests = b::any_cast<uint32_t>(c.getoid("1.3.6.1.4.1.3495.1.3.2.1.1"));
		hits = b::any_cast<uint32_t>(c.getoid("1.3.6.1.4.1.3495.1.3.2.1.2"));
	} catch (b::bad_any_cast&) {
		markdown();
		return;
	}
	markup();
	rpsv = rps.val(requests);
	hpsv = hps.val(hits);
}

uint32_t
mysqlserver::getqueries(void)
{
	mysqlclientp client = getconn();
	uint32_t queries;
	try {
		mysqlclient::resultset res = client->query("SHOW STATUS LIKE 'QUESTIONS'");
		if (res.size() < 1) {
			SMI(smlog::log)->debug(smlog::mysql_monitoring, "Did not get a result for SHOW STATUS command on " + name);
			queries = 0;
		} else {
			try {
				queries = b::lexical_cast<uint32_t>(res[0]["Value"]);
			} catch (b::bad_lexical_cast&) {
				queries = 0;
			}
		}
	} catch (mysqlerr&) {
		markdown();
		return 0;
	}
	markup();
	return queries;
}

mysqlclientp
mysqlserver::getconn(void)
{
	return mysqlclient::forhost(name);
}

uint32_t
mysqlserver::getnumprocesses(void)
{
	mysqlclientp client = getconn();
	mysqlclient::resultset res;
	try {
		res = client->query("SHOW PROCESSLIST");
	} catch (mysqlerr& e) {
		SMI(smlog::log)->debug(smlog::mysql_monitoring, "MySQL connection error for " + name + ": " + e.what());
		markdown();
		return 0;
	}
	int numproc = 0;
	for (uint i = 0; i < res.size(); ++i) {
		if ((res[i]["User"] != "repl" && res[i]["User"] != "system user") && res[i]["Command"] != "Sleep")
			numproc++;
	}
	markup();
	return numproc;
}

void
mysqlserver::_check(void)
{
	uint32_t queries = getqueries();
	qpsv = qps.val(queries);
	procv = getnumprocesses();
	replag = getreplag();
	SMI(smalrm::mgr)->value(name, "running threads", procv);
	SMI(smalrm::mgr)->value(name, "replication lag", replag);
}

std::time_t
mysqlserver::getmasterpos(void)
{
	std::string mastername;
	try {
		mastername = SMI(smcfg::cfg)->fetchstr("/monit/mysql/master");
	} catch (smcfg::nokey&) {
		return 0;
	}
	mysqlclientp client = mysqlclient::forhost(mastername);
	mysqlclient::resultset r;
	try {
		r = client->query("SELECT MAX(rc_timestamp) AS ts FROM enwiki.recentchanges");
	} catch (mysqlerr& e) {
		SMI(smlog::log)->debug(smlog::mysql_monitoring, "MySQL query failed for replication lag on " + name + ": " + e.what());
		return 0;
	}
	if (r.size() < 1) return 0;
	return smutl::wf2time_t(r[0]["ts"]);
}

std::time_t
mysqlserver::getmypos(void)
{
	mysqlclientp client = getconn();
	mysqlclient::resultset r;
	try {
		r = client->query("SELECT MAX(rc_timestamp) AS ts FROM enwiki.recentchanges");
	} catch (mysqlerr& e) {
		SMI(smlog::log)->debug(smlog::mysql_monitoring, "MySQL query failed for replication lag on " + name + ": " + e.what());
		markdown();
		return 0;
	}
	markup();
	if (r.size() < 1) return 0;
	return smutl::wf2time_t(r[0]["ts"]);
}

std::time_t
mysqlserver::getreplag(void)
{
	std::time_t masterpos = getmasterpos(), mypos = getmypos();
	SMI(smlog::log)->debug(smlog::mysql_monitoring,
			       b::io::str(b::format("MySQL replication lag for %s: masterpos=%d mypos=%d")
					  % name % masterpos % mypos));
	if (!masterpos || !mypos) return 0;
	return masterpos - mypos;
}

std::string
mysqlserver::fmt4irc(void) const
{
        if (is(state_down))
		return "\0034down\003";
	std::string rep = b::io::str(b::format("\00311\002\002%d\003/\00303\002\002%d\003") % procv % qpsv);
	try {
		if (SMI(smcfg::cfg)->fetchstr("/monit/mysql/master") != name) {
			rep += b::io::str(b::format("/\00306\002\002%d\003") % replag);
		}
	} catch (smcfg::nokey&) {}
	return rep;
}

std::string
squidserver::fmt4irc(void) const
{
	if (is(state_down))
		return "\0034down\003";
	float perc;
	if (rpsv && hpsv)
		perc = (float(hpsv)/rpsv)*100;
	else perc = 0;
	std::string rep = b::io::str(b::format("\00311\002\002%d\003/\00303\002\002%d\003/\0036\002\002%.02f%%\003") % rpsv % hpsv % perc);
	return rep;
}
