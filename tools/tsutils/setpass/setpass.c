/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

/*
 * setpass: set initial LDAP password.
 */

#define _GNU_SOURCE
#define LDAP_DEPRECATED 1

#include	<errno.h>
#include	<string.h>
#include	<stdio.h>
#include	<unistd.h>
#include	<pwd.h>
#include	<utmpx.h>

#include	<ldap.h>

#define SERVER		"localhost"
#define PORT		LDAP_PORT
#define SECRET		"/etc/ldap_secret"
#define ADMIN_DN 	"cn=Directory Manager"
#define BASE_DN		"ou=People,o=toolserver"

char *
get_ldap_secret(void)
{
FILE		*f;
static char	 buf[128];
size_t		 len;

	if ((f = fopen(SECRET, "r")) == NULL) {
		(void) fprintf(stderr,
			"setpass: cannot open LDAP secret: %s\n",
			strerror(errno));
		return NULL;
	}

	if (fgets(buf, sizeof buf, f) == NULL) {
		(void) fprintf(stderr,
			"setpass: cannot read LDAP secret: %s\n",
			strerror(errno));
		return NULL;
	}

	fclose(f);

	len = strlen(buf);
	if (len && buf[len - 1] == '\n')
		buf[len - 1] = '\0';

	return buf;
}

int
check_utmp(char *username)
{
struct utmpx	*utent;
char		*tty;
	if ((tty = ttyname(0)) == NULL)
		return 0;

	if (!strncmp(tty, "/dev/", 5))
		tty += 5;

	while ((utent = getutxent()) != NULL) {
		if (utent->ut_type != USER_PROCESS)
			continue;
		if (!utent->ut_line || strcmp(utent->ut_line, tty))
			continue;
		if (strcmp(utent->ut_user, username))
			continue;
		return 1;
	}
	return 0;
}

int
main(argc, argv)
	int argc;
	char **argv
#ifdef __GNUC__
	__attribute__((unused))
#endif
	;
{
LDAP		*conn;
char		*secret, *userdn;
struct passwd	*pwd;
int		 err;
char		*attrs[2];
LDAPMessage	*result, *ent;
char		**vals;
LDAPMod		 mod;
LDAPMod		*mods[2];
char		*newpass, *verify;

	if (argc != 1) {
		(void) fprintf(stderr, "usage: setpass\n");
		return 1;
	}

	if ((conn = ldap_init(SERVER, PORT)) == NULL) {
		(void) fprintf(stderr,
			"setpass: cannot connect to LDAP server: %s:%d: %s\n",
			SERVER, PORT, strerror(errno));
		return 1;
	}

	if ((secret = get_ldap_secret()) == NULL)
		return 1;

	setegid(getgid());

	if ((err = ldap_simple_bind_s(conn, ADMIN_DN, secret)) != 0) {
		(void) fprintf(stderr,
			"setpass: cannot bind as %s: %s\n",
			ADMIN_DN, ldap_err2string(err));
		return 1;
	}

	memset(secret, 0, strlen(secret));

	if (!isatty(0) || !isatty(1) || !isatty(2)) {
		(void) fprintf(stderr, "setpass: must be run from a terminal\n");
		return 1;
	}

	if ((pwd = getpwuid(getuid())) == NULL) {
		(void) fprintf(stderr, "setpass: you don't exist\n");
		return 1;
	}

	if (!check_utmp(pwd->pw_name)) {
		(void) fprintf(stderr, "setpass: you don't seem to be logged in\n");
		return 1;
	}

	asprintf(&userdn, "uid=%s,%s", pwd->pw_name, BASE_DN);

	/*
	 * Make sure the user's password is currently {crypt}!, i.e. not set.  
	 * Users who already have a password should use passwd(1) instead of 
	 * this program.
	 */
	attrs[0] = "userPassword";
	attrs[1] = NULL;
	err = ldap_search_s(conn, userdn, LDAP_SCOPE_BASE,
			"(objectclass=posixAccount)",
			attrs, 0, &result);
	if (err) {
		ldap_perror(conn,
			"setpass: retrieving current userPassword");
		return 1;
	}

	if ((ent = ldap_first_entry(conn, result)) == NULL) {
		(void) fprintf(stderr,
			"setpass: no result when looking for current userPassword\n");
		return 1;
	}

	if ((vals = ldap_get_values(conn, ent, "userPassword")) == NULL
	    || vals[0] == NULL) {
		(void) fprintf(stderr,
			"setpass: object has no userPassword\n");
		return 1;
	}

	if (strcmp(vals[0], "{crypt}!")) {
		(void) fprintf(stderr, "setpass: password already set\n");
		(void) fprintf(stderr, "setpass: use passwd(1) to change your password\n");
		return 1;
	}

	if ((newpass = getpass("Enter new password: ")) == NULL)
		return 1;
	if ((verify = getpass("Re-enter password: ")) == NULL)
		return 1;
	if (strcmp(newpass, verify)) {
		(void) fprintf(stderr, "setpass: passwords don't match\n");
		return 1;
	}

	memset(&mod, 0, sizeof(mod));
	mod.mod_op = LDAP_MOD_REPLACE;
	mod.mod_type = "userPassword";
	attrs[0] = newpass;
	mod.mod_values = attrs;

	mods[0] = &mod;
	mods[1] = NULL;

	if ((err = ldap_modify_s(conn, userdn, mods)) != 0) {
		ldap_perror(conn, "setpass: setting new password");
		return 1;
	}

	(void) fprintf(stderr, "setpass: new password successfully set\n");
	return 0;
}
