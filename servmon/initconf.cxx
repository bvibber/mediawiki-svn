#include "smstdinc.hxx"
#include "smcfg.hxx"
#include "smauth.hxx"

std::string cfgfile = PFX "/servmon.cfg";

int main(void)
{
	smcfg::cfg& c = *instance<smcfg::cfg>();
	smauth::add_usr("system", "default");
	c.storestr("/core/enable_password", "default");
	std::cout << "wrote initial configuration file to " << cfgfile << "\n";
}
