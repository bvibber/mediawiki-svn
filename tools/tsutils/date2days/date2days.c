/*
 * Convert a date to days since 1970.
 * $Id$
 */

#define _XOPEN_SOURCE
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
struct tm	 tm;
char		*end;
	if (argc != 2) {
		(void) fprintf(stderr, "usage: %s <date>\n", argv[0]);
		return 1;
	}

	memset(&tm, 0, sizeof(tm));
	end = strptime(argv[1], "%Y-%m-%d", &tm);
	if (end == NULL || *end) {
		(void) fprintf(stderr, "could not parse: %s\n",
				argv[1]);
		return 1;
	}

	when = mktime(&tm);
	(void) printf("%u %u\n", (unsigned)when, (unsigned) (when / 60 / 60 / 24));
	return 0;
}
