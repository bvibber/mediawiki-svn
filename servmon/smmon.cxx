#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smmon.hxx"
#include "smtmr.hxx"
#include "smcfg.hxx"

/* this should be in smstdinc, but i'd rather not pollute the entire
   namespace with C crud */
#include <net-snmp/net-snmp-config.h>
#include <net-snmp/net-snmp-includes.h>

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
				int i;
				memcpy(&i, vars->val.bitstring, vars->val_len);
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
	
void
cfg::initialise(void)
{
//	SMI(smtmr::evthdlr)->install(smtmr::evtp(new smtmr::evt("Monitor: periodic check", 30, true,
//								b::bind(&smmon::chk, this))));
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
	}
}

void
cfg::squidserver::check(void) {
	std::cerr << "squid: checking " << name << '\n';
	snmpclient c(name, 3401);
	int requests;
	try {
		requests = b::any_cast<int>(c.getoid("1.3.6.1.4.1.3495.1.3.2.1.1"));
	} catch (b::bad_any_cast&) {
		std::cerr << "cast failed...\n";
		return;
	}
	std::cerr << "\trequests: " << requests << '\n';
}
       
bool
cfg::knowntype(str type)
{
	return type == "squid";
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
	throw notype();
}
	
} // namespace smmon
