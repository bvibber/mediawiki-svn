/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * wbackend: HTTP backend handling.
 */

#include <sys/types.h>
#include <sys/socket.h>

#include <arpa/inet.h>

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <errno.h>

#include "willow.h"
#include "wbackend.h"
#include "wnet.h"
#include "wlog.h"

static struct backend **backends;
static int nbackends;

static struct backend *new_backend(char *);
static void backend_read(struct fde *);
static struct backend *next_backend(void);

struct backend_cb_data {
struct	backend		*bc_backend;
	backend_cb	 bc_func;
	void		*bc_data;
};

static struct backend *
new_backend(addr)
	char *addr;
{
	char	*host = addr, *port;
struct	backend	 *nb;

	if ((nb = malloc(sizeof(*nb))) == NULL) {
		fputs("out of memory\n", stderr);
		abort();
	}

	memset(nb, 0, sizeof(*nb));
	
	if ((port = strchr(host, ':')) != NULL) {
		*port++ = '\0';
		nb->be_port = atoi(port);
	} else
		nb->be_port = 80;
	nb->be_name = strdup(host);
	nb->be_addr.sin_family = AF_INET;
	nb->be_addr.sin_port = htons(nb->be_port);
	nb->be_addr.sin_addr.s_addr = inet_addr(nb->be_name);
	nb->be_okay = 1;
	return nb;
}

void
add_backend(addr)
	char *addr;
{
	if ((backends = realloc(backends, sizeof(struct backend*) * ++nbackends)) == NULL) {
		fputs("out of memory\n", stderr);
		abort();
	}
	backends[nbackends - 1] = new_backend(addr);
}

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

	fclose(f);
}

int
get_backend(func, data)
	backend_cb func;
	void *data;
{
struct	backend_cb_data	*cbd;
	int		 s;
	
	DEBUG((WLOG_DEBUG, "get_backend: called"));
	
	if ((cbd = wmalloc(sizeof(*cbd))) == NULL) {
		fputs("out of memory\n", stderr);
		abort();
	}

	cbd->bc_func = func;
	cbd->bc_data = data;
	cbd->bc_backend = next_backend();
	
	if ((s = wnet_open("backend connection")) == -1) {
		wlog(WLOG_WARNING, "opening backend socket: %s", strerror(errno));
		return -1;
	}

	if (connect(s, (struct sockaddr *)&cbd->bc_backend->be_addr, sizeof(cbd->bc_backend->be_addr)) == 0) {
		DEBUG((WLOG_DEBUG, "get_backend: connection completed immediately"));
		func(cbd->bc_backend, &fde_table[s], data);
		wfree(cbd);
		return 0;
	}

	if (errno != EINPROGRESS) {
		wlog(WLOG_WARNING, "%s: %s", cbd->bc_backend->be_name, strerror(errno));
		return -1;
	}

	DEBUG((WLOG_DEBUG, "get_backend: waiting for connection to complete"));
	wnet_register(s, FDE_WRITE, backend_read, cbd);
	return 0;
}

static void
backend_read(e)
	struct fde *e;
{
struct	backend_cb_data	*cbd = e->fde_rdata;

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

	if (cur >= nbackends)
		cur = 0;

	return backends[cur++];
}
