#ifndef SM_SMMON_HXX_INCLUDED_
#define SM_SMMON_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smmon {

struct notype : public std::runtime_error {
	notype() : std::runtime_error("monitor type does not exist") {}
};
struct noserv : public std::runtime_error {
	noserv() : std::runtime_error("server does not exist") {}
};
	
class cfg : public smutl::singleton<cfg> {
public:
	bool knowntype(str type);
	bool server_exists(str serv);
	void create_server(str serv, str type);

	struct server {
		virtual std::string type() const = 0;
	};
	typedef b::shared_ptr<server> serverp;
	struct squidserver : public server {
		std::string type() const { return "Squid"; }
	};
	std::map<std::string, serverp> const& servers() const {
		return serverlist;
	}
	serverp const serv(str name) const {
		std::map<std::string, serverp>::const_iterator it = serverlist.find(name);
		if (it == serverlist.end()) throw noserv();
		return it->second;
	}
	
private:
	server* server_fortype(str type);
	std::map<std::string, serverp> serverlist;
};
	
} // namespace smmon

#endif
