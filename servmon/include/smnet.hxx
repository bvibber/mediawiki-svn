/* @(#) $Header$ */
#ifndef SM_SMNET_HXX_INCLUDED_
#define SM_SMNET_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smthr.hxx"
#include "smtmr.hxx"
#include "smlog.hxx"

#undef unix
#undef bsd

namespace smnet {

struct sckterr : public std::runtime_error {
	sckterr(void) : std::runtime_error(std::strerror(errno)), err(errno) {};
	sckterr(char const *s) : std::runtime_error(s), err(0) {}
	int err;
};
struct scktcls : public std::exception {
	scktcls(void) {}
};

struct wouldblock : public sckterr {
	wouldblock() : sckterr("operation would block") {}
};
struct tn2long : public std::runtime_error {
	tn2long(void) : std::runtime_error("received line too long") {}
};
	
class smpx;

class sckt {
public:
	sckt(void);
	virtual ~sckt(void);
	
	/*
	 * set host (either listening or connecting addr depending
	 *  on socket type)
	 */
	void node(str node_);
	/* set service */
	void svc(str srv_);

private:
	friend class smpx;

protected:
	sockaddr_in sin;
	int s;

	sckt(int, sockaddr_in const *, socklen_t);
};		

typedef shared_ptr<sckt> scktp;
	
class clnt : public sckt {
public:
	clnt(void) {};

	bool connect(void);

	/* write [d, d + l) to socket */
	void wrt(u_char const* d, ssize_t l);

	/* read up to m bytes into v */
	void rd(std::vector<u_char>& v, uint m = maxrd);

	/* read 1 char */
	u_char rd1(void);

	inline void wrt(std::string const& s) {
		wrt((u_char *) s.data(), s.size());
	}

	static const int maxrd = 4096;

private:
	friend class lsnr;

	clnt(int s_, sockaddr_in const * sin_, socklen_t len_)
		: sckt(s_, sin_, len_)
		{}
	
	uint _need_data(void);
	static std::map<int, std::vector<u_char> > rdbufs;
};
	
typedef shared_ptr<clnt> clntp;

class lsnr : public sckt {
public:
	void lsn(void);
	shared_ptr<clnt> wt_acc(void);
};

typedef shared_ptr<lsnr> lsnrp;

class smpx : public smutl::singleton<smpx> {
public:
	void run(void) {
		for(;;) poll();
	}

	void poll(void) {
		fd_set rfds, wfds;
		FD_ZERO(&rfds);
		FD_ZERO(&wfds);
		int m = 0;
		time_t now = std::time(0);
		time_t nextevt = SMI(smtmr::evthdlr)->run_pend();
		struct timeval tv;
		tv.tv_sec = nextevt - now;
		tv.tv_usec = 0;
		for (std::map<int,srec>::const_iterator it = fds.begin(), end = fds.end();
				it != end; ++it)
		{
			m = std::max(m, it->first + 1);
			if (it->second.fg & srd)
				FD_SET(it->second.fd, &rfds);
			if (it->second.fg & swr)
				FD_SET(it->second.fd, &wfds);
		}
		int i = select(m, &rfds, &wfds, NULL, nextevt ? &tv : NULL);
		if (i < 0) {
			SMI(smlog::log)->logmsg(0, b::io::str(
							b::format("select() error: %d/%s")
							% errno % std::strerror(errno)));
			return;
		}
		std::map<int,srec> cm = fds;
		for (std::map<int,srec>::const_iterator it = cm.begin(), end = cm.end();
				it != end; ++it)
		{
			if (FD_ISSET(it->first, &rfds))	{
				it->second.cb(srd);
			}
			if (FD_ISSET(it->first, &wfds))
				it->second.cb(swr);
		}
	}

	void add(boost::function<void(scktp, int)> f, scktp sckt_, int flags)
	{
		srec r;
		r.cb = boost::bind(f, sckt_, _1);
		r.fd = sckt_->s;
		r.fg = flags;
		fds[r.fd] = r;
	}

	void rm(scktp sckt_)
	{
		fds.erase(sckt_->s);
	}

	static const int
		srd = 0x01,
		swr = 0x02
		;
private:
	struct srec {
		int fd, fg;
		boost::function<void(int)> cb;
	};
	std::map<int,srec> fds;
};

class tnsrv : noncopyable {
public:
	tnsrv(clntp c)
	: sc(c)
	, stt(nrml)
	, doecho(true)
	, gd_cb(boost::function<void(clntp, u_char)>(
				boost::bind(&tnsrv::nullcb, this, _1, _2)))
	{
		wewill.insert(tnsga);
		wewill.insert(tnecho);
		wecan.insert(tnsga);
		wecan.insert(tnecho);
		youshould.insert(tnsga);
		youshouldnt.insert(tnecho);
		do_(tnsga);
		will(tnecho);
		will(tnsga);
		dont(tnecho);
		dont(tnlinemode);
		boost::function<void(scktp, int)> f = 
			boost::bind(&tnsrv::data_cb, this, _2);
		SMI(smpx)->add(f, static_pointer_cast<sckt>(sc), smpx::srd);
	}

	virtual ~tnsrv(void) {
		SMI(smpx)->rm(sc);
	}

	void cb(boost::function<void(clntp, u_char)> f) {
		gd_cb = f;
	}

	void nullcb(clntp, u_char) {}

	void data_cb(int fl) {
		if (fl != smpx::srd) return;
		std::vector<u_char> d(maxrd);
		sc->rd(d, maxrd);
		for (std::vector<u_char>::iterator it = d.begin(), end = d.end(); it != end; ++it)
		{
			u_char c;
			bool b = rd1(*it, c);
			if (b)
				gd_cb(sc, c);
		}
	}

	void echo(bool doecho_) {
		doecho = doecho_;
	}
	void tnsth(u_char sth, u_char what) {
		u_char a[] = {tniac, sth, what};
		sc->wrt(a, sizeof a);
	}
	void do_(u_char what) {
		tnsth(tndo, what);
	}
	void dont(u_char what) {
		tnsth(tndont, what);
	}
	void will(u_char what) {
		tnsth(tnwill, what);
	}
	void wont(u_char what) {
		tnsth(tnwont, what);
	}
	void shouldyou(u_char what) {
		if (youshouldnt.find(what) != youshouldnt.end())
			dont(what);
	}
	void shouldwe(u_char what) {
		if (wecan.find(what) == wecan.end())
			wont(what);
	}
	void shouldntwe(u_char what) {
		wont(what);
	}
	void crnl(void) {
		if (!doecho) return;
		static u_char a[] = {'\r', '\n'};
		sc->wrt(a, sizeof a);
	}
	bool rd1(u_char c, u_char& data) {
		switch (stt) {
		case iac:
			switch (c) {
			case tnwill:
				stt = gwill; break;
			case tnwont:
				stt = gwont; break;
			case tndo:
				stt = gdo; break;
			case tndont:
				stt = gdont; break;
			case tnsb:
				stt = sb;
				break;
			case iac:
				stt = nrml;
				data = iac;
				return true;
			default:	
				// ???
				stt = nrml;
				stt = nrml; 
				break;
			}
			break;
		case gwill:
			shouldyou(c);
			stt = nrml;
			break;
		case gwont:
			stt = nrml;
			break;
		case gdo:
			shouldwe(c); 
			stt = nrml; 
			break;
		case gdont:
			shouldntwe(c); 
			stt = nrml; 
			break;
		case sb:
			switch (c) {
			case tniac:
				stt = sb_iac;
				break;
			default:
				break;
			}
			break;
		case sb_iac:
			switch (c) {
			case tniac:
				stt = sb;
				break;
			case tnse:
				stt = nrml;
				break;
			}
			break;
		default:	
			switch (c) {
			case tniac:
				stt = iac;
				break;
			case '\n': case '\0':
				break;
			default:
				data = c;
				return true;
			}
			break;
		}
		return false;
	}

	void wrt(u_char const* d, std::size_t l) {
		return sc->wrt(d, l);
	}
	void wrt(std::string const& s) {
		return sc->wrt((u_char *) s.data(), s.size());
	}

	static const int
		tniac = 255,
		tndont = 254,
		tndo = 253,
		tnwont = 252,
		tnwill = 251,
		tnsb = 250,
		tnse = 240,
		tnecho = 1,
		tnsga = 3,
		tnlinemode = 34
		;
	std::set<u_char> wewill, wecan, youshould, youshouldnt;
	static const int maxln = 4096, maxrd = 4096;
private:
	clntp sc;
	enum { nrml, iac, sb, sb_iac, nl, cr,
       		gwill, gwont, gdo, gdont } stt;
	bool doecho;
	boost::function<void(clntp, u_char)> gd_cb;
};
typedef shared_ptr<tnsrv> tnsrvp;

} // namespace smnet

#endif
