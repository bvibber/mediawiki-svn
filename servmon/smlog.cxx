/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smlog.hxx"
#include "smirc.hxx"
#include "smthr.hxx"
#include "smtrm.hxx"

#include "msgtab.hxx"

namespace smlog {

namespace {
	struct loglsnd : smthr::daemon {
		loglsnd(void) {
		}
		
		void start(void) {
			smnet::lsnrp s (new smnet::lsnr(smnet::unix));
			std::remove("/tmp/servmon.log");
			s->node("/tmp/servmon.log");
			try {
				s->lsn();
			} catch (smnet::sckterr& e) {
				SMI(log)->logmsg(0, SM$FAC_LOG, SM$MSG_LSNFAILU, e.what());
				return;
			}
			boost::function<void(smnet::scktp, int)> f =
				boost::bind(&loglsnd::newc, this, _1, _2);
			SMI(smnet::smpx)->add(f, static_pointer_cast<smnet::sckt>(s), smnet::smpx::srd);

			s = smnet::lsnrp(new smnet::lsnr(smnet::internet));
			s->svc("8577");
			try {
				s->lsn();
			} catch (smnet::sckterr& e) {
				SMI(log)->logmsg(0, SM$FAC_LOG, SM$MSG_LSNFAILI, e.what());
				return;
			}
			f = boost::bind(&loglsnd::newc, this, _1, _2);
			SMI(smnet::smpx)->add(f, static_pointer_cast<smnet::sckt>(s), smnet::smpx::srd);
		}
		
		void newc(smnet::scktp sckt_, int) {
			smnet::lsnrp s = dynamic_pointer_cast<smnet::lsnr>(sckt_);
			try {
				smnet::clntp c = s->wt_acc();
				boost::function<void(smnet::scktp, int)> f =
					boost::bind(&loglsnd::cdata, this, _1, _2);
				SMI(smnet::smpx)->add(f, static_pointer_cast<smnet::sckt>(c), smnet::smpx::srd);
			} catch (smnet::sckterr& e) {
				SMI(log)->logmsg(0, SM$FAC_LOG, SM$MSG_LACCFAIL, e.what());
			}
		}
		
		std::map<smnet::scktp, std::string> data;
		
		void cdata(smnet::scktp s, int) {
			smnet::clntp c = dynamic_pointer_cast<smnet::clnt>(s);
			std::vector<u_char> thisdata;
			std::string msg;
			try {
				c->rd(thisdata);
			} catch (smnet::scktcls&) {
				finish(s);
				SMI(smnet::smpx)->rm(c);
				return;
			} catch (smnet::sckterr& e) {
				data.erase(s);
				SMI(smnet::smpx)->rm(c);
				return;
			}
			
			msg.assign(thisdata.begin(), thisdata.end());
			data[s] += msg;
		}

		void finish(smnet::scktp s) {
			std::string levs;
			std::string& msg = data[s];
			
			levs = smutl::car(msg);
			if (levs.empty()) {
				SMI(log)->logmsg(0, SM$FAC_LOG, SM$MSG_MALFRM);
				goto errout;
			}
			
			while (!msg.empty() and msg[0] == ' ')
				msg.erase(msg.begin());
			
			if (msg.empty()) {
				SMI(log)->logmsg(0, SM$FAC_LOG, SM$MSG_MALFRM);
				goto errout;
			}
			
			int lev;
			try {
				lev = lexical_cast<int>(levs);
			} catch (bad_lexical_cast&) {
				goto errout;
			}
			
			if (lev < 0 || lev > 16)
				goto errout;
			
			SMI(log)->logmsg(lev, SM$FAC_LOG, SM$MSG_NETMSG,
					 dynamic_pointer_cast<smnet::clnt>(s)->remote(), msg);
		  errout:
			data.erase(s);
			SMI(smnet::smpx)->rm(s);
		}
	};
} // anonymous namespace

void
log::initialise(void)
{
	(new loglsnd)->run();
}
	
void
log::logmsg(int irclvl, int fac, int msg, sm$msgarg a1, sm$msgarg a2, sm$msgarg a3)
{
	std::string message = sm$getmsg(fac, msg, a1, a2, a3);
	std::string fmt = b::io::str(b::format("<%s> %s") % smutl::fmttime() % message);
	std::cout << fmt << '\n';
	smtrm::terminal::broadcast(message);
	
	if (irclvl)
		SMI(smirc::cfg)->conn()->msg(irclvl, fmt);
}

void
log::debug(dbg_t func, str message)
{
	if (!debugset(func)) return;
	logmsg(0, SM$FAC_LOG, SM$MSG_DEBUG, message);
}
	
bool
log::debugset(dbg_t f)
{
	return debugs.find(f) != debugs.end();
}

void
log::dodebug(dbg_t f)
{
	debugs.insert(f);
}

void
log::dontdebug(dbg_t f)
{
	debugs.erase(f);
}

} // namespace smlog
