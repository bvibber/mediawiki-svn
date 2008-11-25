/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

/*
 * watcherd: monitor MySQL server and notify users running long queries.
 */

#include	<sys/types.h>
#include	<sys/mman.h>
#include	<sys/stat.h>
#include	<stdint.h>
#include	<stdio.h>
#include	<errno.h>
#include	<string.h>
#include	<unistd.h>
#include	<stdlib.h>
#include	<ctype.h>
#include	<time.h>
#include	<mysql.h>
#include	<fcntl.h>
#include	"subst.h"
#include	"tsutils.h"

#define SLOWSTR " SLOW_OK "

static char const *cfgfile = "/etc/watcherd/watcherd.conf";
static char const *msgfile = "/etc/watcherd/mailmessage";
static char const *writefile = "/etc/watcherd/writemessage";
static int debug;
static char *dbhost, *dbuser, *dbpass, *thishost;
static int warntime;
static MYSQL *conn;
static char hostname[128];
static int iter;

static void usage(void);
static void read_configuration(void);
static int run_check(void);
static void notify_query(char const *id, char const *user, char const *db, char const *query, int t);
static void clean_seen_queries(void);
static void add_seen_query(int64_t);
static int has_warned(int64_t);
static void set_warned(int64_t);
static void add_alias(char const *alias, char const *user);
static char const *find_alias(char const *user);
static void sanitise_query(char *query);

int
main(argc, argv)
	int argc;
	char *argv[];
{
int	 c;
char	*progname = argv[0];

	while ((c = getopt(argc, argv, "f:d")) != -1) {
		switch (c) {
		case 'f':
			cfgfile = optarg;
			break;
		case 'd':
			debug = 1;
			break;
		default:
			usage();
			exit(1);
		}
	}

	read_configuration();

	if (debug == 0) {
		if (daemon_detach(progname) == -1) {
			(void) fprintf(stderr, "daemon: %s\n",
					strerror(errno));
			exit(1);
		}
	}

	if (gethostname(hostname, sizeof(hostname) - 1) == -1) {
		logmsg("warning: could not get hostname: %s", strerror(errno));
		(void) strcpy(hostname, "unknown");
	}

	for (;;) {
	int	i;
		if (conn == NULL) {
			if ((conn = mysql_init(NULL)) == NULL) {
				logmsg("out of memory in mysql_init");
				exit(1);
			}

			if (mysql_real_connect(conn, dbhost, dbuser, dbpass, NULL, 0, NULL,
						0) == NULL) {
				logmsg("mysql_connect: %s", mysql_error(conn));
				mysql_close(conn);
				conn = NULL;
				(void) sleep(300);
				continue;
			}
		}

		if ((i = run_check()) < 0)
			break;
		(void) sleep(i);
	}

	return 0;
}

static void
usage()
{
	(void) fprintf(stderr,
"usage: watcherd [-d] [-f <config>]\n"
		);
}

static void
read_configuration()
{
	FILE	*f;
	char	 line[1024];
	int	 lineno = 0;

	if ((f = fopen(cfgfile, "r")) == NULL) {
		(void) fprintf(stderr, "watcherd: could not open configuration file "
				"%s: %s\n", cfgfile, strerror(errno));
		exit(1);
	}

	while (fgets(line, sizeof(line), f)) {
		char	*opt, *value;
		size_t	 n = strlen(line) - 1;

		if (line[n] == '\n')
			line[n] = '\0';

		lineno++;

		opt = line;
		if ((value = strchr(opt, ' ')) != NULL) {
			*value++ = '\0';

			while (*value == ' ')
				value++;
		}

		if (!strcmp(opt, "dbhost")) {
			strdup_free(&dbhost, value);
		} else if (!strcmp(opt, "dbuser")) {
			strdup_free(&dbuser, value);
		} else if (!strcmp(opt, "dbpass")) {
			strdup_free(&dbpass, value);
		} else if (!strcmp(opt, "warntime")) {
			warntime = atoi(value);
		} else if (!strcmp(opt, "thishost")) {
			strdup_free(&thishost, value);
		} else if (!strcmp(opt, "alias")) {
		char	*user, *realuser;
			user = value;
			if ((realuser = strchr(user, ' ')) == NULL) {
				(void) fprintf(stderr, "\"%s\", line %d: "
						"invalid alias syntax\n",
						cfgfile, lineno);
				exit(1);
			}

			*realuser++ = '\0';
			add_alias(user, realuser);
		} else {
			(void) fprintf(stderr, "\"%s\", line %d: "
					"unknown option \"%s\"\n",
					cfgfile, lineno, opt);
			exit(1);
		}
	}

	(void) fclose(f);
}

static int
run_check()
{
MYSQL_RES	*res;
MYSQL_ROW	 row;

	if (mysql_query(conn, "SHOW FULL PROCESSLIST") != 0) {
		logmsg("error retrieving processlist: %s", mysql_error(conn));
		mysql_close(conn);
		conn = NULL;
		return 300;
	}

	if ((res = mysql_use_result(conn)) == NULL) {
		logmsg("error retrieving result set: %s", mysql_error(conn));
		mysql_close(conn);
		conn = NULL;
		return 300;
	}

	++iter;

	printf("check\n");
	while ((row = mysql_fetch_row(res)) != NULL) {
	uint64_t	qid;
	int		t;
	char		*p;

		/*
		 * 0: id
		 * 1: user
		 * 2: host
		 * 3: db
		 * 4: command
		 * 5: time
		 * 6: state
		 * 7: info
		 */

		if (row[7] == NULL)
			continue;

		qid = strtol(row[0], NULL, 10);
		add_seen_query(qid);

		t = atoi(row[5]);
		if (t < warntime)
			continue;

		p = strchr(row[2], ':');
		if (p != NULL)
			*p = '\0';

		if (strcmp(row[2], thishost))
			continue;

		if (strstr(row[7], SLOWSTR) != NULL)
			continue;

		if (!has_warned(qid)) {
			sanitise_query(row[7]);
			notify_query(row[0], find_alias(row[1]), row[3], row[7], t);
			set_warned(qid);
		}
	}

	mysql_free_result(res);
	clean_seen_queries();
	return 60;
}

static void
notify_query(id, user, db, query, t)
	char const *id, *user, *db, *query;
	int t;
{
subst_t		 s;
char		*msg, timebuf[32], limitbuf[32], *template;
int		 term;
char		 curtime[64];
time_t		 now;

	logmsg("warning \"%s\" about query %s", user, id);

	(void) time(&now);
	(void) strftime(curtime, sizeof(curtime),
			"%Y-%m-%d %H:%M:%S UTC", gmtime(&now));

	s = subst_new();
	subst_add_var(s, "id", id);
	subst_add_var(s, "user", user);
	subst_add_var(s, "db", db);
	subst_add_var(s, "query", query);
	(void) snprintf(timebuf, sizeof(timebuf), "%d", t);
	subst_add_var(s, "time", timebuf);
	(void) snprintf(limitbuf, sizeof(limitbuf), "%d", warntime);
	subst_add_var(s, "limit", limitbuf);
	subst_add_var(s, "hostname", hostname);
	subst_add_var(s, "curtime", curtime);
	subst_add_var(s, "slowstr", SLOWSTR);

	if ((term = get_user_tty(user)) != -1) {
		template = file_to_string(writefile);
		if (template != NULL) {
			msg = subst_run(s, template);
			(void) write(term, msg, strlen(msg));
			free(template);
			free(msg);
			subst_free(s);
			return;
		} else {
			logmsg("warning: cannot open writemsg \"%s\": %s",
					writefile, strerror(errno));
		}
	}

	if ((template = file_to_string(msgfile)) == NULL) {
		logmsg("warning: cannot open msgfile \"%s\": %s",
				msgfile, strerror(errno));
		subst_free(s);
		return;
	}

	msg = subst_run(s, template);
	subst_free(s);
	free(template);

	(void) sendmail(user, msg);
	free(msg);
}

typedef struct query {
	int64_t	q_id;
	int	q_iter;
	int	q_warned;
	struct query *q_next;
} query;

static query querylist;

static void
add_seen_query(qid)
	int64_t qid;
{
query	*q;
	for (q = querylist.q_next; q; q = q->q_next)
		if (q->q_id == qid) {
			q->q_iter = iter;
			return;
		}

	q = calloc(1, sizeof(*q));
	q->q_id = qid;
	q->q_iter = iter;
	q->q_next = querylist.q_next;
	querylist.q_next = q;
}

static void
set_warned(qid)
	int64_t qid;
{
query	*q;
	for (q = querylist.q_next; q; q = q->q_next)
		if (q->q_id == qid) {
			q->q_warned = 1;
			return;
		}
}

static int
has_warned(qid)
	int64_t qid;
{
query	*q;
	for (q = querylist.q_next; q; q = q->q_next)
		if (q->q_id == qid)
			return q->q_warned;
	return 0;
}

static void
clean_seen_queries()
{
query	*newlist = NULL;

	while (querylist.q_next) {
	query	*tmp, *q = querylist.q_next;

		if (q->q_iter == iter) {
			tmp = q->q_next;
			q->q_next = newlist;
			newlist = q;
			querylist.q_next = tmp;
			continue;
		}

		tmp = q;
		querylist.q_next = q->q_next;
		free(q);
	}

	querylist.q_next = newlist;
}

typedef struct alias {
	char *alias;
	char *user;
	struct alias *next;
} alias_t;

static alias_t aliaslist;

static void
add_alias(alias, user)
	char const *alias, *user;
{
alias_t	*a;
	a = calloc(1, sizeof(*a));
	a->alias = strdup(alias);
	a->user = strdup(user);
	a->next = aliaslist.next;
	aliaslist.next = a;
}

static char const *
find_alias(user)
	char const *user;
{
alias_t	*a;
	for (a = aliaslist.next; a; a = a->next)
		if (!strcmp(a->alias, user))
			return a->user;
	return user;
}

static void
sanitise_query(str)
	char *str;
{
	while (*str) {
		if (!isascii(*str) || !isprint(*str))
			*str = '?';
		str++;
	}
}
