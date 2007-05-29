/*
 * Print all expired accounts.
 * $Id: lsexp.c 21740 2007-05-01 00:39:42Z river $
 */

#include <sys/types.h>
#include <sys/time.h>
#include <stdio.h>
#include <pwd.h>
#include <time.h>
#include <unistd.h>
#include <errno.h>
#include <string.h>
#include <shadow.h>

int
main(void)
{
struct spwd	*spwent;
time_t		 when, now;

	(void) time(&now);

	while ((spwent = getspent()) != NULL) {
		if (spwent->sp_expire <= 0)
			continue;

		when = spwent->sp_expire * 24 * 60 * 60;

		if (when > now)
			continue;

		(void) printf("%s\n", spwent->sp_namp);
	}

	return 0;
}
