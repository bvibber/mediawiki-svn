/* @(#) $Header$ */
#ifndef SM_SMMC_HXX_INCLUDED_
#define SM_SMMC_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

struct memcache;

namespace smmc {

/* memcached client */       
class mc : public smutl::singleton<mc> {
public:
	mc();
	void initialise(void);
	void reload_servers(void);
	
	std::string get(str key);
	
private:
	void add_server(str server);
	struct memcache *mcp;
};

} // namespace smmc

#endif
