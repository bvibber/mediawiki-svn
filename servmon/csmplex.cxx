/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smcsmplex.hxx"
#include "smnet.hxx"
#include "smtrm.hxx"

namespace csmplex {

csmplexd::csmplexd(void)
{
	// no-op
}

void
csmplexd::start(void)
{
	smnet::lsnrp s (new smnet::lsnr);
	s->svc("5050");
	try {
		s->lsn();
	} catch (smnet::sckterr& e) {
		SMI(smlog::log)->logmsg(0, SM$FAC_TRM, SM$MSG_LSTFAIL, e.what());
		return;
	}

	boost::function<void(smnet::scktp, int)> f = 
		boost::bind(&csmplexd::newc, this, _1, _2);
	SMI(smnet::smpx)->add(f, static_pointer_cast<smnet::sckt>(s), smnet::smpx::srd);
}

void
csmplexd::newc(smnet::scktp s_, int)
{
	/* yuck */
	smnet::lsnrp s = dynamic_pointer_cast<smnet::lsnr>(s_);
	
	try {
		smnet::clntp c = s->wt_acc();
		smnet::tnsrvp tns (new smnet::tnsrv(c));
		smtrm::trmsrv* trm (new smtrm::trmsrv (tns));
		trm->start();
	} catch (smnet::sckterr& e) {
		SMI(smlog::log)->logmsg(0, SM$FAC_TRM, SM$MSG_ACCFAIL, e.what());
		return;
	}
}

} // namespace csmplex

