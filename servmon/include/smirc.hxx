#ifndef SM_SMIRC_HXX_INCLUDED_
#define SM_SMIRC_HXX_INCLUDED_

#include "smstdinc.hxx"

namespace smirc {

class irccfg {
public:
	void newserv_or_chgnick(std::string const& server, std::string const& nick);
	bool server_exists(std::string const& server);
	void server_set_secnick(std::string const& server, std::string const& nick);
};

extern irccfg cfg;

} // namespace smirc

#endif
