/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wbackend: HTTP backend handling.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <sys/types.h>
#include <sys/socket.h>

#include <arpa/inet.h>

#include <cstdlib>
#include <cstdio>
#include <cstring>
#include <cerrno>
#include <climits>
#include <cmath>
#include <ctime>

#include "willow.h"
#include "wbackend.h"
#include "wnet.h"
#include "wlog.h"
#include "confparse.h"
#include "wconfig.h"

#define rotl(i,r) (((i) << (r)) | ((i) >> (sizeof(i)*CHAR_BIT-(r))))

vector<backend *> backends;

static void backend_read(struct fde *);
static struct backend *next_backend(string const &url);
static uint32_t carp_urlhash(string const &);
static uint32_t carp_hosthash(string const &);
static void carp_recalc(string const &url);
static void carp_calc(void);
static int becarp_cmp(backend const *a, backend const *b);

struct backend_cb_data : freelist_allocator<backend_cb_data> {
struct	backend		*bc_backend;
	backend_cb	 bc_func;
	void		*bc_data;
	string		 bc_url;
};

backend::backend(string const &name, string const &straddr, sockaddr *addr, socklen_t addrlen)
	: be_name(name)
	, be_straddr(straddr)
	, be_dead(false)
	, be_hash(carp_hosthash(be_straddr))
	, be_load(1.)
{
	memcpy(&be_addr, addr, be_addrlen = addrlen);
}

void
add_backend(string const &addr, int port, int family)
{
int		 i;
addrinfo	 hints, *res, *r;
char		 portstr[6];
	sprintf(portstr, "%d", port);
	std::memset(&hints, 0, sizeof(hints));
	hints.ai_socktype = SOCK_STREAM;
	if (family != -1)
		hints.ai_family = family;

	if ((i = getaddrinfo(addr.c_str(), portstr, &hints, &res)) != 0) {
		wlog(WLOG_ERROR, "resolving %s: %s", addr.c_str(), gai_strerror(i));
		return;
	}

	for (r = res; r; r = r->ai_next) {
		backends.push_back(new backend(addr,
			wnet::straddr(r->ai_addr, r->ai_addrlen),
			r->ai_addr, r->ai_addrlen));
		wlog(WLOG_NOTICE, "backend server: %s[%s]:%d", 
		     addr.c_str(), wnet::straddr(r->ai_addr, r->ai_addrlen).c_str(), port);
	}
	freeaddrinfo(res);

	carp_calc();
}

#if 0
void
backend_file(file)
	char *file;
{
	FILE	*f;
	char	 line[1024];
	
	if ((f = fopen(file, "r")) == NULL) {
		perror(file);
		exit(8);
	}

	while (fgets(line, sizeof line, f)) {
		line[strlen(line) - 1] = '\0';
		add_backend(line);
	}

	(void)fclose(f);
}
#endif

int
get_backend(string const &url, backend_cb func, void *data, int flags)
{
struct	backend_cb_data	*cbd;
	int		 s;
static	time_t		 last_nfile;
	time_t		 now = time(NULL);

	WDEBUG((WLOG_DEBUG, "get_backend: called"));

	cbd = new backend_cb_data;

	cbd->bc_func = func;
	cbd->bc_data = data;
	cbd->bc_url = url;
	
	for (;;) {
		cbd->bc_backend = next_backend(url);

		if (cbd->bc_backend == NULL) {
			delete cbd;
			return -1;
		}

		if ((s = wnet_open("backend connection", cbd->bc_backend->be_addr.ss_family)) == -1) {
			if (errno != ENFILE || now - last_nfile > 60) 
				wlog(WLOG_WARNING, "opening backend socket: %s", strerror(errno));
			if (errno == ENFILE)
				last_nfile = now;
			delete cbd;
			return -1;
		}

		if (connect(s, (struct sockaddr *)&cbd->bc_backend->be_addr, 
		    sizeof(cbd->bc_backend->be_addr)) == 0) {
			WDEBUG((WLOG_DEBUG, "get_backend: connection completed immediately"));
			func(cbd->bc_backend, &fde_table[s], data);
			delete cbd;
			return 0;
		}

		if (errno != EINPROGRESS) {
			time_t retry = time(NULL) + config.backend_retry;
			wnet_close(s);
			wlog(WLOG_WARNING, "%s: %s; retry in %d seconds", 
				cbd->bc_backend->be_name.c_str(), strerror(errno), config.backend_retry);
			cbd->bc_backend->be_dead = 1;
			cbd->bc_backend->be_time = retry;
			continue;
		}

		WDEBUG((WLOG_DEBUG, "get_backend: waiting for connection to complete"));
		wnet_register(s, FDE_WRITE, backend_read, cbd);
		return 0;
	}
}

static void
backend_read(fde *e)
{
struct	backend_cb_data	*cbd = static_cast<backend_cb_data *>(e->fde_rdata);
	int		 error = 0;
	socklen_t	 len = sizeof(error);

	getsockopt(e->fde_fd, SOL_SOCKET, SO_ERROR, &error, &len);

	if (error && error != EINPROGRESS) {
		time_t retry = time(NULL) + config.backend_retry;
		wnet_close(e->fde_fd);
		wlog(WLOG_WARNING, "%s: [%d] %s; retry in %d seconds", 
			cbd->bc_backend->be_name.c_str(), error, strerror(error), config.backend_retry);
		cbd->bc_backend->be_dead = 1;
		cbd->bc_backend->be_time = retry;
		if (get_backend(cbd->bc_url, cbd->bc_func, cbd->bc_data, 0) == -1) {
			cbd->bc_func(NULL, NULL, cbd->bc_data);
		}
		delete cbd;
		return;
	}

	/*
	 * After handing the fd off to the caller, we don't care about it
	 * any more. 
	 */
	wnet_register(e->fde_fd, FDE_WRITE, NULL, NULL);

	cbd->bc_func(cbd->bc_backend, e, cbd->bc_data);
	delete cbd;
}

static struct backend *
next_backend(string const &url)
{
static	size_t	cur = 0;
	size_t	tried = 0;

	if (config.use_carp)
		carp_recalc(url);

	WDEBUG((WLOG_DEBUG, "next_backend: url=[%s]", url.c_str()));

	while (tried++ <= backends.size()) {
		time_t now = time(NULL);

		if (cur >= backends.size())
			cur = 0;

		if (backends[cur]->be_dead && now >= backends[cur]->be_time)
			backends[cur]->be_dead = 0;

		if (backends[cur]->be_dead) {
			cur++;
			continue;
		}

		if (config.use_carp)
			cur = 0;
		return backends[cur++];
	}

	return NULL;
}

static uint32_t
carp_urlhash(string const &str)
{
	uint32_t h = 0;
	for (string::const_iterator it = str.begin(), end = str.end(); it != end; ++it)
		h += rotl(h, 19) + *it;
	return h;
}

static uint32_t
carp_hosthash(string const &str)
{
	uint32_t h = carp_urlhash(str) * 0x62531965;
	return rotl(h, 21);
}

static void
carp_calc(void)
{
struct	backend *be, *prev;
	size_t	 i, j;

	backends[0]->be_carp = (uint32_t) pow((backends.size() * backends[0]->be_load), 1.0 / backends.size());
	backends[0]->be_carplfm = 1.0;
	for (i = 1; i < backends.size(); ++i) {
		float l = 0;
		be = backends[i];
		prev = backends[i - 1];
		be->be_carplfm = 1.0 + ((backends.size()-i+1) * (be->be_load - prev->be_load));
		for (j = 0; j < i; ++j)
			l *= backends[j]->be_carp;
		be->be_carp = (uint32_t) (be->be_carp / l);
		be->be_carp += (uint32_t) pow(prev->be_carp, backends.size()-i+1);
		be->be_carp = (uint32_t) pow(be->be_carp, 1/(backends.size()-i+1));
	}
}

static void
carp_recalc(string const &url)
{
	uint32_t	hash;
	size_t		i;
	for (i = 0; i < backends.size(); ++i) {
		hash = carp_urlhash(url) ^ backends[i]->be_hash;
		hash += hash * 0x62531965;
		hash = rotl(hash, 21);
		hash *= (uint32_t) backends[i]->be_carplfm;
		backends[i]->be_carp = hash;
	}
	sort(backends.begin(), backends.end(), becarp_cmp);
}

static int
becarp_cmp(backend const *a, backend const *b)
{
	return a->be_carp - b->be_carp;
}
