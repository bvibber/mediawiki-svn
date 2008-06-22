/*
 * Convert days since 1970 to a date.
 * $Id$
 */

#include	<stdio.h>
#include	<stdlib.h>
#include	<time.h>
#include	<string.h>
#include	<errno.h>

int
main(argc, argv)
	int argc;
	char **argv;
{
time_t		 when;
struct tm	*whentm;
char		 res[64];
	if (argc != 2) {
		(void) fprintf(stderr, "usage: %s <days>\n", argv[0]);
		return 1;
	}

	when = atoi(argv[1]) * 24 * 60 * 60;
	if ((whentm = localtime(&when)) == NULL) {
		(void) fprintf(stderr, "localtime: %s\n", strerror(errno));
		return 1;
	}
	(void) strftime(res, sizeof(res) - 1, "%A, %d %B %Y", whentm);
	res[sizeof(res) - 1] = '\0';
	(void) printf("%s\n", res);
	return 0;
}
