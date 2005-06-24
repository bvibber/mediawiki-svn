/* @(#) $Header$ */
#ifndef SM_SMMON_HXX_INCLUDED_
#define SM_SMMON_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smthr.hxx"
#include "smmysql.hxx"
#include "smserver.hxx"

namespace smmon {

struct notype : public std::runtime_error {
	notype() : std::runtime_error("monitor type does not exist") {}
};
struct noserv : public std::runtime_error {
	noserv() : std::runtime_error("server does not exist") {}
};

class cfg : public smutl::singleton<cfg> {
public:
	void initialise(void);
	void chk(void);

	bool knowntype(str type);
	bool server_exists(str serv);
	void create_server(str serv, str type, bool addconf = true);
        void remove_server(str serv);
        void set_cluster(str serv, str cluster);

	std::map<std::string, serverp> const& servers() const {
		return serverlist;
	}
	std::map<std::string, serverp>& servers() {
		return serverlist;
	}
	serverp const serv(str name) const {
		std::map<std::string, serverp>::const_iterator it = serverlist.find(name);
		if (it == serverlist.end()) throw noserv();
		return it->second;
	}
	struct checker : smthr::thrbase {
		void start();
		void chk1();
		void irc_print_cluster(str cluster, std::set<serverp>& hosts);
		void check_loop(void);
		b::try_mutex chk_m;
		virtual ~checker() {}
	};

        void state_transition(str serv, server::state_t oldstate, server::state_t newstate);

private:
	server* server_fortype(str type, str name);
        std::string get_option(str server, str option, str type);
	std::map<std::string, serverp> serverlist;
	mysqlclientp sqlp;
};

} // namespace smmon

#endif
