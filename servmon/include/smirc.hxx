/* @(#) $Header$ */
#ifndef SM_SMIRC_HXX_INCLUDED_
#define SM_SMIRC_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smnet.hxx"

namespace smirc {

struct irctrmsrv;
	
class ircclnt {
public:
	ircclnt(std::string const& serv, int port);
	ircclnt();
	~ircclnt();
	
	void nick(str pnick);
	void nick(str pnick, str snick);

	void join(str channel);
	void part(str channel);

	void msg(int level, str message);
	void msg(str channel, str message);

	void command_reply(str msg);
	
private:
	friend class irctrmsrv;
	
	bool rdline(strr l);
	void doregister(void);
	void data_cb(int what);
	void connected(void);
	void parseline(std::string line);

	struct cbdata {
		std::string command;
		std::string prefix;
		std::vector<std::string> args;
	};
	void cb_001(cbdata&);
	void cb_ping(cbdata&);
	void cb_privmsg(cbdata&);
	typedef boost::function<void(cbdata&)> cbtype;

	std::map<std::string, cbtype> cbs;
	std::string name;
	std::string linebuf;
	std::string mynick;
	std::string replyto;
	bool cip;
	smnet::clntp sckt;
	std::string pnick, snick;

	/* stuff for IRC command execution */
	irctrmsrv *trmpimpl;
};

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

	void channel(str channel);
	bool nochannel(str channel);
	void channel_level(str channel, int level);
	
	ircclntp conn();

private:
	friend class ircclnt;
	
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
