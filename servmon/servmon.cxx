#include "smstdinc.hxx"
#include "smcsmplex.hxx"
#include "smthr.hxx"

int
main(int argc, char *argv[])
{
	csmplex::csmplexd cm;
	cm.run();

	instance<smthr::thrmgr>()->wait();
}

