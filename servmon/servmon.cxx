/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smcsmplex.hxx"
#include "smthr.hxx"
#include "smcfg.hxx"
#include "smtmr.hxx"
#include "smirc.hxx"
#include "smmon.hxx"

int
main(int argc, char *argv[])
{
	SMI(smcfg::cfg); // force reading
	SMI(smirc::cfg)->initialise();
	SMI(smmon::cfg)->initialise();
	csmplex::csmplexd cm;
	cm.start();
	SMI(smnet::smpx)->run();
}
