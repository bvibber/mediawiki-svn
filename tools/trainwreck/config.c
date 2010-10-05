/* Copyright (c) 2007-2009 River Tarnell <river@loreley.flyingparchment.org.uk>.  */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

#include	<stdio.h>
#include	<string.h>
#include	<stdlib.h>
#include	<errno.h>
#include	<regex.h>

#include	"config.h"

regex_t *db_regex;
regex_t *ignore_regex;

int server_id = 4123;
int *ignorable_errno;
int nignorable;

char *master_host, *master_user, *master_pass;
int master_port;
char *slave_host, *slave_user, *slave_pass;
int slave_port;
int max_buffer = 0;

char *ctldoor;
char *statedir;
int autostart;
int unsynced;

int binlog_v4;

static void do_ignore_errno(unsigned);

static void
strdup_free(s, new)
	char **s;
	char const *new;
{
	if (*s)
		free(*s);
	*s = strdup(new);
}

int
read_configuration(cfgfile)
	char const *cfgfile;
{
FILE	*f;
char	 line[1024];

	if ((f = fopen(cfgfile, "r")) == NULL) {
		(void) fprintf(stderr, "cannot open configuration file \"%s\": %s\n",
			       cfgfile, strerror(errno));
		return -1;
	}

	while (fgets(line, sizeof(line), f)) {
	char	*opt, *value;
	size_t	 n;

		n = strlen(line) - 1;
		if (line[n] == '\n')
			line[n] = '\0';

		if (*line == '#')
			continue;

		if (strlen(line) == 0)
			continue;

		opt = line;
		if ((value = strchr(opt, ' ')) == NULL) {
			(void) fprintf(stderr, "syntax error in configuration file \"%s\"\n",
				       cfgfile);
			return -1;
		}

		*value++ = '\0';

		if (!strcmp(opt, "master-host")) {
			strdup_free(&master_host, value);
		} else if (!strcmp(opt, "master-user")) {
			strdup_free(&master_user, value);
		} else if (!strcmp(opt, "master-pass")) {
			strdup_free(&master_pass, value);
		} else if (!strcmp(opt, "master-port")) {
			master_port = atoi(value);
		} else if (!strcmp(opt, "slave-host")) {
			strdup_free(&slave_host, value);
		} else if (!strcmp(opt, "slave-user")) {
			strdup_free(&slave_user, value);
		} else if (!strcmp(opt, "slave-pass")) {
			strdup_free(&slave_pass, value);
		} else if (!strcmp(opt, "slave-port")) {
			slave_port = atoi(value);
		} else if (!strcmp(opt, "ignore-errno")) {
			do_ignore_errno(atoi(value));
		} else if (!strcmp(opt, "max-buffer")) {
			max_buffer = atoi(value);
		} else if (!strcmp(opt, "server-id")) {
			server_id = atoi(value);
		} else if (!strcmp(opt, "control-door")) {
			strdup_free(&ctldoor, value);
		} else if (!strcmp(opt, "statedir")) {
			strdup_free(&statedir, value);
		} else if (!strcmp(opt, "autostart")) {
			autostart = atoi(value);
		} else if (!strcmp(opt, "fsync")) {
			unsynced = !atoi(value);
		} else if (!strcmp(opt, "binlog-version")) {
		int	vers = atoi(value);
			if (vers == 3)
				binlog_v4 = 0;
			else if (vers == 4)
				binlog_v4 = 1;
			else {
				fprintf(stderr, "unknown binlog version %d\n", vers);
				return -1;
			}
		} else if (!strcmp(opt, "only-replicate")) {
		int	err;
			db_regex = calloc(1, sizeof(*db_regex));
			if ((err = regcomp(db_regex, value, REG_EXTENDED | REG_NOSUB)) != 0) {
			char	errbuf[1024];
				(void) regerror(err, NULL, errbuf, sizeof(errbuf));
				(void) fprintf(stderr, "error in regular expression \"%s\": %s\n",
					       value, errbuf);
				return -1;
			}
		} else if (!strcmp(opt, "ignore-database")) {
		int	err;
			ignore_regex = calloc(1, sizeof(*ignore_regex));
			if ((err = regcomp(ignore_regex, value, REG_EXTENDED | REG_NOSUB)) != 0) {
			char	errbuf[1024];
				(void) regerror(err, NULL, errbuf, sizeof(errbuf));
				(void) fprintf(stderr, "error in regular expression \"%s\": %s\n",
					       value, errbuf);
				return -1;
			}
		} else {
			(void) fprintf(stderr, "unknown option \"%s\" in configuration file \"%s\"\n",
				       opt, cfgfile);
			return -1;
		}
	}

	return 0;
}

static void
do_ignore_errno(n)
	unsigned n;
{
	ignorable_errno = realloc(ignorable_errno, sizeof(int) * (nignorable + 1));
	ignorable_errno[nignorable] = n;
	nignorable++;
}

int
can_ignore_errno(n)
	unsigned n;
{
int	i;
	for (i = 0; i < nignorable; i++)
		if (ignorable_errno[i] == n)
			return 1;
	return 0;
}

