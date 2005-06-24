/* @(#) $Header $ */

#include "smstdinc.hxx"
#include "smmon.hxx"
#include "smsnmp.hxx"

/* this should be in smstdinc, but i'd rather not pollute the entire
   namespace with C crud */
#include <net-snmp/net-snmp-config.h>
#include <net-snmp/net-snmp-includes.h>

snmpclient::snmpclient(str host_, int port_)
: host(host_)
, port(port_)
, hostport(b::io::str(b::format("%s:%d") % host_ % port_))
{}

b::any
snmpclient::getoid(str oidname) {
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
	session.version = SNMP_VERSION_1;
	session.remote_port = port;
	session.community = reinterpret_cast<u_char *>(const_cast<char *>("public"));
	session.community_len = strlen(reinterpret_cast<char const *>(session.community));
	session.retries = 3;
	session.timeout = 1000000 * 10;

	ss = snmp_open(&session);
	if (!ss) {
		snmp_perror("snmp");
		return b::any();
	}

	pdu = snmp_pdu_create(SNMP_MSG_GET);
	read_objid(oidname.c_str(), anoid, &anoidlen);
	snmp_add_null_var(pdu, anoid, anoidlen);
	status = snmp_synch_response(ss, pdu, &response);
	b::any res;
	if (status == STAT_SUCCESS && response->errstat == SNMP_ERR_NOERROR) {
		vars = response->variables;
		if (vars->type == ASN_OCTET_STR) {
			std::string s;
			s.assign(vars->val.string, vars->val.string + vars->val_len);
			res = s;
		} else if (vars->type == 0x41) { /* Counter32 */
			uint32_t i = 0;
			memcpy(&i, vars->val.bitstring, std::max(std::size_t(4), vars->val_len));
			res = i;
		} else if (vars->type == ASN_INTEGER) {
			res = *vars->val.integer;
		}
	} else {
		if (status == STAT_SUCCESS) {
			std::string error = "SNMP error for host " + hostport + ": ";
			error += snmp_errstring(response->errstat);
			//SMI(smlog::log)->logmsg(0, error);
		} else {
			std::string error = "SNMP error for host " + hostport + ": ";
			char           *err;
			snmp_error(ss, NULL, NULL, &err);
			error += err;
			SNMP_FREE(err);
			//SMI(smlog::log)->logmsg(0, error);
		}
	}
	snmp_free_pdu(response);
	snmp_close(ss);
	return res;
}
