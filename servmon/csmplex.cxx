#include "smstdinc.hxx"
#include "smcsmplex.hxx"
#include "smnet.hxx"

namespace csmplex {

template<class ntt>
class csmplexc : public smthr::thrbase {
public:
	csmplexc(smnet::clnt<ntt> nt_)
	: nt(nt_)
	{}
 
	void start(void) {
		std::cerr << "accepting a client\n";
		try {
			nt.wrt("accepted a client\r\n");
		} catch (smnet::sckterr& e) {
			std::cerr << "write error: " << e.what() << '\n';
		}
		smnet::tnsrv<smnet::inet> tn(nt);
		for (;;) {
			std::string ln;
			try {
				ln = tn.rdln();
			} catch (smnet::scktcls&) {
				break;
			} catch (smnet::tn2long&) {
				break;
			} catch (smnet::sckterr& e) {
				std::cerr << "error! " << e.what() << "\n";
				break;
			}
			std::cerr << "read: [" << ln << "]\n";
		}
		delete this;
	}
 
private:
	smnet::clnt<ntt> nt;
};

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
		csmplexc<smnet::inet> *ch = new csmplexc<smnet::inet>(c);
		ch->run();
	}
}

} // namespace csmplex

