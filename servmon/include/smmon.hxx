/* @(#) $Header$ */
#ifndef SM_SMMON_HXX_INCLUDED_
#define SM_SMMON_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smthr.hxx"

namespace smmon {

struct notype : public std::runtime_error {
	notype() : std::runtime_error("monitor type does not exist") {}
};
struct noserv : public std::runtime_error {
	noserv() : std::runtime_error("server does not exist") {}
};

class xomitr {
public:
	xomitr();
	uint32_t val(uint32_t newval);
private:
	uint32_t v;
	std::time_t l;
};

class mysqlclient;
typedef b::shared_ptr<mysqlclient> mysqlclientp;
	
class cfg : public smutl::singleton<cfg> {
public:
	void initialise(void);
	void chk(void);
	
	bool knowntype(str type);
	bool server_exists(str serv);
	void create_server(str serv, str type, bool addconf = true);

	struct server {
		server(str name_) : name(name_), state(state_unknown) {}
		virtual std::string type(void) const = 0;
		virtual void check(void) = 0;
		virtual std::string fmt4irc(void) const = 0;
		virtual ~server() {}
		std::string name;
		enum state_t { state_up, state_down, state_unknown };
		state_t state;
		bool is(state_t s) const;
		void markup(void);
		void markdown(void);
		static std::string statestring(state_t s);
	};
	typedef b::shared_ptr<server> serverp;
	struct squidserver : public server {
		squidserver(str name) : server(name), rpsv(0), hpsv(0) {}
		std::string type(void) const { return "Squid"; }
		std::string fmt4irc(void) const;
		void check();
		xomitr rps, hps;
		uint32_t rpsv, hpsv;
	};
	struct mysqlserver : public server {
		mysqlserver(str name) : server(name), qpsv(0) {}
		std::string type(void) const { return "MySQL"; }
		std::string fmt4irc(void) const;
		void check();
		xomitr qps;
		uint32_t qpsv, procv;
		uint64_t replag;
		
		uint32_t getqueries(void);
		uint32_t getnumprocesses(void);
		uint64_t getmasterpos(void);
		uint64_t getmypos(void);
		uint64_t getreplag(void);
		
		mysqlclientp getconn(void);
		mysqlclientp clnt;
	};
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
		b::try_mutex chk_m;
		virtual ~checker() {}
	};

        void state_transition(str serv, server::state_t oldstate, server::state_t newstate);
	
private:
	server* server_fortype(str type, str name);
	std::map<std::string, serverp> serverlist;
	mysqlclientp sqlp;
};
	
} // namespace smmon

#endif
