#include "smstdinc.hxx"
#include "smirc.hxx"
#include "smutl.hxx"
#include "smcfg.hxx"
#include "smnet.hxx"

namespace smirc {

class ircclnt {
public:
	ircclnt(std::string const& serv, int port) {
		sckt = smnet::inetclntp(new smnet::inetclnt);
		sckt->svc(lexical_cast<std::string>(port));
		sckt->endpt(serv);
		sckt->connect();
		boost::function<void(smnet::inetclntp, int)> f = 
			boost::bind(&ircclnt::data_cb, this, _2);
		SMI(smnet::smpx)->add(f, sckt, smnet::smpx::srd | smnet::smpx::swr);
		cip = true;
	}
	void data_cb(int what) {
		if (what == smnet::smpx::srd && cip) {
			std::cerr << "error connecting to server\n";
			delete this;
		}
		if (what == smnet::smpx::swr && cip) {
			std::cerr << "connected\n";
			return;
		}
	}
	
private:
	bool cip;
	smnet::inetclntp sckt;
};

void
cfg::newserv_or_chgnick(str server, str nick)
{
	if (!SMI(smcfg::cfg)->listhas("/irc/servers", server))
		SMI(smcfg::cfg)->addlist("/irc/servers", server);
	SMI(smcfg::cfg)->storestr(str(format("/irc/servers/%s/nickname") % server), nick);
}

void 
cfg::server_set_secnick(str server, str nick)
{
	if (!SMI(smcfg::cfg)->listhas("/irc/servers", server))
		return;
	SMI(smcfg::cfg)->storestr(str(format("/irc/servers/%s/secnickname") % server), nick);
}

bool 
cfg::server_exists(str server)
{
	return SMI(smcfg::cfg)->listhas("/irc/servers", server);
}

void 
cfg::remove_server(str server)
{
	try {
		SMI(smcfg::cfg)->dellist("/irc/servers", server);
	} catch (smcfg::nokey&) {
	}
}

void 
cfg::enable_server(str server, bool ebl) 
{
	setkeybool(server, "/irc/servers/%s/enabled", ebl);
}

bool 
cfg::server_enabled(str server)
{
	try {
		return getkeybool(server, "enabled");
	} catch (smcfg::nokey&) {
		return false;
	}
}

int 
cfg::getkeyint(str server, str key)
{
	return SMI(smcfg::cfg)->fetchint(str(format("/irc/server/%s/%s")
		% server % key));
}

bool 
cfg::getkeybool(str server, str key)
{
	return SMI(smcfg::cfg)->fetchbool(str(format("/irc/server/%s/%s")
		% server % key));
}

str
cfg::getkeystr(str server, str key)
{
	return SMI(smcfg::cfg)->fetchstr(str(format("/irc/server/%s/%s")
		% server % key));
}

void
cfg::setkeyint(str server, str key, int value)
{
	SMI(smcfg::cfg)->storeint(str(format("/irc/server/%s/%s")
		% server % key), value);
}

void
cfg::setkeystr(str server, str key, str value)
{
	SMI(smcfg::cfg)->storestr(str(format("/irc/server/%s/%s")
		% server % key), value);
}

void
cfg::setkeybool(str server, str key, bool value)
{
	SMI(smcfg::cfg)->storebool(str(format("/irc/server/%s/%s")
		% server % key), value);
}

} // namespace smirc
