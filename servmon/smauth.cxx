#include "smstdinc.hxx"
#include "smcfg.hxx"
#include "smauth.hxx"

namespace smauth {

bool login_usr(std::string const& usr, std::string const& pass)
{
	std::string rpass;
	
	if (!usr_exists(usr)) return false;

	try {
		rpass = SMI(smcfg::cfg)->fetchstr(str(format("/core/users/%s/password") % usr));
	} catch (smcfg::nokey&) {
		return false;
	}

	return (pass == rpass);
}

bool authebl(std::string const& pass)
{
	std::string rpass;
	try {
		rpass = SMI(smcfg::cfg)->fetchstr("/core/enable_password");
	} catch (smcfg::nokey&) {
		return false;
	}
	return (pass == rpass);
}
	
bool usr_exists(std::string const& usr)
{
	return SMI(smcfg::cfg)->listhas("/core/users", usr);
}

void add_usr(std::string const& usr, std::string const& pass)
{
	if (!usr_exists(usr))
		SMI(smcfg::cfg)->addlist("/core/users", usr);
	SMI(smcfg::cfg)->storestr(str(format("/core/users/%s/password") % usr), pass);
}

void del_usr(std::string const& usr)
{
	SMI(smcfg::cfg)->dellist("/core/users", usr);
}

} // namespace smauth
