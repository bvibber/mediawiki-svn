#include "smstdinc.hxx"
#include "smcsmplex.hxx"
#include "smnet.hxx"
#include "smtrm.hxx"

namespace csmplex {

template<class ntt>
class csmplexc : public smthr::thrbase {
public:
	csmplexc(smnet::clnt<ntt> nt_)
	: nt(nt_)
	{}
	virtual ~csmplexc(void) {}
 
	void start(void) {
		std::cerr << "accepting a client\n";
		smnet::tnsrv<smnet::inet> tn(nt);
		smtrm::trmsrv<smnet::tnsrv<smnet::inet> > trm(tn);
		trm.run();
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

