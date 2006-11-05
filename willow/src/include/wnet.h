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
#include <event.h>
#include <pthread.h>
#include <vector>
#include <deque>
#include <cassert>

using std::deque;
using std::vector;

#include "willow.h"
#include "polycaller.h"

struct fde;

extern int max_fd;

struct client_data;

enum sprio {
	prio_stats	= 0,
	prio_backend,
	prio_norm,
	prio_accept,
	prio_max
};

struct readbuf {
	char	*rb_p;		/* start of allocated region	*/
	int	 rb_size;	/* size of allocated region	*/
	int	 rb_dsize;	/* [p,p+dsize) is valid data	*/
	int	 rb_dpos;	/* current data position	*/
};
#define readbuf_spare_size(b) ((b)->rb_size - (b)->rb_dsize)
#define readbuf_spare_start(b) ((b)->rb_p + (b)->rb_dsize)
#define readbuf_data_left(b) ((b)->rb_dsize - (b)->rb_dpos)
#define readbuf_inc_data_pos(b, i) ((b)->rb_dpos += (i))
#define readbuf_cur_pos(b) ((b)->rb_p + (b)->rb_dpos)

#define FDE_READ	0x1
#define FDE_WRITE	0x2

extern struct ioloop_t {
	ioloop_t();

	void	prepare(void);

	void	_register	(int, int, polycallback<fde *>);
	void	_accept		(fde *, int);

	template<typename T>
	void	readback (int fd, polycaller<fde *, T> cb, T ud) {
		_register(fd, FDE_READ, polycallback<fde *>(cb, ud));
	}

	template<typename T>
	void	writeback (int fd, polycaller<fde *, T> cb, T ud) {
		_register(fd, FDE_WRITE, polycallback<fde *>(cb, ud));
	}

	void	clear_readback	(int fd);
	void	clear_writeback	(int fd);
} *ioloop;

/*
 * For working with packed UDP data.
 */
#define HAS_SPACE(b,l,endp)	(((b) + (l)) < endp)
#define ADD_UINT32(b,i,endp)	if (HAS_SPACE(b,4,endp)) { *(uint32_t*)b = i; b += 4; }
#define ADD_UINT16(b,i,endp)	if (HAS_SPACE(b,2,endp)) { *(uint16_t*)b = i; b += 2; }
#define ADD_UINT8(b,i,endp)	if (HAS_SPACE(b,1,endp)) { *(uint8_t*)b = i; b += 1; }
#define ADD_STRING(b,s,endp) do {	uint16_t len = strlen(s);		\
					if (HAS_SPACE(b,2 + len,endp)) {	\
						ADD_UINT16(b,len,endp);		\
						memcpy(b, s, len);		\
						b += len;			\
					}					\
				} while (0)

struct fde {
	int			 fde_fd;
	const char		*fde_desc;
	polycallback<fde *>	 fde_read_handler;
	polycallback<fde *>	 fde_write_handler;
struct	client_data		*fde_cdata;
	char			 fde_straddr[40];
	int			 fde_epflags;
struct	readbuf			 fde_readbuf;
	struct {
		unsigned int	open:1;
		unsigned int	read_held:1;
		unsigned int	write_held:1;
		unsigned int	pend:1;
	}			 fde_flags;
struct	event			 fde_ev;
enum	sprio			 fde_prio;
	lockable		 fde_lock;
};
extern struct fde *fde_table;

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

	void	wnet_run(void);

	int	wnet_open		(const char *desc, sprio p, int aftype, int type = SOCK_STREAM);
	void	wnet_close		(int);
	void	wnet_write		(int, const void *, size_t, polycaller<fde *, int>);
	int	wnet_sendfile		(int, int, size_t, off_t, polycaller<fde *, int>);
	void	wnet_set_blocking	(int);
	void	wnet_add_accept_wakeup	(int);
	int	wnet_socketpair		(int, int, int, int[2]);
	void 	wnet_set_time		(void);
	void 	wnet_init_select	(void);
	void	make_event_base		(void);

	int	readbuf_getdata		(struct fde *);
	void	readbuf_free		(struct readbuf *);

namespace wnet {	/* things above should move here eventually */

	string			straddr(sockaddr const *addr, socklen_t len);
	string			fstraddr(string const &, sockaddr const *addr, socklen_t len);
	vector<addrinfo>	nametoaddrs(string const &name, int port);
	string			reserror(int);

}
	
#endif
