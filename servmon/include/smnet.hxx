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

	void wrt(u_char* d, std::size_t l) {
		if (write(s, d, l) < l) 
			throw sckterr();
	}

private:
	int _locked_inc(void) {
		smthr::lck l(mrl);
		return ++refs[s];
	}

	int _locked_dec(void) {
		smthr::lck l(mrl);
		int i = --refs[s];
		if (i == 0) refs.erase(i);
		return i;
	}
		
protected:
	sockaddr addr;
	socklen_t len;
	int s;
	static std::map<int,int> refs;
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

} // namespace smnet

#endif
