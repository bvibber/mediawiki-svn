/* Copyright (c) 2009 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

/*
 * setmail: allow users to change their LDAP 'mail' attribute
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

#include	<readline/readline.h>

#define SERVER		"localhost"
#define PORT		LDAP_PORT
#define BASE_DN		"ou=People,o=toolserver"

char *
get_ldap_secret(void)
{
static	char *secret;
	char *pw;

	if ((pw = getpass("Enter LDAP password: ")) == NULL) {
		(void) fprintf(stderr,
			"setmail: cannot read LDAP password\n");
		return NULL;
	}

	secret = strdup(pw);
	return secret;
}

int
main(argc, argv)
	int argc;
	char **argv;
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
char		*input, *newmail;

	if ((conn = ldap_init(SERVER, PORT)) == NULL) {
		(void) fprintf(stderr,
			"setmail: cannot connect to LDAP server: %s:%d: %s\n",
			SERVER, PORT, strerror(errno));
		return 1;
	}

	if (!isatty(0) || !isatty(1) || !isatty(2)) {
		(void) fprintf(stderr, "setmail: must be run from a terminal\n");
		return 1;
	}

	if ((pwd = getpwuid(getuid())) == NULL) {
		(void) fprintf(stderr, "setmail: you don't exist\n");
		return 1;
	}

	asprintf(&userdn, "uid=%s,%s", pwd->pw_name, BASE_DN);
	
	(void) printf("User DN: %s\n", userdn);

	if ((secret = get_ldap_secret()) == NULL)
		return 1;

	if (err = ldap_simple_bind_s(conn, userdn, secret)) {
		(void) fprintf(stderr,
			"setmail: cannot bind as %s: %s\n",
			userdn, ldap_err2string(err));
		return 1;
	}

	memset(secret, 0, strlen(secret));

	/*
	 * Print the user's existing email address.
	 */
	attrs[0] = "mail";
	attrs[1] = NULL;
	err = ldap_search_s(conn, userdn, LDAP_SCOPE_BASE,
			"(objectclass=posixAccount)",
			attrs, 0, &result);
	if (err) {
		ldap_perror(conn,
			"setmail: retrieving current mail address");
		return 1;
	}

	if ((ent = ldap_first_entry(conn, result)) == NULL) {
		(void) fprintf(stderr,
			"setmail: no result when looking for current mail address\n");
		return 1;
	}

	if ((vals = ldap_get_values(conn, ent, "mail")) == NULL
	    || vals[0] == NULL) {
		(void) fprintf(stderr,
			"setmail: object has no mail attribute\n");
		return 1;
	}

	(void) printf("\nCurrent email address: %s\n", vals[0]);

	if ((input = readline("Do you wish to change this address? (yes/no) ")) == NULL)
		return 1;

	if (strcmp(input, "yes"))
		return 0;

	(void) printf("\n");
	if ((newmail = readline("Enter new e-mail address: ")) == NULL)
		return 1;

	(void) printf("\nYour new email address is: %s\n", newmail);
	if ((input = readline("Continue? (yes/no) ")) == NULL)
		return 1;
	if (strcmp(input, "yes"))
		return 0;

	memset(&mod, 0, sizeof(mod));
	mod.mod_op = LDAP_MOD_REPLACE;
	mod.mod_type = "mail";
	attrs[0] = newmail;
	mod.mod_values = attrs;

	mods[0] = &mod;
	mods[1] = NULL;

	if (err = ldap_modify_s(conn, userdn, mods)) {
		ldap_perror(conn, "setmail: setting new mail address");
		return 1;
	}

	(void) printf("setmail: new address successfully set\n");
	return 0;
}
