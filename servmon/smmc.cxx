/* @(#) $Header$ */
#include "smmc.hxx"
#include "smcfg.hxx"

#include <memcache.h>

namespace smmc {

mc::mc()
{
	mcp = mc_new();
}
	
void
mc::reload_servers(void)
{
	std::string servercmd = SMI(smcfg::cfg)->fetchstr("/mc/servercmd");
	std::vector<std::string> servers = smutl::snarf(servercmd);
	
	FE_TC_AS(std::vector<std::string>, servers, i)
		add_server(*i);
}

void
mc::add_server(str server)
{
	mc_server_add4(mcp, server.c_str());
}
	
void
mc::initialise(void)
{
	reload_servers();
}

std::string
mc::get(str key)
{
	void *p = mc_aget(mcp, key.c_str(), key.size());
	std::string r = static_cast<char const *>(p);
	std::free(p);
	return r;
}

} // namespace smmc
