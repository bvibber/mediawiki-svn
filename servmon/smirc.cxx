#include "smstdinc.hxx"
#include "smirc.hxx"
#include "smutl.hxx"
#include "smcfg.hxx"

namespace smirc {

irccfg cfg;

void irccfg::newserv_or_chgnick(std::string const& server, std::string const& nick)
{
	if (!instance<smcfg::cfg>()->listhas("/irc/servers", server))
		instance<smcfg::cfg>()->addlist("/irc/servers", server);
	instance<smcfg::cfg>()->storestr(str(format("/irc/servers/%s/nickname") % server), nick);
}

void irccfg::server_set_secnick(std::string const& server, std::string const& nick)
{
	if (!instance<smcfg::cfg>()->listhas("/irc/servers", server))
		return;
	instance<smcfg::cfg>()->storestr(str(format("/irc/servers/%s/secnickname") % server), nick);
}

bool irccfg::server_exists(std::string const& server)
{
	return instance<smcfg::cfg>()->listhas("/irc/servers", server);
}

} // namespace smirc
