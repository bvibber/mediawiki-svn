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
	
class cfg : public smutl::singleton<cfg> {
public:
	void initialise(void);
	void chk(void);
	
	bool knowntype(str type);
	bool server_exists(str serv);
	void create_server(str serv, str type);

	struct server {
		server(str name_) : name(name_) {}
		virtual std::string type() const = 0;
		virtual void check() = 0;

		virtual ~server() {}
		std::string name;
	};
	typedef b::shared_ptr<server> serverp;
	struct squidserver : public server {
		squidserver(str name) : server(name) {}
		std::string type() const { return "Squid"; }
		void check();
		xomitr rps;
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
private:
	server* server_fortype(str type, str name);
	std::map<std::string, serverp> serverlist;
};
	
} // namespace smmon

#endif
