/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wnet: Networking.
 */

#ifndef WNET_H
#define WNET_H

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#if defined __digital__ && defined __unix__
/* sendfile prototype is missing on Tru64 UNIX */
# include <sys/uio.h>

ssize_t sendfile(int, int, off_t, size_t, const struct iovec *, int);
#endif

#include <sys/types.h>
#include <sys/socket.h>

#include <netinet/in.h>
#include <netdb.h>

#include "config.h"
#include <sys/time.h>
#include <sys/fcntl.h>

#include <event.h>
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

#include "willow.h"
#include "polycaller.h"

struct client_data;

namespace wnet {
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

	void	_accept		(wnet::socket *, int);
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

namespace wnet {

struct buffer;

struct buffer_item : freelist_allocator<buffer_item> {
	buffer_item(char const *buf_, size_t len_, bool free_)
		: buf(buf_)
		, len(len_)
		, off(0)
		, free(free_) {
	}
	buffer_item(buffer_item const &o)
		: len(o.len)
		, off(o.off)
		, free(true) {
		buf = (const char *)memcpy(new char[len], o.buf, len);
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

	deque<buffer_item> items;
};

} // namespace wnet

extern lockable		 acceptq_lock;
extern tss<event_base>	 evb;

struct client_data {
	sockaddr_storage	cdat_addr;
	socklen_t		cdat_addrlen;
};

extern char current_time_str[];
extern char current_time_short[];
extern time_t current_time;
extern int wnet_exit;

	void	wnet_add_accept_wakeup	(wnet::socket *);
	void 	wnet_set_time		(void);
	void	make_event_base		(void);

namespace wnet {	/* things above should move here eventually */

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
	st_dgram = SOCK_DGRAM,
};

struct address {
	address(void);
	address(const address &o);
	address(sockaddr *, socklen_t);

	address& operator= (const address &o);

	string const &straddr(bool lng = true) const;

	socket 	 *makesocket  (char const *, sprio) const;
	int	  length  (void) const {	return _addrlen;		}
	sockaddr *addr    (void) const {	return (sockaddr *)&_addr;	}
	int	  family  (void) const {	return _fam;			}
	int	  socktype(void) const {	return _stype;			}
	int	  protocol(void) const {	return _prot;			}

private:
	friend struct addrlist;
	address(addrinfo *ai);

	sockaddr_storage	 _addr;
	socklen_t		 _addrlen;
	int			 _fam, _stype, _prot;
	mutable string		 _straddr, _shortaddr;
};

struct addrlist {
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

struct socket : noncopyable {
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
	int		 read		(char *, size_t);
	int		 recvfrom	(char *, size_t, wnet::address &);
	int		 sendto		(char const *, size_t, wnet::address const &);
	int		 write		(char const *, size_t);
	void		 nonblocking	(bool);
	void		 reuseaddr	(bool);
	void		 bind		(void);
	void		 listen		(int bl = 25);
	int		 getopt		(int, void *, socklen_t *) const;
	int		 setopt		(int, void *, socklen_t);
	int		 error		(void) const;
	char const	*description	(void) const;

	wnet::address const	&address	(void) const;
	string const 	&straddr	(bool = true) const;

	template<typename T>
	void	readback (polycaller<wnet::socket *, T> cb, T ud);

	template<typename T>
	void	writeback (polycaller<wnet::socket *, T> cb, T ud);


protected:
	friend struct wnet::address;
	friend struct ::ioloop_t;

	explicit socket (int, wnet::address const &, char const *, sprio);
	explicit socket (wnet::address const &, char const *, sprio);

	void		_register	(int, polycallback<wsocket *>);
	static void	_ev_callback	(int fd, short ev, void *d);

	polycallback<wsocket *>	_read_handler, _write_handler;

	int		 _s;
	wnet::address	 _addr;
	char const	*_desc;
	sprio		 _prio;
	event		 ev;
};

	vector<addrinfo>	nametoaddrs(string const &name, int port);
	string			reserror(int);

template<typename T>
void
socket::readback (polycaller<wnet::socket *, T> cb, T ud) {
	_register(FDE_READ, polycallback<wnet::socket *>(cb, ud));
}

template<typename T>
void
socket::writeback (polycaller<wnet::socket *, T> cb, T ud) {
	_register(FDE_WRITE, polycallback<wnet::socket *>(cb, ud));
}

} // namespace wnet

#endif
