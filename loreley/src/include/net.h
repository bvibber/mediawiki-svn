/* Loreley: Lightweight HTTP reverse-proxy.                              */
/* net: Networking.							*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#ifndef WNET_H
#define WNET_H

#if defined __digital__ && defined __unix__
/* sendfile prototype is missing on Tru64 UNIX */
# include <sys/uio.h>

ssize_t sendfile(int, int, off_t, size_t, const struct iovec *, int);
#endif

#include <sys/types.h>
#include <sys/socket.h>

#include <netinet/in.h>
#include <netdb.h>
#include <unistd.h>

#include "autoconf.h"
#include <sys/time.h>
#include <sys/fcntl.h>
#ifdef HAVE_SYS_SENDFILE_H
# include <sys/sendfile.h>
#endif

#include <pthread.h>
#include <vector>
#include <deque>
#include <cassert>
#include <stdexcept>
#include <cerrno>
#include <utility>
using std::deque;
using std::vector;
using std::runtime_error;
using std::pair;
using std::make_pair;
using std::memcpy;

#include "loreley.h"

extern bool wnet_exit;
struct event_queue;

struct event;

namespace net {

struct addrlist;
struct address;
struct socket;
typedef socket wsocket;

}

enum sprio {
	prio_stats	= 0,
	prio_backend,
	prio_norm,
	prio_accept,
	prio_max
};

#define FDE_READ	0x1
#define FDE_WRITE	0x2

extern struct ioloop_t {
	ioloop_t();

	void	prepare	(void);
	void	run	(void);
	void	thread_run (void);
	void	_accept		(net::socket *, int);
} *ioloop;

/*
 * For working with packed UDP data.
 */
#define HAS_SPACE(b,l,endp)	(((b) + (l)) < endp)
#define ADD_UINT32(b,i,endp)	if (HAS_SPACE(b,4,endp)) { *(uint32_t*)b = i; b += 4; }
#define ADD_UINT16(b,i,endp)	if (HAS_SPACE(b,2,endp)) { *(uint16_t*)b = i; b += 2; }
#define ADD_UINT8(b,i,endp)	if (HAS_SPACE(b,1,endp)) { *(uint8_t*)b = i; b += 1; }
#define ADD_STRING(b,s,endp) do {	uint16_t len = s.size();		\
					if (HAS_SPACE(b,2 + len,endp)) {	\
						ADD_UINT16(b,len,endp);		\
						memcpy(b, s.data(), len);		\
						b += len;			\
					}					\
				} while (0)

struct event_impl;

namespace net {

struct buffer;

struct buffer_item : freelist_allocator<buffer_item> {
	buffer_item(char const *buf_, size_t len_, bool free_)
		: buf(buf_)
		, len(len_)
		, off(0)
		, free(free_) {
	}
	buffer_item(buffer_item const &o)
		: buf(NULL)
		, len(o.len)
		, off(o.off)
		, free(true) {
		buf = (const char *)memcpy(new char[len], o.buf, len);
	}
	buffer_item&
	operator=(buffer_item const &other) {
		buf = NULL;
		len = other.len;
		off = other.off;
		free = true;
		buf = (const char *)memcpy(new char[len], other.buf, len);
		return *this;
	}

	~buffer_item() {
		if (free)
			delete[] buf;
	}

	char const	*buf;
	size_t		 len;
	size_t		 off;
	bool		 free;
};

struct buffer {
	void add(char const *buf, size_t len, bool free) {
		items.push_back(buffer_item(buf, len, free));
	}

	void reset(void) {
		items.clear();
	}

	deque<buffer_item, pt_allocator<buffer_item> > items;
};

} // namespace net

extern lockable		 acceptq_lock;
//extern tss<event_base>	 evb;

struct client_data {
	sockaddr_storage	cdat_addr;
	socklen_t		cdat_addrlen;
};

extern char current_time_str[];
extern char current_time_short[];
extern time_t current_time;

	void	wnet_add_accept_wakeup	(net::socket *);
	void 	wnet_set_time		(void);
	void	make_event_base		(void);

namespace net {	/* things above should move here eventually */

struct socket_error : runtime_error {
	int	_err;
	int	err(void) const {
		return _err;
	}
	socket_error(int i) : runtime_error(strerror(i)), _err(i) {}
	socket_error(char const *s) : runtime_error(s), _err(0) {}
	socket_error() : runtime_error(strerror(errno)), _err(errno) {}
};

struct resolution_error : socket_error {
	resolution_error(int i) : socket_error(gai_strerror(i)) {}
};

enum connect_status {
	connect_okay,
	connect_later
};

enum socktype {
	st_stream = SOCK_STREAM,
	st_dgram = SOCK_DGRAM
};

struct address : freelist_allocator<address> {
	address(void);
	address(const address &o);
	address(sockaddr *, socklen_t);

	address& operator= (const address &o);

	string const &straddr(bool lng = true) const;

	socket 	 *makesocket  (char const *, sprio) const;
	int	  length  (void) const {	return _addrlen;		}
	sockaddr const *addr    (void) const {	return (sockaddr const *)&_addr;	}
	sockaddr *addr    (void)       {	return (sockaddr *)&_addr;	}
	int	  family  (void) const {	return _fam;			}
	int	  socktype(void) const {	return _stype;			}
	int	  protocol(void) const {	return _prot;			}

	static address	from_ifname(int s, string const &ifname);
	static u_int	ifname_to_index(string const &ifname);

private:
	friend struct addrlist;
	address(addrinfo *ai);

	sockaddr_storage	 _addr;
	socklen_t		 _addrlen;
	int			 _fam, _stype, _prot;
	mutable string		 _straddr, _shortaddr;
};

struct addrlist : freelist_allocator<addrlist> {
	typedef address value_type;
	typedef vector<value_type>::const_iterator
		iterator, const_iterator;

	~addrlist();

	iterator	 begin		(void) const;
	iterator	 end		(void) const;
	socket		*makesocket	(char const *, sprio) const;
	static addrlist	*resolve	(string const &, 
					 string const &, 
					 enum socktype, int = AF_UNSPEC);
	static addrlist	*resolve	(string const &, int ,
					 enum socktype, int = AF_UNSPEC);

	static address	 first		(string const &, 
					 string const &, 
					 enum socktype, int = AF_UNSPEC);
	static address	 first		(string const &, int,
					 enum socktype, int = AF_UNSPEC);

private:
	addrlist() {};

	vector<value_type>	 _addrs;
};

struct event {
	event();
	~event();

	void schedule(function<void (void)>, int64_t when);
	event_impl	*impl;
};

struct socket : noncopyable, freelist_allocator<socket> {
	~socket();

	static socket	*create(string const &addr, int port,
				enum socktype, char const *, sprio,
				int = AF_UNSPEC);
	static socket	*create(string const &addr, string const &port,
				enum socktype, char const *, sprio,
				int = AF_UNSPEC);
	static pair<socket *, socket *> socketpair(enum socktype);

	socket		*accept		(char const *, sprio);
	connect_status	 connect	(void);
	int		 read		(char *buf, size_t count) {
		return ::read(_s, buf, count);
	}
	int		 recvfrom	(char *, size_t, net::address &);
	int		 sendto		(char const *, size_t, net::address const &);
	int		 write		(char const *buf, size_t count) {
		return ::write(_s, buf, count);
	}
#if defined(HAVE_SENDFILE) && defined(__linux__)
	int		 sendfile	(int to, off_t *off, size_t n) {
		return ::sendfile(_s, to, off, n);
	}
#endif

	void		 nonblocking	(bool);
	void		 reuseaddr	(bool);
	void		 bind		(void);
	void		 listen		(int bl = 25);
	int		 getopt		(int, int, void *, socklen_t *) const;
	int		 setopt		(int, int, void *, socklen_t);
	void		 cork		(void);
	void		 uncork		(void);
	int		 error		(void) const;
	char const	*description	(void) const;

	void		 mcast_join	(string const &ifname);
	void		 mcast_leave	(string const &ifname);

	net::address const	&address	(void) const {
		return _addr;
	}
	string const 	&straddr	(bool lng = true) const {
		return _addr.straddr(lng);
	}

	template<typename T>
	void	readback (T cb, int64_t);

	template<typename T>
	void	writeback (T cb, int64_t);

	void	clearbacks	(void);

protected:
	friend struct net::address;
	friend struct ::ioloop_t;
	friend void socket_callback(int fd, short ev, void *d);
	typedef function<void (wsocket *, bool)> call_type;

	explicit socket (int, net::address const &, char const *, sprio);
	explicit socket (net::address const &, char const *, sprio);

	void		_register	(int, int64_t, call_type);
	static void	_ev_callback	(int fd, short ev, void *d);

	function<void (wsocket *, int)>	_read_handler, _write_handler;

	int		 _s;
	net::address	 _addr;
	char const	*_desc;
	sprio		 _prio;
	::event		*ev;
	int		 _ev_flags;
	event_queue	*_queue;
};

	vector<addrinfo>	nametoaddrs(string const &name, int port);
	string			reserror(int);

template<typename T>
void
socket::readback (T cb, int64_t to) {
	_register(FDE_READ, to, call_type(cb));
}

template<typename T>
void
socket::writeback (T cb, int64_t to) {
	_register(FDE_WRITE, to, call_type(cb));
}

} // namespace net

#endif
