#include <xti.h>
#include <xti_inet.h>

#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <strings.h>
#include <fcntl.h>

#include "rdcp.h"

int
main(int argc, char *argv[])
{
	int	tfd, n, flags;
	struct sockaddr_in servaddr;
	struct t_call tcall;
	struct t_discon tdiscon;
	struct rdcp_handle *handle;
	struct rdcp_frame frame;
	struct rdcp_prefer opts;

	if (argc != 2) {
		fprintf(stderr, "usage: %s <port>\n", argv[0]);
		return 1;
	}

	tfd = t_open("/dev/tcp", O_RDWR, NULL);
	t_bind(tfd, NULL, NULL);
	bzero(&servaddr, sizeof(servaddr));
	servaddr.sin_family = AF_INET;
	servaddr.sin_port = htons(atoi(argv[1]));
	inet_pton(AF_INET, "127.0.0.1", &servaddr.sin_addr);

	tcall.addr.maxlen = tcall.addr.len = sizeof(servaddr);
	tcall.addr.buf = (void *)&servaddr;

	tcall.opt.len = tcall.udata.len = 0;

	if (t_connect(tfd, &tcall, NULL) < 0) {
		if (t_errno == TLOOK) {
			if ((n = t_look(tfd)) == T_DISCONNECT) {
				tdiscon.udata.maxlen = 0;
				t_rcvdis(tfd, &tdiscon);
				fprintf(stderr, "t_connect: %s\n", strerror(tdiscon.reason));
				return 1;
			} else {
				fprintf(stderr, "t_connect: unexpected error: %d\n", n);
				return 1;
			}
		} else {
			fprintf(stderr, "t_connect: %s\n", t_strerror(t_errno));
			return 1;
		}
	}

	handle = rdcp_handle_alloc();
	opts.rp_rtype = RDCP_RT_VAR;
	opts.rp_rsize = 0;

	if (!RDCP_IS_OK(rdcp_bind(tfd, handle, &opts)))
		return 1;
	
	while (RDCP_IS_OK(n = rdcp_read(handle, &frame))) {
		printf("read a frame.  len=%d data=[%*s]\n", frame.rf_len, frame.rf_len, frame.rf_buf);
		rdcp_frame_free(&frame);
	}

	fprintf(stderr, "rdcp err: %s (XTI error: %s)\n", rdcp_strerror(handle),
		t_strerror(rdcp_xtierrno(handle)));
	rdcp_unbind(handle);
	rdcp_handle_free(handle);
	return 0;
}
