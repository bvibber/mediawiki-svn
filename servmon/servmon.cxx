/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smcsmplex.hxx"
#include "smthr.hxx"
#include "smcfg.hxx"
#include "smtmr.hxx"
#include "smirc.hxx"
#include "smmon.hxx"
#include "smmc.hxx"
#include "smauth.hxx"

void
initconf(void)
{
	smcfg::cfg& c = *SMI(smcfg::cfg);
	smauth::add_usr("system", "default");
	c.storestr("/core/enable_password", "default");
	std::cout << "% Wrote initial configuration file.\n";
}

int
main(int argc, char *argv[])
{
	if (argc > 1 && !strcmp(argv[1], "-initconf")) {
		initconf();
		return 0;
	}
	       
	SMI(smcfg::cfg); // force reading
	SMI(smirc::cfg)->initialise();
	SMI(smmon::cfg)->initialise();
	SMI(smmc::mc)->initialise();
	csmplex::csmplexd cm;
	cm.start();
	SMI(smnet::smpx)->run();
}
