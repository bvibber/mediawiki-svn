#include "smstdinc.hxx"
#include "smcfg.hxx"

std::string cfgfile = PFX "/servmon.cfg";

int main(void)
{
	smcfg::cfg& c = *instance<smcfg::cfg>();
	c.storestr("/users/administrator/password", "default");
	std::cout << "wrote initial configuration file to " << cfgfile << "\n";
}
