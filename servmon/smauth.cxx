#include "smstdinc.hxx"
#include "smcfg.hxx"
#include "smauth.hxx"

namespace smauth {

bool login_usr(std::string const& usr, std::string const& pass)
{
	std::string rpass;
	
	try {
		rpass = SMI(smcfg::cfg)->fetchstr(str(format("/users/%s/password") % usr));
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
	
} // namespace smauth
