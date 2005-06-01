/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * trickle: copy one directory to another, slowly.
 */

#pragma ident "@(#) $Id$"

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/stat.h>

#include <unistd.h>
#include <string.h>
#include <stdlib.h>
#include <stdio.h>
#include <strings.h>

#include "trickle.h"
#include "rdcp.h"

static int io[2];
static struct rdcp_handle *remote;
int sock;

int proto_rsh(host, command)
	const char *host, *command;
{
	int i;
	pid_t pid;
	if (socketpair(AF_UNIX, SOCK_STREAM, 0, io) < 0) {
		perror("pipe");
		exit(8);
	}
	switch (pid = fork()) {
	case -1:
		perror("fork");
		exit(8);
	case 0:
		close(0);
		close(1);
		close(2);
		dup2(io[1], 0);
		dup2(io[1], 1);
		dup2(1, 2);
		execlp(command, command, host, trickle, "-Z", NULL);
		perror("execlp");
		exit(8);
	}
	return io[0];
}		
	
int
proto_neg(s)
	int s;
{
static	unsigned char	vers = PROTO_VERS;
struct	rdcp_prefer	opts;
struct	rdcp_frame	frame;
	int		i;

	sock = s;
	bzero(&opts, sizeof(opts));
	opts.rp_rtype = RDCP_RT_VAR;
	opts.rp_rsize = 0;
	remote = rdcp_handle_alloc();
	rdcp_handle_type(remote, ROPT_RDWR);

	if (i = rdcp_bind(s, remote, &opts)) {
		fprintf(stderr, "%s: rdcp_bind: %s\n", progname, rdcp_strerror(i));
		exit(8);
	}

	bzero(&frame, sizeof(frame));
	frame.rf_buf = &vers;
	frame.rf_len = 1;
	if (i = rdcp_write(remote, &frame)) {
		fprintf(stderr, "%s: rdcp_write: %s\n", progname, rdcp_strerror(i));
		exit(8);
	}
	if (i = rdcp_read(remote, &frame)) {
		fprintf(stderr, "%s: rdcp_write: %s\n", progname, rdcp_strerror(i));
		exit(8);
	}
	if (frame.rf_len != 1 || *(unsigned char*)frame.rf_buf != PROTO_VERS) {
		fprintf(stderr, "%s: protocol version mismatch (local %d, remote %d)\n",
				progname, PROTO_VERS, (int) *(unsigned char *)frame.rf_buf);
		exit(8);
	}
	rdcp_frame_free(&frame);
	return 0;
}

int proto_close()
{
	rdcp_unbind(remote);
	rdcp_handle_free(remote);
}

void
proto_accept(void)
{
struct	rdcp_frame	frame;
	char		msg = P_ACCEPT;
	int		n;
	frame.rf_len = 1;
	frame.rf_buf = &msg;
	if (n = rdcp_write(remote, &frame)) {
		fprintf(stderr, "%s: write: %s\n", progname, rdcp_strerror(n));
		exit(8);
	}
}

void
proto_decline(void)
{
struct	rdcp_frame	frame;
	char		msg = P_DECLINE;
	int		n;
	frame.rf_len = 1;
	frame.rf_buf = &msg;
	if (n = rdcp_write(remote, &frame)) {
		fprintf(stderr, "%s: write: %s\n", progname, rdcp_strerror(n));
		exit(8);
	}
}

void
proto_putfile(name, sb)
	const char *name;
	struct stat *sb;
{
struct	rdcp_frame	 frame;
	char		*buf;
	size_t		 siz = 0;
	int		 n, type = T_FILE;
	frame.rf_len =
		  sizeof(uint32_t)	/* mtime	*/
		+ sizeof(uint32_t)	/* actime	*/
		+ sizeof(uint32_t)	/* gid		*/
		+ sizeof(uint32_t)	/* uid		*/
		+ sizeof(uint32_t)	/* type		*/
		+ sizeof(uint32_t)	/* mode		*/
		+ strlen(name);
	buf = malloc(frame.rf_len + 1);
	if (sb->st_mode & S_IFDIR)
		type = T_DIR;
	*(uint32_t*)(buf + siz) = htonl(type);
	siz += 4;
	*(uint32_t*)(buf + siz) = htonl(sb->st_mtime);
	siz += 4;
	*(uint32_t*)(buf + siz) = htonl(sb->st_atime);
	siz += 4;
	*(uint32_t*)(buf + siz) = htonl(sb->st_uid);
	siz += 4;
	*(uint32_t*)(buf + siz) = htonl(sb->st_gid);
	siz += 4;
	*(uint32_t*)(buf + siz) = htonl(sb->st_mode);
	siz += 4;
	strcpy(buf + siz, name);
	frame.rf_buf = buf;
	if (n = rdcp_write(remote, &frame)) {
		fprintf(stderr, "%s: write: %s\n", progname, rdcp_strerror(n));
		exit(8);
	}
}

struct pfile *
proto_getfile()
{
struct	pfile		*ret = malloc(sizeof(*ret));
struct	rdcp_frame	 frame;
	int		 n;
	size_t		 siz = 0;
	char		*buf;
	if (n = rdcp_read(remote, &frame))
		exit(8);
	buf = frame.rf_buf;
	if (frame.rf_len < 25)
		exit(8);
	ret->type = htonl(*(uint32_t*)(buf + siz));
	siz += 4;
	ret->mtime = htonl(*(uint32_t*)(buf + siz));
	siz += 4;
	ret->actime = htonl(*(uint32_t*)(buf + siz));
	siz += 4;
	ret->uid = htonl(*(uint32_t*)(buf + siz));
	siz += 4;
	ret->gid = htonl(*(uint32_t*)(buf + siz));
	siz += 4;
	ret->mode = htonl(*(uint32_t*)(buf + siz));
	siz += 4;
	ret->name = malloc(frame.rf_len-24 + 1);
	bcopy((char *)frame.rf_buf + siz, ret->name, frame.rf_len-24);
	ret->name[frame.rf_len-24] = '\0';
	rdcp_frame_free(&frame);
	return ret;
}

char *
proto_readdir()
{
struct	rdcp_frame	frame;
	int		n;
	char		*r;
	if (n = proto_read(&frame))
		exit(8);
	r = malloc(frame.rf_len + 1);
	bcopy(frame.rf_buf, r, frame.rf_len);
	r[frame.rf_len] = '\0';
	rdcp_frame_free(&frame);
	return r;
}

int
proto_read(frame)
	struct rdcp_frame *frame;
{
	return rdcp_read(remote, frame);
}

void
proto_write(frame)
	struct rdcp_frame *frame;
{
	int n;
	if (n = rdcp_write(remote, frame)) {
		fprintf(stderr, "%s: write: %s\n", progname, rdcp_strerror(n));
		exit(8);
	}
}

void
proto_eof()
{
struct	rdcp_frame	frame;
	bzero(&frame, sizeof(frame));
	proto_write(&frame);
}

int
proto_offer(file, sb)
	const char *file;
	struct stat *sb;
{
struct	rdcp_frame	frame;
	proto_putfile(file, sb);
	proto_read(&frame);
	if (frame.rf_len != 1)
		fatal("protocol error");
	if (*(char*)frame.rf_buf == P_ACCEPT)
		return 1;
	if (*(char*)frame.rf_buf == P_DECLINE)
		return 0;
	fatal("protocol error (%d)", (int)*(char *)frame.rf_buf);

}

void
proto_writeblock(b, s)
	void *b;
	size_t s;
{
struct	rdcp_frame	frame;
	frame.rf_buf = b;
	frame.rf_len = s;
	proto_write(&frame);
}
