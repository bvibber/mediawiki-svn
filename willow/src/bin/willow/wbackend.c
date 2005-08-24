/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wbackend: HTTP backend handling.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include <sys/types.h>
#include <sys/socket.h>

#include <arpa/inet.h>

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <errno.h>
#include <strings.h>
#include <limits.h>
#include <math.h>

#include "willow.h"
#include "wbackend.h"
#include "wnet.h"
#include "wlog.h"
#include "confparse.h"
#include "wconfig.h"

#define rotl(i,r) ((i) << (r) | (i) >> sizeof(i)*CHAR_BIT-(r));

static struct backend **backends;
int nbackends;

static struct backend *new_backend(const char *, const char *, int);
static void backend_read(struct fde *);
static struct backend *next_backend(const char *url);
static uint32_t carp_urlhash(const char *);
static uint32_t carp_hosthash(const char *);
static uint32_t carp_combine(const char *url, uint32_t host);
static void carp_recalc(const char *url);
static int becalc_cmp(const struct backend *a, const struct backend *b);

struct backend_cb_data {
struct	backend		*bc_backend;
	backend_cb	 bc_func;
	void		*bc_data;
const	char		*bc_url;
};

static struct backend *
new_backend(name, addr, port)
	const char *name, *addr;
	int port;
{
struct	backend	 *nb;

	if ((nb = wcalloc(1, sizeof(*nb))) == NULL)
		outofmemory();

	nb->be_port = port;
	nb->be_straddr = wstrdup(addr);
	nb->be_name = wstrdup(name);
	nb->be_addr.sin_family = AF_INET;
	nb->be_addr.sin_port = htons(nb->be_port);
	nb->be_addr.sin_addr.s_addr = inet_addr(nb->be_name);
	nb->be_dead = 0;
	nb->be_hash = carp_hosthash(nb->be_name);
	nb->be_load = 1.0;
	return nb;
}

void
add_backend(addr, port)
	const char *addr;
	int port;
{
	if (port < 1 || port > 65535) {
		conf_report_error("invalid backend port: %d", port);
		nerrors++;
		return;
	}
	
	if ((backends = wrealloc(backends, sizeof(struct backend*) * ++nbackends)) == NULL)
		outofmemory();
	backends[nbackends - 1] = new_backend(addr, addr, port);
	wlog(WLOG_NOTICE, "backend: %s:%d", addr, port);
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
get_backend(url, func, data, flags)
	const char *url;
	backend_cb func;
	void *data;
	int flags;
{
struct	backend_cb_data	*cbd;
	int		 s;
	
	WDEBUG((WLOG_DEBUG, "get_backend: called"));
	
	if ((cbd = wmalloc(sizeof(*cbd))) == NULL)
		outofmemory();

	cbd->bc_func = func;
	cbd->bc_data = data;
	cbd->bc_url = url;
	
	for (;;) {
		cbd->bc_backend = next_backend(url);

		if (cbd->bc_backend == NULL) {
			wfree(cbd);
			return -1;
		}

		if ((s = wnet_open("backend connection")) == -1) {
			wlog(WLOG_WARNING, "opening backend socket: %s", strerror(errno));
			wfree(cbd);
			return -1;
		}

		if (connect(s, (struct sockaddr *)&cbd->bc_backend->be_addr, 
		    sizeof(cbd->bc_backend->be_addr)) == 0) {
			WDEBUG((WLOG_DEBUG, "get_backend: connection completed immediately"));
			func(cbd->bc_backend, &fde_table[s], data);
			wfree(cbd);
			return 0;
		}

		if (errno != EINPROGRESS) {
			time_t retry = time(NULL) + config.backend_retry;
			wnet_close(s);
			wlog(WLOG_WARNING, "%s: %s; retry in %d seconds", 
				cbd->bc_backend->be_name, strerror(errno), config.backend_retry);
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
backend_read(e)
	struct fde *e;
{
struct	backend_cb_data	*cbd = e->fde_rdata;
	int		 error = 0, len = sizeof(error);

	getsockopt(e->fde_fd, SOL_SOCKET, SO_ERROR, &error, &len);

	if (error && error != EINPROGRESS) {
		time_t retry = time(NULL) + config.backend_retry;
		wnet_close(e->fde_fd);
		wlog(WLOG_WARNING, "%s: [%d] %s; retry in %d seconds", 
			cbd->bc_backend->be_name, error, strerror(error), config.backend_retry);
		cbd->bc_backend->be_dead = 1;
		cbd->bc_backend->be_time = time(NULL) + config.backend_retry;
		if (get_backend(cbd->bc_url, cbd->bc_func, cbd->bc_data, 0) == -1) {
			cbd->bc_func(NULL, NULL, cbd->bc_data);
		}

		wfree(cbd);
		return;
	}

	/*
	 * After handing the fd off to the caller, we don't care about it
	 * any more. 
	 */
	wnet_register(e->fde_fd, FDE_WRITE, NULL, NULL);

	cbd->bc_func(cbd->bc_backend, e, cbd->bc_data);
	wfree(cbd);
}

static struct backend *
next_backend(url)
	const char *url;
{
static	int	cur = 0;
	int	tried = 0;

	if (config.use_carp)
		carp_recalc(url);

	while (tried++ <= nbackends) {
		time_t now = time(NULL);

		if (cur >= nbackends)
			cur = 0;

		if (backends[cur]->be_dead && now >= backends[cur]->be_time)
			backends[cur]->be_dead = 0;

		if (backends[cur]->be_dead) {
			cur++;
			continue;
		}

		return backends[cur++];
	}

	return NULL;
}

static uint32_t
carp_urlhash(str)
	const char *str;
{
	uint32_t h = 0;
	for (; *str; ++str)
		h += rotl(h, 19) + *str;
	return h;
}

static uint32_t
carp_hosthash(str)
	const char *str;
{
	uint32_t h = carp_urlhash(str) * 0x62531965;
	return rotl(h, 21);
}

static uint32_t
carp_combine(url, host)
	const char *url;
	uint32_t host;
{
	uint32_t c = carp_urlhash(url) ^ host;
	c += c * 0x62531965;
	return rotl(c, 21);
}

static void
carp_calc(void)
{
struct	backend *be, *prev;
	int	 i, j;

	backends[0]->be_carp = pow((nbackends * backends[0]->be_load), 1.0 / nbackends);
	for (i = 1; i < nbackends; ++i) {
		float l = 0;
		be = backends[i];
		prev = backends[i - 1];
		be->be_carplfm = ((nbackends-i+1) * (be->be_load - prev->be_load));
		for (j = 0; j < i; ++j)
			l *= backends[j]->be_carp;
		be->be_carp /= l;
		be->be_carp += pow(prev->be_carp, nbackends-i+1);
		be->be_carp = pow(be->be_carp, 1/(nbackends-i+1));
	}
}

static void
carp_recalc(url)
	const char *url;
{
	uint32_t	hash;
	int		i;
	for (i = 0; i < nbackends; ++i) {
		hash = carp_urlhash(url) ^ backends[i]->be_hash;
		hash += hash * 0x62531965;
		hash = rotl(hash, 21);
		hash *= backends[i]->be_carplfm;
		backends[i]->be_carp = hash;
	}
	qsort(backends, nbackends, sizeof(struct backend *), becalc_cmp);
}

static int
becalc_cmp(a, b)
const 	struct	backend	*a, *b;
{
	return a->be_carp - b->be_carp;
}
