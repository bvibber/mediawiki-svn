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
	struct t_call tcall, *tcallp;
	struct t_discon tdiscon;
	struct t_info tinfo;
	struct t_bind tbind;
	struct rdcp_handle *handle;
	struct rdcp_frame frame;
	struct rdcp_prefer opts;

	if (argc != 2) {
		fprintf(stderr, "usage: %s <port>\n", argv[0]);
		return 1;
	}

	tfd = t_open("/dev/tcp", O_RDWR, &tinfo);

	bzero(&servaddr, sizeof(servaddr));
	servaddr.sin_family = AF_INET;
	servaddr.sin_port = htons(atoi(argv[1]));
	servaddr.sin_addr.s_addr = INADDR_ANY;

	tbind.addr.maxlen = tbind.addr.len = sizeof(servaddr);
	tbind.addr.buf = (void *)&servaddr;
	tbind.qlen = 5;

	if (t_bind(tfd, &tbind, NULL) < 0) {
		t_error("tbind");
		return 1;
	}

	for (;;) {
		int	connfd, i;

		tcallp = t_alloc(tfd, T_CALL, T_ALL);
		t_listen(tfd, tcallp);
		
		connfd = t_open("/dev/tcp", O_RDWR, NULL);
		t_bind(connfd, NULL, NULL);
		t_accept(tfd, connfd, tcallp);

		t_free(tcallp, T_CALL);
		
		handle = rdcp_handle_alloc();
		opts.rp_rtype = RDCP_RT_VAR;
		opts.rp_rsize = 0;

		if (!RDCP_IS_OK(rdcp_bind(connfd, handle, &opts)))
			return 1;
	
		frame.rf_buf = "test\n";
		frame.rf_len = 5;
		i = rdcp_write(handle, &frame);
		if (!RDCP_IS_OK(i)) {
			fprintf(stderr, "rdcp err: %d\n", i);
			return 1;
		}
		frame.rf_buf = "another test\n";
		frame.rf_len = 13;
		i = rdcp_write(handle, &frame);
		if (!RDCP_IS_OK(i)) {
			fprintf(stderr, "rdcp err: %d\n", i);
			return 1;
		}
		rdcp_unbind(handle);
		rdcp_handle_free(handle);

		t_close(connfd);
	}
}
