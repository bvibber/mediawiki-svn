#include "smstdinc.hxx"
#include "smcsmplex.hxx"
#include "smthr.hxx"
#include "smcfg.hxx"
#include "smtmr.hxx"
#include "smirc.hxx"

void
test_event(void)
{
	std::cerr << "test event ran at " << std::time(0) << '\n';
}

int
main(int argc, char *argv[])
{
	instance<smcfg::cfg>(); // force reading
	SMI(smtmr::evthdlr)->install(smtmr::evtp(new smtmr::evt("test event", 5, true, &test_event)));
	SMI(smirc::cfg)->initialise();
	csmplex::csmplexd cm;
	cm.start();
	SMI(smnet::smpx)->run();
}
