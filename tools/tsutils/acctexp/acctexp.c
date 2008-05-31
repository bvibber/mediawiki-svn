/*
 * Print account expiry date.
 * $Id: acctexp.c 11210 2005-10-06 09:59:28Z kateturner $
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
main(argc, argv)
	int argc;
	char **argv;
{
struct spwd	*spwent;
struct passwd	*pwent;
char const	*uname = NULL;
uid_t		 uid;
time_t		 when;
char		 res[256];
struct tm	 *whentm;
	if (argc > 1) {
		if (getgid() != getegid()) {
			(void) fprintf(stderr, "only the super-user may view"
				" the account expiry data of another user\n");
			return 1;
		}

		uname = argv[1];
	} else {
		uid = getuid();
		if ((pwent = getpwuid(uid)) == NULL) {
			(void) fprintf(stderr, "getpwuid: %s\n", strerror(errno));
			return 1;
		}

		uname = pwent->pw_name;
	}
	if ((spwent = getspnam(uname)) == NULL) {
		(void) fprintf(stderr, "getspnam: %s\n", strerror(errno));
		return 1;
	}
	(void) seteuid(getuid());
	if (spwent->sp_expire == 0) {
		return 0;
	}
	when = spwent->sp_expire * 24 * 60 * 60;
	if (when < 0) {
		return 0;
	}
	if ((whentm = localtime(&when)) == NULL) {
		(void) fprintf(stderr, "localtime: %s\n", strerror(errno));
		return 1;
	}
	(void) strftime(res, sizeof(res) - 1, "%A, %d %B %Y", whentm);
	res[sizeof(res) - 1] = '\0';
	if (argc > 1)
		(void) printf("The account \"%s\" will expire on %s.\n", uname, res);
	else
		(void) printf("Your account will expire on %s.\n", res);
	return 0;
}
