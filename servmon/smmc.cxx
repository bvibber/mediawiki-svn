/* @(#) $Header$ */
#include "smmc.hxx"
#include "smcfg.hxx"

#include "l_memcache.h"

namespace smmc {

mc::mc()
{
	mcp = mc_new();
}
	
void
mc::reload_servers(void)
{
	std::string servercmd;
	try {
		servercmd = SMI(smcfg::cfg)->fetchstr("/mc/servercmd");
	} catch (smcfg::nokey&) {
		return;
	}
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
	void *p;
	uint32_t len;
	mc_aget(mcp, key.c_str(), key.size(), &p, &len);
	if (!p)
		throw nokey();
	char const *s = static_cast<char const *>(p);
	std::string r (s, s + len);
	std::cerr << "mc::get: r=["<<r<<"]\n";
	std::free(p);
	return r;
}

} // namespace smmc
