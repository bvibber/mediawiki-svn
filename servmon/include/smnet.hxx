#ifndef SM_SMNET_HXX_INCLUDED_
#define SM_SMNET_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smthr.hxx"

#undef unix
#undef bsd

namespace smnet {

struct sckterr : public std::runtime_error {
	sckterr(void) : std::runtime_error(std::strerror(errno)) {};
	sckterr(char const *s) : std::runtime_error(s) {}
};
struct scktcls : public std::exception {
	scktcls(void) {}
};

class smpx;

class bsd : noncopyable {
public:
	bsd(void) {}

	bsd(int s_) 
	: s(s_) 
	{}

	bsd(int s_, sockaddr* addr_, socklen_t len_)
	: s(s_)
	, addr(*addr_)
	, len(len_)
	{}

	void _bsd_iam(int s_) {
		s = s_;
	}

	virtual ~bsd(void) {
		std::cerr << "destroying socket " << s << "\n";
		close(s);
	}

	void _bsd_lsn(void) {
		int one = 1;
		setsockopt(s, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));
		one = fcntl(s, F_GETFL, 0);
		fcntl(s, F_SETFL, one | O_NONBLOCK);
		if (bind(s, reinterpret_cast<sockaddr*>(&addr), len) < 0)
			throw sckterr();
		if (listen(s, 5) < 0)
			throw sckterr();
	}

	int _bsd_wt_acc(sockaddr* caddr, socklen_t* clen) {
		int i;
		if ((i = accept(s, caddr, clen)) < 0)
			throw sckterr();
		return i;
	}

	void wrt(u_char const* d, ssize_t l) {
		if (write(s, d, l) < l) 
			throw sckterr();
	}

	void rd(std::vector<u_char>& v, uint m = maxrd) {
		std::vector<u_char>& rdbuf = rdbufs[s];
		if (!rdbuf.empty()) {
			int l = std::min(m, rdbuf.size());
			v.resize(l);
			copy(rdbuf.begin(), rdbuf.begin() + l, v.begin());
			rdbuf.erase(rdbuf.begin(), rdbuf.begin() + l);
			return;
		}
		uint i = _need_data();
		v.resize(i);
		i = std::min(i, m);
		copy(rdbuf.begin(), rdbuf.begin() + i, v.begin());
		rdbuf.erase(rdbuf.begin(), rdbuf.begin() + i);
	}

	inline u_char rd1(void) {
		std::vector<u_char>& rdbuf = rdbufs[s];
		_need_data();
		u_char c = *rdbuf.begin();
		rdbuf.erase(rdbuf.begin());
		return c;
	}
	static const int maxrd = 4096;
	
private:
	uint _need_data(void) {
		std::vector<u_char>& rdbuf = rdbufs[s];
		if (!rdbuf.empty()) return 0;
		std::cerr << "reading from " << s << '\n';
		rdbuf.resize(maxrd);
		int i = read(s, &rdbuf[0], maxrd);
		if (i == 0) throw scktcls();
		else if (i < 0) throw sckterr();
		rdbuf.resize(i);
		return i;
	}

protected:
	int s;
	sockaddr addr;
	socklen_t len;
	static std::map<int, std::vector<u_char> > rdbufs;
	friend class smpx;
};

class inet : public bsd {
public:
	inet() {
		int i;
		if ((i = socket(AF_INET, SOCK_STREAM, 0)) < 0)
			throw sckterr();
		_bsd_iam(i);
		ctmkaddr();
	}

	inet(int s_) : bsd(s_) {
		ctmkaddr();
	}

	inet(int s_, sockaddr* caddr, socklen_t clen)
	: bsd(s_, caddr, clen)
	{
		ctmkaddr();
	}

#if 0
	inet(inet const& o)
	: bsd(o) {
		ctmkaddr();
	}
#endif

	void ctmkaddr(void) {
		sockaddr_in *sin = (sockaddr_in*) &addr;
		sin->sin_family = AF_INET;
		sin->sin_addr.s_addr = htonl(INADDR_ANY);
		sin->sin_port = 0;
		len = sizeof(*sin);
	}

	void svc(std::string const& s) {
		sockaddr_in *sin = (sockaddr_in*) &addr;
		sin->sin_port = htons(svc_ = lexical_cast<int>(s));
	}
	void endpt(std::string const& host) {
		struct hostent *hptr;
		if ((hptr = gethostbyname(host.c_str())) == NULL)
			throw sckterr(hstrerror(h_errno));
		in_addr **pptr = (in_addr **) hptr->h_addr_list;
		sockaddr_in *sin = (sockaddr_in*) &addr;
		memcpy(&sin->sin_addr, *pptr, sizeof(struct in_addr));
		// XXX: try other addresses
	}
	void connect(void) {
		if ((::connect(s, &addr, len) < 0) && errno != EWOULDBLOCK)
			throw sckterr();
	}
	void lsn(void) {
		_bsd_lsn();
	}

	shared_ptr<inet> wt_acc(void) {
		sockaddr_in caddr;
		socklen_t clen = sizeof(caddr);
		int i = _bsd_wt_acc((sockaddr*)&caddr, &clen);
		inet *n = new inet(i, (sockaddr*) &caddr, clen);
		return shared_ptr<inet>(n);
	}

private:
	int svc_;
};

class unix {
};

template<class fmly>
class sckt : noncopyable {
public:
	sckt(void)
	: wr(new fmly)
	{}
	virtual ~sckt(void) {
	}

	void svc(std::string const& s) {
		wr->svc(s);
	}

	template<class fmly_>
	static sckt<fmly_> newsckt(int s) {
		sckt<fmly_> ss(s);
		return ss;
	}

protected:
	sckt(int s) : wr(s) {}
	sckt(shared_ptr<fmly> wr_) : wr(wr_) {}
	shared_ptr<fmly> wr;
	friend class smpx;
};

template<class fmly>
class clnt : public sckt<fmly> {
public:
	clnt(void) {};
	clnt(shared_ptr<fmly> wr_) : sckt<fmly>(wr_) {};

	void wrt(u_char const* d, std::size_t l) {
		sckt<fmly>::wr->wrt(d, l);
	}
	void wrt(std::string const& s) {
		sckt<fmly>::wr->wrt((u_char *) s.data(), s.size());
	}
	void rd(std::vector<u_char>& v, uint m = fmly::maxrd) {
		sckt<fmly>::wr->rd(v, m);
	}
	inline char rd1(void) {
		return sckt<fmly>::wr.rd1();
	}
	void endpt(std::string const& host) {
		sckt<fmly>::wr->endpt(host);
	}
	void connect(void) {
		sckt<fmly>::wr->connect();
	}

private:
	friend class smpx;
};
typedef clnt<inet> inetclnt;
typedef shared_ptr<inetclnt> inetclntp;

template<class fmly>
class lsnr : public sckt<fmly> {
public:
	void lsn(void) {
		sckt<fmly>::wr->lsn();
	}
	shared_ptr<clnt<fmly> > wt_acc(void) {
		return shared_ptr<clnt<fmly> > (new clnt<fmly>(sckt<fmly>::wr->wt_acc()));
	}
};
typedef lsnr<inet> inetlsnr;
typedef shared_ptr<inetlsnr> inetlsnrp;

struct tn2long : public std::runtime_error {
	tn2long(void) : std::runtime_error("received line too long") {}
};

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
		for (std::map<int,srec>::const_iterator it = fds.begin(), end = fds.end();
				it != end; ++it)
		{
			m = std::max(m, it->first + 1);
			std::cout << "testing " << it->first << " f = " << it->second.fg << "\n";
			if (it->second.fg & srd)
				FD_SET(it->second.fd, &rfds);
			if (it->second.fg & swr)
				FD_SET(it->second.fd, &wfds);
		}
		std::cout << "selecting...\n";
		int i = select(m, &rfds, &wfds, NULL, NULL);
		std::cout << i << "\n";
		if (i < 0) return;
		std::map<int,srec> cm = fds;
		for (std::map<int,srec>::const_iterator it = cm.begin(), end = cm.end();
				it != end; ++it)
		{
			std::cout << "testing fd " << it->first << "\n";
			if (FD_ISSET(it->first, &rfds))	{
				std::cerr << "fd " << it->first << " is ready\n";
				it->second.cb(srd);
			}
			if (FD_ISSET(it->first, &wfds))
				it->second.cb(swr);
		}
	}

	template<class stype>
	void add(boost::function<void(stype, int)> f, stype sckt, int flags)
	{
		srec r;
		r.cb = boost::bind(f, sckt, _1);
		r.fd = sckt->wr->s;
		r.fg = flags;
		fds[r.fd] = r;
		std::cerr << "adding callback for fd " << r.fd << "\n";
	}

	template<class stype>
	void rm(stype& sckt)
	{
		fds.erase(sckt->wr->s);
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

template<class fmly>
class tnsrv : noncopyable {
public:
	typedef shared_ptr<clnt<fmly> > sckt_t;
	tnsrv(sckt_t c)
	: sc(c)
	, stt(nrml)
	, doecho(true)
	, gd_cb(boost::function<void(sckt_t, u_char)>(
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
		boost::function<void(sckt_t, int)> f = 
			boost::bind(&tnsrv::data_cb, this, _2);
		SMI(smpx)->add(f, sc, smpx::srd);
	}

	virtual ~tnsrv(void) {
		SMI(smpx)->rm(sc);
	}

	void cb(boost::function<void(sckt_t, u_char)> f) {
		gd_cb = f;
	}

	void nullcb(sckt_t, u_char) {}

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
				std::cerr << "i don't understand option code " << int(c) << "\n";
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
	shared_ptr<clnt<fmly> > sc;
	enum { nrml, iac, sb, sb_iac, nl, cr,
       		gwill, gwont, gdo, gdont } stt;
	bool doecho;
	boost::function<void(shared_ptr<clnt<fmly> >, u_char)> gd_cb;
};
typedef tnsrv<inet> inettnsrv;
typedef shared_ptr<inettnsrv> inettnsrvp;

} // namespace smnet

#endif
