#ifndef SM_SMNET_HXX_INCLUDED_
#define SM_SMNET_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smthr.hxx"

#undef unix
#undef bsd

namespace smnet {

struct sckterr : public std::runtime_error {
	sckterr(void) : std::runtime_error(std::strerror(errno)) {};
};
struct scktcls : public std::exception {
	scktcls(void) {}
};

class bsd {
public:
	bsd(void) {}

	bsd(int s_) : s(s_) {
		_locked_inc();
	}

	bsd(int s_, sockaddr* addr_, socklen_t len_)
	: s(s_)
	, addr(*addr_)
	, len(len_)
	{
		_locked_inc();
	}

	bsd(bsd const& o)
	: s(o.s)
	, addr(o.addr)
	, len(o.len)
	{
		_locked_inc();
	}

	bsd& operator= (bsd const& o) {
		s = o.s;
		addr = o.addr;
		len = o.len;
		_locked_inc();
		return *this;
	}

	void _bsd_iam(int s_) {
		s = s_;
		_locked_inc();
	}

	virtual ~bsd(void) {
		if (_locked_dec() == 0) close(s);
	}

	void _bsd_lsn(void) {
		int one = 1;
		setsockopt(s, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));
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
		rdbuf.resize(maxrd);
		int i = read(s, &rdbuf[0], maxrd);
		if (i == 0) throw scktcls();
		else if (i < 0) throw sckterr();
		rdbuf.resize(i);
		return i;
	}

	int _locked_inc(void) {
		smthr::lck l(mrl);
		return ++refs[s];
	}

	int _locked_dec(void) {
		smthr::lck l(mrl);
		int i = --refs[s];
		if (i == 0) {
			refs.erase(s);
			rdbufs.erase(s);
		}
		return i;
	}
		
protected:
	int s;
	sockaddr addr;
	socklen_t len;
	static std::map<int,int> refs;
	static std::map<int, std::vector<u_char> > rdbufs;
	smthr::mtx mrl;
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

	inet(inet const& o)
	: bsd(o) {
		ctmkaddr();
	}

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

	void lsn(void) {
		_bsd_lsn();
	}

	inet wt_acc(void) {
		sockaddr_in caddr;
		socklen_t clen = sizeof(caddr);
		int i = _bsd_wt_acc((sockaddr*)&caddr, &clen);
		return inet(i, (sockaddr*) &caddr, clen);
	}

private:
	int svc_;
};

class unix {
};

template<class fmly>
class sckt {
public:
	sckt(void) {}
	sckt(sckt<fmly> const& s)
	: wr(s.wr)
	{}

	virtual ~sckt(void) {
	}

	void svc(std::string const& s) {
		wr.svc(s);
	}

	template<class fmly_>
	static sckt<fmly_> newsckt(int s) {
		sckt<fmly_> ss(s);
		return ss;
	}

protected:
	sckt(int s) : wr(s) {}
	sckt(fmly wr_) : wr(wr_) {}
	fmly wr;
};

template<class fmly>
class clnt : public sckt<fmly> {
public:
	clnt(void) {};
	clnt(fmly wr_) : sckt<fmly>(wr_) {};
	clnt(clnt<fmly> const& s)
	: sckt<fmly>(s) {}

	void wrt(u_char const* d, std::size_t l) {
		return sckt<fmly>::wr.wrt(d, l);
	}
	void wrt(std::string const& s) {
		return sckt<fmly>::wr.wrt((u_char *) s.data(), s.size());
	}
	void rd(std::vector<u_char>& v, uint m = fmly::maxrd) {
		sckt<fmly>::wr.rd(v, m);
	}
	inline char rd1(void) {
		return sckt<fmly>::wr.rd1();
	}
};

template<class fmly>
class lsnr : public sckt<fmly> {
public:
	void lsn(void) {
		sckt<fmly>::wr.lsn();
	}
	clnt<fmly> wt_acc(void) {
		return clnt<fmly>(sckt<fmly>::wr.wt_acc());
	}
};

struct tn2long : public std::runtime_error {
	tn2long(void) : std::runtime_error("received line too long") {}
};

template<class fmly>
class tnsrv {
public:
	tnsrv(clnt<fmly> c)
	: sc(c)
	, stt(nrml)
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
	}
	void tnsth(u_char sth, u_char what) {
		u_char a[] = {tniac, sth, what};
		sc.wrt(a, sizeof a);
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
		static u_char a[] = {'\r', '\n'};
		sc.wrt(a, sizeof a);
	}
	u_char rd1(void) {
		for (;;) {
			u_char c = sc.rd1();
			switch (stt) {
			case iac:
				switch (c) {
				case tnwill:
					shouldyou(sc.rd1()); stt = nrml; break;
				case tnwont:
					sc.rd1(); stt = nrml;
					break;
				case tndo:
					shouldwe(sc.rd1()); stt = nrml; break;
				case tndont:
					shouldntwe(sc.rd1()); stt = nrml; break;
				case tnsb:
					stt = sb;
					break;
				case iac:
					stt = nrml;
					return iac;
					break;
				default:
					// ???
					std::cerr << "i don't understand option code " << int(c) << "\n";
					stt = nrml;
					break;
				}
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
					return c;
				}
				break;
			}
		}
	}

	std::string rdln(int m = maxln) {
		std::string lnbuf;
		for (;;) {
			u_char c = rd1();
			if (c == '\r') break;
			lnbuf += c;
			sc.wrt(&c, 1);
			if (lnbuf.size() > m)
				throw tn2long();
		}
		crnl();
		return lnbuf;
	}
	void wrt(u_char const* d, std::size_t l) {
		return sc.wrt(d, l);
	}
	void wrt(std::string const& s) {
		return sc.wrt((u_char *) s.data(), s.size());
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
	static const int maxln = 4096;
private:
	clnt<fmly> sc;
	enum { nrml, iac, sb, sb_iac, nl, cr } stt;
};

} // namespace smnet

#endif
