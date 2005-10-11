/*
 * Generate a random password.
 * $Id$
 */

#include <sys/types.h>
#include <sys/stat.h>

#include <fcntl.h>
#include <stdio.h>
#include <errno.h>
#include <strings.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>

static char pwchars[] =
	"abcdefghijklmnopqrstuvwxyz"
	"ABCDEFGHIJKLMNOPQRSTUVWXYZ"
	"0123456789"
	"_-$!@#~"
	;

#define DEFLEN 8

int
main(ac, av)
int	  ac;
char	**av;
{
int	 f, i, len;
char	*d, *r;
	len = av[1] ? atoi(av[1]) : DEFLEN;
	if (len < 1 || len > 16) {
		fprintf(stderr, "length must be between 1 and 16\n");
		return 1;
	}
	if ((f = open("/dev/random", O_RDONLY)) == -1) {
		perror("/dev/random");
		return 1;
	}
	d = malloc(len);
	if ((i = read(f, d, len)) < len) {
		fprintf(stderr, "/dev/random: %s\n", 
			len == -1 ? strerror(errno) : "not enough data");
		return 1;
	}
	close(f);
	r = malloc(len + 1);
	bzero(r, len + 1);
	for (i = 0; i < len; ++i)
		r[i] = pwchars[d[i] % (sizeof(pwchars) - 1)];
	fputs(r, stdout);
	fputs("\n", stdout);
	return 0;
}
