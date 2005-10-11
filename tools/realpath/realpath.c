/*
 * Print real pathname
 * $Id$
 */

#include <stdio.h>
#include <limits.h>
#include <errno.h>
#include <string.h>
#include <stdlib.h>

int
main(ac, av)
int	  ac;
char	**av;
{
char	res[PATH_MAX + 1];
	if (!av[1]) {
		fprintf(stderr, "usage: %s <path>\n", av[0]);
		return 1;
	}
	if ((realpath(av[1], res)) == NULL) {
		fprintf(stderr, "realpath(%s): %s\n", 
			av[1], strerror(errno));
		return 1;
	}
	printf("%s\n", res);
	return 0;
}
