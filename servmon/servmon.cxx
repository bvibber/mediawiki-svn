#include "smstdinc.hxx"
#include "smcsmplex.hxx"
#include "smthr.hxx"
#include "smcfg.hxx"

int
main(int argc, char *argv[])
{
	instance<smcfg::cfg>(); // force reading
	csmplex::csmplexd cm;
	cm.start();
	SMI(smnet::smpx)->run();
}

