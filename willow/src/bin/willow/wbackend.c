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

#include "willow.h"
#include "wbackend.h"
#include "wnet.h"
#include "wlog.h"
#include "confparse.h"
#include "wconfig.h"

static struct backend **backends;
int nbackends;

static struct backend *new_backend(const char *, int);
static void backend_read(struct fde *);
static struct backend *next_backend(void);

struct backend_cb_data {
struct	backend		*bc_backend;
	backend_cb	 bc_func;
	void		*bc_data;
};

static struct backend *
new_backend(addr, port)
	const char *addr;
	int port;
{
struct	backend	 *nb;

	if ((nb = wcalloc(1, sizeof(*nb))) == NULL)
		outofmemory();

	nb->be_port = port;
	nb->be_name = wstrdup(addr);
	nb->be_addr.sin_family = AF_INET;
	nb->be_addr.sin_port = htons(nb->be_port);
	nb->be_addr.sin_addr.s_addr = inet_addr(nb->be_name);
	nb->be_dead = 0;
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
	backends[nbackends - 1] = new_backend(addr, port);
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
get_backend(func, data, flags)
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
	
	for (;;) {
		cbd->bc_backend = next_backend();

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
		if (get_backend(cbd->bc_func, cbd->bc_data, 0) == -1) {
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
next_backend(void)
{
static	int	cur = 0;
	int	tried = 0;

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
