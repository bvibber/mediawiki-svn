/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smnet.hxx"

namespace smnet {

sckt::sckt(int type)
{
	int ftype = type == internet ? AF_INET : AF_UNIX;
	this->type = type;
	if ((s = socket(ftype, SOCK_STREAM, 0)) < 0)
		throw sckterr();
	if (type == internet) {
		struct sockaddr_in *sa = (sockaddr_in *)&sin;
		memset(sa, 0, sizeof *sa);
		sa->sin_family = ftype;
		sa->sin_addr.s_addr = htonl(INADDR_ANY);
		sa->sin_port = 0;
		len = sizeof(*sa);
	} else {
		struct sockaddr_un *sa = (sockaddr_un *)&sin;
		memset(sa, 0, sizeof *sa);
		sa->sun_family = ftype;
		sa->sun_path[0] = '\0';
		len = SUN_LEN(sa);
	}
}

sckt::sckt(int type_, int s_, sockaddr const *sin_, socklen_t len)
{
	std::memcpy(&sin, sin_, len);
	type = type_;
	s = s_;
	this->len = len;
}
	
sckt::~sckt(void) {
	close(s);
}

void
sckt::setblocking(bool)
{
}
	
void sckt::svc(std::string const& s) {
	if (type == internet) {
		sockaddr_in *sa = (sockaddr_in *)&sin;
		sa->sin_port = htons(lexical_cast<int>(s));
	} else {
		/* no-op for unix domain */
	}
}

void sckt::node(std::string const& host) {
	if (type == internet) {
		sockaddr_in *sa = (sockaddr_in *)&sin;
		struct hostent *hptr;
		if ((hptr = gethostbyname(host.c_str())) == NULL)
			throw sckterr(hstrerror(h_errno));
		in_addr **pptr = (in_addr **) hptr->h_addr_list;
		memcpy(&sa->sin_addr, *pptr, sizeof(struct in_addr));
		// XXX: try other addresses
	} else {
		sockaddr_un *sa = (sockaddr_un *)&sin;
		strncpy(sa->sun_path, host.c_str(), 107);
		sa->sun_path[107] = '\0';
		len = SUN_LEN(sa);
	}
}

void lsnr::lsn(void) {
	int one = 1;
	setsockopt(s, SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));
	one = fcntl(s, F_GETFL, 0);
	fcntl(s, F_SETFL, one | O_NONBLOCK);
	if (bind(s, (sockaddr *)&sin, len) < 0)
		throw sckterr();
	if (listen(s, 5) < 0)
		throw sckterr();
}

bool clnt::connect(void) {
	if ((::connect(s, (sockaddr *)&sin, len) < 0) && errno != EWOULDBLOCK)
		throw sckterr();
	return (errno == EWOULDBLOCK) ? false : true;
}

shared_ptr<clnt>
lsnr::wt_acc(void) {
	sockaddr_storage caddr;
	socklen_t clen = sizeof(caddr);
	int i;
	if ((i = accept(s, (sockaddr *)&caddr, &clen)) < 0)
		throw sckterr();
	clnt *n = new clnt(type, i, (sockaddr *)&caddr, clen);
	return shared_ptr<clnt>(n);
}

void
clnt::wrt(u_char const* d, ssize_t l) {
	if (::write(s, d, l) < l) 
		throw sckterr();
}

void
clnt::rd(std::vector<u_char>& v, uint m) {
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

u_char
clnt::rd1(void) {
	std::vector<u_char>& rdbuf = rdbufs[s];
	_need_data();
	u_char c = *rdbuf.begin();
	rdbuf.erase(rdbuf.begin());
	return c;
}

uint
clnt::_need_data(void) {
	std::vector<u_char>& rdbuf = rdbufs[s];
	if (!rdbuf.empty()) return 0;
	rdbuf.resize(maxrd);
	int i = read(s, &rdbuf[0], maxrd);
	if (i == 0) throw scktcls();
	else if (i < 0) {
		if (errno == EAGAIN || errno == EWOULDBLOCK)
			throw wouldblock();
		else
			throw sckterr();
	}
	rdbuf.resize(i);
	return i;
}

std::map<int, std::vector<u_char> > clnt::rdbufs;

	
} // namespace smnet
