#ifndef SM_SMNET_HXX_INCLUDED_
#define SM_SMNET_HXX_INCLUDED_

#include "smstdinc.hxx"

#undef unix
#undef bsd

namespace smnet {

struct sckterr : public std::runtime_error {
	sckterr(void) : std::runtime_error(std::strerror(errno)) {};
};

class bsd {
public:
	bsd(void) {}
	bsd(int s_) : s(s_) {}
	bsd(int s_, sockaddr* addr_, socklen_t len_)
	: s(s_)
	, addr(*addr_)
	, len(len_)
	{
		++refs[s];
	}

	bsd(bsd const& o)
	: s(o.s)
	, addr(o.addr)
	, len(o.len)
	{
		++refs[s];
	}

	virtual ~bsd(void) {
		if (--refs[s] == 0) close(s);
	}


	void _bsd_lsn(void) {
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

protected:
	sockaddr addr;
	socklen_t len;
	int s;
	static std::map<int,int> refs;
};

class inet : public bsd {
public:
	inet() {
		if ((s = socket(AF_INET, SOCK_STREAM, 0)) < 0)
			throw sckterr();
		refs[s] = 1;
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
