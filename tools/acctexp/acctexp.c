/*
 * Print account expiry date.
 * $Id$
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
main(ac, av)
int	  ac;
char	**av;
{
struct spwd	*spwent;
struct passwd	*pwent;
uid_t		 uid;
time_t		 when;
char		 res[256];
struct tm	 *whentm;
	uid = getuid();
	if ((pwent = getpwuid(uid)) == NULL) {
		fprintf(stderr, "getpwuid: %s\n", strerror(errno));
		return 1;
	}
	if ((spwent = getspnam(pwent->pw_name)) == NULL) {
		fprintf(stderr, "getspnam: %s\n", strerror(errno));
		return 1;
	}
	seteuid(getuid());
	if (spwent->sp_expire == 0) {
		return 0;
	}
	when = spwent->sp_expire * 24 * 60 * 60;
	if (when < 0) {
		return 0;
	}
	if ((whentm = localtime(&when)) == NULL) {
		fprintf(stderr, "localtime: %s\n", strerror(errno));
		return 1;
	}
	strftime(res, sizeof(res) - 1, "%A, %d %B %Y", whentm);
	res[sizeof(res) - 1] = '\0';
	printf("Your account will expire on %s.\n", res);
	return 0;
}
	

	
