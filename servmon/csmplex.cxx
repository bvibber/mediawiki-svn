#include "smstdinc.hxx"
#include "smcsmplex.hxx"
#include "smnet.hxx"

namespace csmplex {

csmplexd::csmplexd(void)
{
	// no-op
}

void csmplexd::start(void)
{
	smnet::lsnr<smnet::inet> s;
	s.svc("5050");
	try {
		s.lsn();
	} catch (smnet::sckterr& e) {
		std::cerr << "listen failed: " << e.what() << '\n';
		return;
	}

	for (;;) {
		smnet::clnt<smnet::inet> c;
		try { 
			c = s.wt_acc(); 
		} catch (smnet::sckterr& e) {
			std::cerr << "accept failed: " << e.what() << '\n';
			continue;
		}
	}
}

} // namespace csmplex

