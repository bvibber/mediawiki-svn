#ifndef SM_SMIRC_HXX_INCLUDED_
#define SM_SMIRC_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smirc {

class cfg : public smutl::singleton<cfg> {
public:
	void newserv_or_chgnick(str server, str nick);
	bool server_exists(str server);
	void server_set_secnick(str server, str nick);
	void remove_server(str server);
	
	void enable_server(str server, bool ebl);
	bool server_enabled(str server);

private:
	bool getkeybool(str server, str key);
	str getkeystr(str server, str key);
	int getkeyint(str server, str key);

	void setkeybool(str server, str key, bool value);
	void setkeystr(str server, str key, str value);
	void setkeyint(str server, str key, int value);
};

} // namespace smirc

#endif
