#include "smstdinc.hxx"
#include "smcsmplex.hxx"
#include "smnet.hxx"
#include "smtrm.hxx"

namespace csmplex {

csmplexd::csmplexd(void)
{
	// no-op
}

void csmplexd::start(void)
{
	smnet::inetlsnrp s (new smnet::inetlsnr);
	s->svc("5050");
	try {
		s->lsn();
	} catch (smnet::sckterr& e) {
		std::cerr << "listen failed: " << e.what() << '\n';
		return;
	}

	boost::function<void(smnet::inetlsnrp, int)> f = 
		boost::bind(&csmplexd::newc, this, _1, _2);
	SMI(smnet::smpx)->add(f, s, smnet::smpx::srd);
}

void csmplexd::newc(smnet::inetlsnrp s, int)
{
	try {
		smnet::inetclntp c = s->wt_acc();
		smnet::inettnsrvp tns (new smnet::inettnsrv(c));
		//smtrm::inettrmsrvp trm (new smtrm::inettrmsrv (tns));
		smtrm::inettrmsrv* trm (new smtrm::inettrmsrv (tns));
		trm->start();
	} catch (smnet::sckterr& e) {
		std::cerr << "accept failed: " << e.what() << '\n';
		return;
	}
}

} // namespace csmplex

