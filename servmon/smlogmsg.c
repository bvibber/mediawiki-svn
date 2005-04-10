/* @(#) $Header$ */
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/un.h>

#include <netdb.h>

#include <stdio.h>
#include <string.h>
#include <strings.h>
#include <stdlib.h>
#include <errno.h>
#include <unistd.h>

int tflag, uflag;
const char *progname, *host;
char *level = "0";

static void usage(void);
static int sock_unix(const char *hostname);
static int sock_tcp(const char *pathname);

int
main(int argc, char *argv[])
{
struct	iovec	 iovec[3];
	int	 s, i;
	char	*msg;
	size_t	 len;

	progname = argv[0];

	while ((i = getopt(argc, argv, "tul:H:h:"
#ifdef __linux__
					"+"
#endif
					)) != -1) {
		switch(i) {
			case 't':
				tflag++;
				break;
			case 'u':
				uflag++;
				break;
			case 'l':
				level = optarg;
				break;
			case 'H':
				host = optarg;
				break;
			case 'h':
			default:
				usage();
		}
	}

	argc -= optind;
	argv += optind;
	
	if (argc < 1) {
		fprintf(stderr, "%s: not enough arguments\n", progname);
		usage();
	}
	
	if (tflag && uflag) {
		fprintf(stderr, "%s: -t and -u may not be specified together\n", progname);
		exit(8);
	}

	if (tflag && !host) {
		fprintf(stderr, "%s: -t requires -H <host>\n", progname);
		exit(8);
	}

	if (tflag)
		s = sock_tcp(host);
	else
		s = sock_unix(host ? host : "/tmp/servmon.log");
	
	len = 1;
	for (i = 0; i < argc; ++i)
		len += strlen(argv[i]) + 1;
	msg = alloca(len);
	*msg = '\0';
	for (i = 0; i < argc; ++i) {
		strcat(msg, argv[i]);
		strcat(msg, " ");
	}

	/*
	 * servmon truncates messages longer than this.
	 */
	if (strlen(msg) > 4096)
		msg[4096] = '\0';

	iovec[0].iov_base = level;
	iovec[0].iov_len = strlen(level);
	iovec[1].iov_base = " ";
	iovec[1].iov_len = 1;
	iovec[2].iov_base = msg;
	iovec[2].iov_len = strlen(msg);

	if (writev(s, iovec, 3) < 0) {
		perror("write");
		exit(8);
	}

	exit(0);
}

static void
usage(void)
{
	fprintf(stderr, "usage: %s [-ut] [-l level] [-H host] <message>\n", progname);
	exit(8);
}

static int
sock_unix(const char *sockname)
{
struct	sockaddr_un 	sa;
	int		s;
	socklen_t	len = sizeof(sa);

	bzero(&sa, sizeof(sa));
	sa.sun_family = AF_UNIX;
	strncpy(sa.sun_path, sockname, sizeof(sa.sun_path) - 1);
	
	if ((s = socket(PF_UNIX, SOCK_STREAM, 0)) < 0) {
		perror("socket");
		exit(8);
	}

	if (connect(s, (struct sockaddr *) &sa, len) < 0) {
		perror("connect");
		exit(8);
	}
	
	return s;
}

static int
sock_tcp(const char *hostname)
{
struct	sockaddr_storage	sa;
struct	addrinfo		hints, *res, *each;
	int			i;
	
	bzero(&sa, sizeof(sa));
	bzero(&hints, sizeof(hints));
	hints.ai_socktype = SOCK_STREAM;

	if ((i = getaddrinfo(hostname, "8577", &hints, &res)) != 0) {
		fprintf(stderr, "%s: %s\n", progname, gai_strerror(i));
		exit(8);
	}

	for (each = res; each; each = each->ai_next) {
		int s;
		if ((s = socket(each->ai_family, each->ai_socktype, each->ai_protocol)) < 0) {
			fprintf(stderr, "%s: socket: %s\n", progname, strerror(errno));
			continue;
		}

		if (connect(s, each->ai_addr, each->ai_addrlen) < 0) {
			fprintf(stderr, "%s: connect: %s\n", progname, strerror(errno));
			continue;
		}
			
		freeaddrinfo(res);
		return s;
	}

	freeaddrinfo(res);
	exit(8);
}
