#ifndef SM_SMIRC_HXX_INCLUDED_
#define SM_SMIRC_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smirc {

class ircclnt;
typedef b::shared_ptr<ircclnt> ircclntp;

class cfg : public smutl::singleton<cfg> {
public:
	cfg()
	: cip(false)
	, connected(false)
	, srv_iter(servers.end())
	{}

	void initialise(void);

	void newserv_or_chgnick(str server, str nick);
	bool server_exists(str server);
	void server_set_secnick(str server, str nick);
	void remove_server(str server);
	
	void enable_server(str server, bool ebl);
	bool server_enabled(str server);

private:
	bool cip, connected;
	void chk(void);
	ircclntp connection;

	void connect(void);
	bool next_server(void);
	void srv_reset(void);
	void get_servers(void);

	std::set<std::string> servers;
	std::set<std::string>::const_iterator srv_iter;

	bool getkeybool(str server, str key);
	str getkeystr(str server, str key);
	int getkeyint(str server, str key);

	void setkeybool(str server, str key, bool value);
	void setkeystr(str server, str key, str value);
	void setkeyint(str server, str key, int value);
};

} // namespace smirc

#endif
