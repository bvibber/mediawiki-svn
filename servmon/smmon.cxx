#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smmon.hxx"
#include "smtmr.hxx"
#include "smcfg.hxx"
#include "smirc.hxx"

/* this should be in smstdinc, but i'd rather not pollute the entire
   namespace with C crud */
#include <net-snmp/net-snmp-config.h>
#include <net-snmp/net-snmp-includes.h>

#include <mysql/mysql.h>

namespace smmon {

class snmpclient {
public:
	snmpclient(str host_, int port_ = 161)
		: host(host_)
		, port(port_)
		, hostport(b::io::str(b::format("%s:%d") % host_ % port_))
		{}

	b::any getoid(str oidname) {
		snmp_session   session, *ss;
		snmp_pdu      *pdu, *response;
		oid            anoid[MAX_OID_LEN];
		size_t         anoidlen = MAX_OID_LEN;
		variable_list *vars;
		int            status;
	       
		static bool init = false;

		if (!init)
			init_snmp("servmon"), init++;

		snmp_sess_init(&session);
		session.peername = const_cast<char *>(hostport.c_str());
		std::cerr << "using port: " << port << "\n";
		session.version = SNMP_VERSION_1;
		session.remote_port = port;
		session.community = reinterpret_cast<u_char *>(const_cast<char *>("public"));
		session.community_len = strlen(reinterpret_cast<char const *>(session.community));

		ss = snmp_open(&session);
		if (!ss) {
			snmp_perror("snmp");
			return b::any();
		}

		pdu = snmp_pdu_create(SNMP_MSG_GET);
		std::cerr << "port now: " << session.remote_port << '\n';
		read_objid(oidname.c_str(), anoid, &anoidlen);
		snmp_add_null_var(pdu, anoid, anoidlen);
		status = snmp_synch_response(ss, pdu, &response);
		if (status == STAT_SUCCESS && response->errstat == SNMP_ERR_NOERROR) {
			vars = response->variables;
			print_variable(vars->name, vars->name_length, vars);
			b::any res;
			std::cerr << "get a reply of type: " << int(vars->type) << "\n";
			if (vars->type == ASN_OCTET_STR) {
				std::string s;
				s.assign(vars->val.string, vars->val.string + vars->val_len);
				res = s;
				std::cerr << "result type str: " << s << "\n";
			} else if (vars->type == 0x41) { /* Counter32 */
				uint32_t i = 0;
				memcpy(&i, vars->val.bitstring, std::max(std::size_t(4), vars->val_len));
				res = i;
				std::cerr << "result type counter32: " << i << "\n";
			} else if (vars->type == ASN_INTEGER) {
				res = *vars->val.integer;
				std::cerr << "result type int: " << *vars->val.integer << "\n";
			}
			return res;
		} else {
			if (status == STAT_SUCCESS)
				std::cerr << snmp_errstring(response->errstat);
			else
				snmp_sess_perror("snmpget", ss);
			return b::any();
		}
	}
		
private:
	std::string host;
	int port;
	std::string hostport;
};

struct mysqlerr : public std::runtime_error {
	mysqlerr(str s) : std::runtime_error(s) {}
};
	
class mysqlclient {
public:
	mysqlclient(str host_, int port_ = 0)
	: host(host_)
	, port(port_)
	, connected(false)
	{}
	void connect(void) {
		mysql_init(&connection);
		mysql_options(&connection, MYSQL_READ_DEFAULT_GROUP, "servmon");
		unsigned int tm = 5;
		mysql_options(&connection, MYSQL_OPT_CONNECT_TIMEOUT, reinterpret_cast<char const *>(&tm));
		std::string user, pass;
		try {
			user = SMI(smcfg::cfg)->fetchstr("/monit/mysql/username");
			pass = SMI(smcfg::cfg)->fetchstr("/monit/mysql/password");
		} catch (smcfg::nokey&) {
			throw mysqlerr("username/password not specified");
		}
		if (!mysql_real_connect(&connection, host.c_str(), user.c_str(), pass.c_str(),
					NULL, port, NULL, 0)) {
			throw mysqlerr(mysql_error(&connection));
		}
		connected = true;
	}
	typedef std::map<std::string, std::string> row;
	typedef std::vector<row> resultset;
	resultset query(str query) {
		if (!connected) connect();
		int ret = mysql_real_query(&connection, query.c_str(), query.size());
		if (ret) throw mysqlerr(mysql_error(&connection));
		MYSQL_RES *res = mysql_store_result(&connection);
		MYSQL_ROW mr;
		MYSQL_FIELD *fields = mysql_fetch_fields(res);
		resultset resset;
		int numrows = mysql_num_fields(res);
		while (mr = mysql_fetch_row(res)) {
			row r;
			unsigned long *lengths = mysql_fetch_lengths(res);
			for (int i = 0; i < numrows; ++i) {
				std::string cname = fields[i].name;
				std::string data;
				if (mr[i])
					data.assign(mr[i], mr[i] + lengths[i]);
				r[cname] = data;
			}
			resset.push_back(r);
		}
				
		mysql_free_result(res);
		return resset;
	}
	static mysqlclientp forhost(str host) {
		std::map<std::string, mysqlclientp>::iterator it = clients.find(host);
		if (it != clients.end()) return it->second;
		return clients[host] = mysqlclientp(new mysqlclient(host));
	}
private:
	static std::map<std::string, mysqlclientp> clients;
	bool connected;
	
	std::string host;
	int port;
	MYSQL connection;
};
std::map<std::string, mysqlclientp> mysqlclient::clients;
	
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
cfg::initialise(void)
{
	try {
		std::set<std::string> servers = SMI(smcfg::cfg)->fetchlist("/monit/servers");
		FE_TC_AS(std::set<std::string>, servers, i) {
			create_server(*i, SMI(smcfg::cfg)->fetchstr("/monit/server/"+*i+"/type"));
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
		std::cerr << "warning: could not lock mutex for check\n";
		return;
	}
}

void
cfg::checker::start(void)
{
	std::cerr << "checker starting...\n";
	for (;;) {
		sleep(5);
		std::cerr << "checker iter...\n";
		chk1();

		/* IRC report */
		std::string rep;
		b::try_mutex::scoped_lock m (chk_m);
		std::map<std::string, serverp>& serverlist = SMI(cfg)->servers();

		/* squids */
		int squidreqs = 0, squidhits = 0;
		for(std::map<std::string, serverp>::const_iterator
			    it = serverlist.begin(), end = serverlist.end(); it != end; ++it) {
			if (it->second->type() != "Squid") continue;
			b::shared_ptr<squidserver> s = b::dynamic_pointer_cast<squidserver>(it->second);
			rep += "\002" + it->first + "\002: ";
			rep += s->fmt4irc() + " ";
			squidreqs += s->rpsv;
			squidhits += s->hpsv;
		}
		float squidperc;
		if (squidhits && squidreqs)
			squidperc = (float(squidhits)/squidreqs)*100;
		else squidperc = 0;
		rep += b::io::str(b::format("\002total:\002 \00311%d\003/\00303%d\003/\0036%.02f%%\003") % squidreqs % squidhits % squidperc);

		/* mysql */
		for(std::map<std::string, serverp>::const_iterator
			    it = serverlist.begin(), end = serverlist.end(); it != end; ++it) {
			if (it->second->type() != "MySQL") continue;
			b::shared_ptr<mysqlserver> s = b::dynamic_pointer_cast<mysqlserver>(it->second);
			rep += " \002" + it->first + "\002: " + s->fmt4irc();
		}
		SMI(smirc::cfg)->conn()->msg(rep);
	}
}

void
cfg::squidserver::check(void) {
	std::cerr << "squid: checking " << name << '\n';
	snmpclient c(name, 3401);
	uint32_t requests, hits;
	try {
		requests = b::any_cast<uint32_t>(c.getoid("1.3.6.1.4.1.3495.1.3.2.1.1"));
		hits = b::any_cast<uint32_t>(c.getoid("1.3.6.1.4.1.3495.1.3.2.1.2"));
	} catch (b::bad_any_cast&) {
		std::cerr << "cast failed...\n";
		return;
	}
	rpsv = rps.val(requests);
	hpsv = hps.val(hits);
}

void
cfg::mysqlserver::check(void)
{
	std::cerr << "mysql: checking " << name << "\n";
	mysqlclientp client = mysqlclient::forhost(name);
	uint32_t queries;
	try {
		mysqlclient::resultset res = client->query("SHOW STATUS LIKE 'QUESTIONS'");
		if (res.size() < 1) {
			std::cerr << "oh no, didn't get a result\n";
			queries = 0;
		} else {
			try {
				std::cerr << "value: [" << res[0]["Value"] << "]\n";
				queries = b::lexical_cast<uint32_t>(res[0]["Value"]);
			} catch (b::bad_lexical_cast&) {
				std::cerr << "cast failed...\n";
				queries = 0;
			}
		}
	} catch (mysqlerr& e) {
		std::cerr << "mysql err: " << e.what() << "\n";
	}
	qpsv = qps.val(queries);
}

std::string
cfg::mysqlserver::fmt4irc(void) const
{
	std::string rep = b::io::str(b::format("\00311%d\003") % qpsv);
	return rep;
}
	
std::string
cfg::squidserver::fmt4irc(void) const
{
	float perc;
	if (rpsv && hpsv)
		perc = (float(hpsv)/rpsv)*100;
	else perc = 0;
	std::string rep = b::io::str(b::format("\00311%d\003/\00303%d\003/\0036%.02f%%\003") % rpsv % hpsv % perc);
	return rep;
}

bool
cfg::knowntype(str type)
{
	return type == "squid" || type == "mysql";
}

bool
cfg::server_exists(str serv)
{
	return serverlist.find(serv) != serverlist.end();
}

void
cfg::create_server(str serv, str type)
{
	if (server_exists(serv)) return; /* XXX error? */
	try {
		server* s = server_fortype(type, serv);
		serverlist[serv] = serverp(s);
		SMI(smcfg::cfg)->addlist("/monit/servers", serv);
		SMI(smcfg::cfg)->storestr("/monit/server/"+serv+"/type", type);
	} catch (notype&) {
		/* XXX error? */
		return;
	}
}

cfg::server*
cfg::server_fortype(str type, str name)
{
	if (type == "squid") return new squidserver(name);
	if (type == "mysql") return new mysqlserver(name);
	throw notype();
}
	
} // namespace smmon
