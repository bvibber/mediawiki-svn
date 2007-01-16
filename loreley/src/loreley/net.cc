/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* net: OS agnostic networking.						*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
#ifdef __INTEL_COMPILER
# pragma hdrstop
#endif

namespace sfun {
	using ::bind;	/* because of conflict with boost::bind from util.h */
};

using std::deque;
using std::signal;
using std::multimap;

#include "loreley.h"
#include "net.h"
#include "config.h"
#include "log.h"
#include "http.h"

/* see ifname_to_address.cc */
int ifname_to_address(int, sockaddr_in *, char const *);
unsigned int if_nametoindex_wrap(const char *);

static void secondly_sched(void);

ioloop_t *ioloop;

char current_time_str[30];
char current_time_short[30];
time_t current_time;

bool wnet_exit;
vector<wsocket *>	awaks;
size_t			cawak;

void
wnet_add_accept_wakeup(wsocket *s)
{
	awaks.push_back(s);
}

net::event	secondly_ev;

static void
secondly_update(void)
{
	WDEBUG("secondly_update");
	wnet_set_time();
	secondly_sched();
}

static void
secondly_sched(void)
{
	secondly_ev.schedule(secondly_update, 1000);
}

ioloop_t::ioloop_t(void)
{
	prepare();
	secondly_sched();
}

void
ioloop_t::_accept(wsocket *s, int)
{
	wsocket		*newe;
static rate_limited_logger enfile_log(60, ll_warn, "accept error: %s");

	if ((newe = s->accept("HTTP client", prio_norm)) == NULL) {
		if (errno == ENFILE || errno == EMFILE)
			enfile_log.log(strerror(errno));
		else
			wlog.warn(format("accept error: %s") % strerror(errno));
		s->readback(bind(&ioloop_t::_accept, this, _1, _2), -1);
		return;
	}

	s->readback(bind(&ioloop_t::_accept, this, _1, _2), -1);

	newe->nonblocking(true);

	if (cawak == awaks.size())
		cawak = 0;
char	buf[sizeof(wsocket *) * 2];
	memcpy(buf, &newe, sizeof(newe));
	memcpy(buf + sizeof(newe), &s, sizeof(s));
	WDEBUG(format("_accept, lsnr=%d") % s);

	if (awaks[cawak]->write(buf, sizeof(wsocket *) * 2) < 0) {
		wlog.error(format("writing to thread wakeup socket: %s")
			% strerror(errno));
		exit(1);
	}
	cawak++;
	return;
}

void
wnet_set_time(void)
{
struct	tm	*now;
	time_t	 old = current_time;
	size_t	 n;
	
	current_time = time(NULL);
	if (current_time == old)
		return;

	now = gmtime(&current_time);

	n = strftime(current_time_str, sizeof(current_time_str), "%a, %d %b %Y %H:%M:%S GMT", now);
	assert(n);
	n = strftime(current_time_short, sizeof(current_time_short), "%Y-%m-%d %H:%M:%S", now);
	assert(n);
}

namespace net {

address::address(void)
{
	memset(&_addr, 0, sizeof(_addr));
	_addrlen = 0;
	_fam = AF_UNSPEC;
	_stype = _prot = 0;
}

address::address(sockaddr *sa, socklen_t len)
{
	memcpy(&_addr, sa, len);
	_addrlen = len;
	_stype = _prot = 0;
	_fam = ((sockaddr_storage *)sa)->ss_family;
}

address::address(addrinfo *ai) 
{
	memcpy(&_addr, ai->ai_addr, ai->ai_addrlen);
	_addrlen = ai->ai_addrlen;
	_fam = ai->ai_family;
	_stype = ai->ai_socktype;
	_prot = ai->ai_protocol;
}

socket *
address::makesocket(char const *desc, sprio p) const
{
	return new socket(*this, desc, p);
}

address::address(address const &o)
	: _addrlen(o._addrlen)
	, _fam(o._fam)
	, _stype(o._stype)
	, _prot(o._prot) {
	memcpy(&_addr, &o._addr, _addrlen);
}

address &
address::operator= (address const &o)
{
	_addrlen = o._addrlen;
	_fam = o._fam;
	_stype = o._stype;
	_prot = o._prot;
	memcpy(&_addr, &o._addr, _addrlen);
	return *this;
}

string const &
address::straddr(bool lng) const
{
char	res[NI_MAXHOST];
int	i;
	if (!lng) {
		if (_shortaddr.empty()) {
			if ((i = getnameinfo(sockaddr_cast<sockaddr const *>(&_addr),
			    _addrlen, res, sizeof(res), NULL, 0, NI_NUMERICHOST)) != 0)
				throw resolution_error(i);
			_shortaddr = res; 
		}
		return _shortaddr;
	}

	if (_straddr.empty()) {
	char	port[NI_MAXSERV];
		if ((i = getnameinfo(sockaddr_cast<sockaddr const *>(&_addr),
		    _addrlen, res, sizeof(res), port, sizeof(port), 
			NI_NUMERICHOST | NI_NUMERICSERV)) != 0)
			throw resolution_error(i);
		_straddr = str(format("[%s]:%s") % res % port);
	}
	return _straddr;
}

addrlist *
addrlist::resolve(string const &addr, string const &port,
		  enum socktype socktype, int family)
{
addrinfo	 hints, *res, *ai;
int		 r;
	memset(&hints, 0, sizeof(hints));
	hints.ai_socktype = (int) socktype;
	if (family != AF_UNSPEC)
		hints.ai_family = family;

	if ((r = getaddrinfo(addr.c_str(), 
	    port.c_str(), &hints, &res)) != 0)
		throw resolution_error(r);

addrlist	*al = new addrlist;
	for (ai = res; ai; ai = ai->ai_next)
		al->_addrs.push_back(address(ai));

	freeaddrinfo(res);
	return al;
}

address
addrlist::first(string const &addr, int port,
		enum socktype socktype, int family)
{
	return first(addr, lexical_cast<string>(port), socktype, family);
}

addrlist *
addrlist::resolve(string const &addr, int port, 
		  enum socktype socktype, int family)
{
	return resolve(addr, lexical_cast<string>(port), socktype, family);
}

address
addrlist::first(string const &addr, string const &port,
		enum socktype socktype, int family)
{
addrlist	*r = addrlist::resolve(addr, port, socktype, family);
address		 res;
	res = *r->begin();
	delete r;
	return res;
}

addrlist::~addrlist(void)
{
}

addrlist::iterator
addrlist::begin(void) const
{
	return _addrs.begin();
}

addrlist::iterator
addrlist::end(void) const
{
	return _addrs.end();
}

socket *
addrlist::makesocket(char const *desc, sprio p) const
{
iterator	it = _addrs.begin(), end = _addrs.end();
	for (; it != end; ++it) {
	socket	*ns;
		if ((ns = it->makesocket(desc, p)) != NULL)
			return ns;
	}
	throw socket_error();
}

socket *
socket::create(string const &addr, int port,
	       enum socktype socktype, char const *desc, sprio p, int family)
{
	return create(addr, lexical_cast<string>(port), socktype, desc, p, family);
}

socket *
socket::create(string const &addr, string const &port,
	       enum socktype socktype, char const *desc, sprio p, int family)
{
addrlist	*al = addrlist::resolve(addr, port, socktype, family);
	return al->makesocket(desc, p);
}

pair<socket *, socket *>
socket::socketpair(enum socktype st)
{
socket	*s1 = NULL, *s2 = NULL;
int	 sv[2];
	if (::socketpair(AF_UNIX, (int) st, 0, sv) == -1)
		throw socket_error();
	s1 = new socket(sv[0], net::address(), "socketpair", prio_norm);
	try {
		s2 = new socket(sv[1], net::address(), "socketpair", prio_norm);
	} catch (...) {
		delete s1;
		throw;
	}
	return make_pair(s1, s2);
}

connect_status
socket::connect(void)
{
	if (::connect(_s, _addr.addr(), _addr.length()) == -1)
		if (errno == EINPROGRESS)
			return connect_later;
		else
			throw socket_error();
	return connect_okay;
}

socket *
socket::accept(char const *desc, sprio p)
{
int			ns;
sockaddr_storage	addr;
socklen_t		addrlen = sizeof(addr);
	if ((ns = ::accept(_s, (sockaddr *)&addr, &addrlen)) == -1)
		return NULL;

	/*
	 * If TCP_CORK is not supported, disable Nagle's algorithm on the
	 * socket to prevent delays in HTTP keepalive (at the expense of
	 * sending more packets).
	 */
#if !defined(TCP_CORK) && defined(TCP_NODELAY)
int	one = 1;
	setsockopt(ns, IPPROTO_TCP, TCP_NODELAY, &one, sizeof(one));
#endif

	return new socket(ns, net::address((sockaddr *)&addr, addrlen), desc, p);
}

int
socket::recvfrom(char *buf, size_t count, net::address &addr)
{
sockaddr_storage	saddr;
socklen_t		addrlen = sizeof(addr);
int			i;
	if ((i = ::recvfrom(_s, buf, count, 0, (sockaddr *)&saddr, &addrlen)) < 0)
		return i;
	WDEBUG(format("recvfrom: fam=%d") % saddr.ss_family);
	addr = net::address((sockaddr *)&saddr, addrlen);
	return i;
}

int
socket::sendto(char const *buf, size_t count, net::address const &addr)
{
	return ::sendto(_s, buf, count, 0, addr.addr(), addr.length());
}

void
socket::nonblocking(bool v)
{
int	val;
	val = fcntl(_s, F_GETFL, 0);
	if (val == -1)
		throw socket_error();
	if (v)
		val |= O_NONBLOCK;
	else	val &= ~O_NONBLOCK;

	if (fcntl(_s, F_SETFL, val) == -1)
		throw socket_error();
}

void
socket::reuseaddr(bool v)
{
int	i = v;
int	len = sizeof(i);
	setopt(SOL_SOCKET, SO_REUSEADDR, &i, len);
}

void
socket::cork(void)
{
#ifdef TCP_CORK
int	one = 1;
	setopt(IPPROTO_TCP, TCP_CORK, &one, sizeof(one));
#endif
}

void
socket::uncork(void)
{
#ifdef TCP_CORK
int	zero = 0;
	setopt(IPPROTO_TCP, TCP_CORK, &zero, sizeof(zero));
#endif
}

int
socket::getopt(int level, int what, void *addr, socklen_t *len) const
{
int	i;
	if ((i = getsockopt(_s, level, what, addr, len)) == -1)
		throw socket_error();
	return i;
}

int
socket::setopt(int level, int what, void *addr, socklen_t len)
{
int	i;
	if ((i = setsockopt(_s, level, what, addr, len)) == -1)
		throw socket_error();
	return i;
}

int
socket::error(void) const
{
int		error = 0;
socklen_t	len = sizeof(error);
	try {
		getopt(SOL_SOCKET, SO_ERROR, &error, &len);
		return error;
	} catch (socket_error &) {
		return 0;
	}
}

void
socket::bind(void)
{
int	one = 1;
	setopt(SOL_SOCKET, SO_REUSEADDR, &one, sizeof(one));
	if (sfun::bind(_s, _addr.addr(), _addr.length()) == -1)
		throw socket_error();
}

void
socket::listen(int bl)
{
	if (::listen(_s, bl) == -1)
		throw socket_error();
}

void
socket::mcast_join(string const &ifname)
{
	switch (_addr.family()) {
	case AF_INET: {
	struct address	 ifaddr = address::from_ifname(_s, ifname);
	sockaddr_in	*inbind = (sockaddr_in *)_addr.addr();
	sockaddr_in	*inif   = (sockaddr_in *)ifaddr.addr();
	ip_mreq		 mr;
		memset(&mr, 0, sizeof(mr));
		mr.imr_multiaddr.s_addr = inbind->sin_addr.s_addr;
		mr.imr_interface.s_addr = inif->sin_addr.s_addr;
		WDEBUG(format("NET: %s joins mcast on if %s")
			% straddr() % ifaddr.straddr());
		setopt(IPPROTO_IP, IP_ADD_MEMBERSHIP, &mr, sizeof(mr));
		break;
	}

	case AF_INET6: {
#ifdef IPV6_ADD_MEMBERSHIP
	u_int		 ifindex = address::ifname_to_index(ifname);
	sockaddr_in6	*inbind = (sockaddr_in6 *)_addr.addr();
	ipv6_mreq	 mr;
		memset(&mr, 0, sizeof(mr));
		memcpy(&mr.ipv6mr_multiaddr, &inbind->sin6_addr,
			sizeof(mr.ipv6mr_multiaddr));
		mr.ipv6mr_interface = ifindex;
		setopt(IPPROTO_IPV6, IPV6_ADD_MEMBERSHIP, &mr, sizeof(mr));
#else
		wlog.warn("IPv6 multicast not supported on this platform");
#endif
		break;
	}

	default:
		throw socket_error("multicast join not applicable for this socket type");
	}
}

u_int
address::ifname_to_index(string const &ifname)
{
u_int	ret = if_nametoindex_wrap(ifname.c_str());
	if (ret == 0)
		throw socket_error("named interface does not exist");
	return ret;
}

address
address::from_ifname(int s, string const &ifname)
{
sockaddr_in	addr;
	if (ifname_to_address(s, &addr, ifname.c_str()) < 0)
		throw socket_error();
address	ret((sockaddr *)&addr, sizeof(sockaddr_in));
	return ret;
}

} // namespace net
