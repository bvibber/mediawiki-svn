#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smmon.hxx"

namespace smmon {

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
		server* s = server_fortype(type);
		serverlist[serv] = serverp(s);
	} catch (notype&) {
		/* XXX error? */
		return;
	}
}

cfg::server*
cfg::server_fortype(str type)
{
	if (type == "squid") return new squidserver;
	throw notype();
}
	
} // namespace smmon
