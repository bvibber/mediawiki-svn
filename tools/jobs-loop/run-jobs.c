#include <sys/types.h>
#include <pwd.h>
#include <stdio.h>
#include <unistd.h>

int main(int argc, char **argv) {
	if (argc < 2) {
		fprintf(stderr, "No database specified on command line.\n");
		return 1;
	}
	char *database = argv[1];
	
	struct passwd *apache = getpwnam("apache");
	if (apache == NULL) {
		fprintf(stderr, "Could not look up 'apache' user.\n");
		return 1;
	}

	if (setgid(apache->pw_gid)) {
		fprintf(stderr, "Could not set group to apache.\n");
		return 1;
	}
	if (setuid(apache->pw_uid)) {
		fprintf(stderr, "Could not set user to apache.\n");
		return 1;
	}
	
	char dir[] = "/apache/common/php-1.5/maintenance/";
	if (chdir(dir)) {
		fprintf(stderr, "Could not chdir to %s\n", dir);
		return 1;
	}
	
	execl("/usr/local/bin/php",
		"/usr/local/bin/php",
		"runJobs.php",
		database,
		(char *)0);
	fprintf(stderr, "Could not exec php / jobs script.\n");
	return 1;
}
