/*
 * Logwood: Convert Squid logs to MySQL database.
 */

#include <stdio.h>
#include <string.h>
#include <ctype.h>
#include <stdlib.h>
#include <time.h>

#include <glib.h>
#include <mysql.h>
#include <pcre.h>

static GHashTable *all_langs;
static GHashTable *all_projects;

struct group {
	char		*gr_pattern;
	char		*gr_name;
	pcre		*gr_re;
	pcre_extra	*gr_study;
};

GList *refer_groups;
GList *agent_groups;

static void process_file(const char *name);

static char	*readfile	(const char *name);

static int	 host_ok	(const char *host);
static int	 path_ok	(const char *path);

static GList	*read_group		(const char *);
static char	*consider_grouping	(GList *, char *);

static int one = 1;
static unsigned long	 lines = 0;

static char *dbuser, *dbpass, *dbhost, *dbname;

#define	STMT_INSERT_SITE 	"INSERT INTO sites (si_name) VALUES (LOWER(?))"
#define	STMT_INSERT_URL 	"INSERT INTO url_id (ur_site, ur_path, ur_grouped) VALUES (?, ?, ?)"
#define STMT_INSERT_COUNT	"INSERT INTO url_count (uc_url_id, uc_count) VALUES (?, 0)"
#define STMT_INCR_COUNT		"UPDATE url_count SET uc_count = uc_count + 1 WHERE uc_url_id = ?"

#define STMT_QUERY_HOUR		"SELECT hr_id FROM hours WHERE hr_site = ? AND hr_hour = ?"
#define STMT_INSERT_HOUR	"INSERT INTO hours (hr_site, hr_hour, hr_count) VALUES (?, ?, 0)"
#define STMT_INCR_HOUR		"UPDATE hours SET hr_count = hr_count + 1 WHERE hr_id = ?"

#define STMT_QUERY_SITE		"SELECT si_id FROM sites WHERE si_name = ?"
#define STMT_QUERY_URL		"SELECT ur_id FROM url_id WHERE ur_site = ? AND ur_path = ?"

#define STMT_QUERY_REF		"SELECT ref_id FROM ref_ids WHERE ref_site = ? AND ref_url = ?"
#define STMT_INSERT_REF		"INSERT INTO ref_ids (ref_site, ref_url, ref_grouped) VALUES (?, ?, ?)"
#define STMT_INSERT_REF_COUNT	"INSERT INTO ref_count (ref_id, ref_count) VALUES (?, 0)"
#define STMT_INCR_REF		"UPDATE ref_count SET ref_count = ref_count + 1, ref_touched = NOW() WHERE ref_id = ?"

#define STMT_QUERY_AGENT	"SELECT ag_id FROM agent_ids WHERE ag_site = ? AND ag_name = ?"
#define STMT_INSERT_AGENT	"INSERT INTO agent_ids (ag_site, ag_name, ag_grouped) VALUES (?, ?, ?)"
#define STMT_INSERT_AGENT_COUNT	"INSERT INTO agent_count (ac_id, ac_count) VALUES (?, 0)"
#define STMT_INCR_AGENT		"UPDATE agent_count SET ac_count = ac_count + 1, ac_touched = NOW() WHERE ac_id = ?"

int 
main(argc, argv)
int 	 argc;
char 	*argv[];
{
FILE	*f;
char	 lang[32];
time_t		 start = time(NULL);

	all_langs = g_hash_table_new(g_str_hash, g_str_equal);
	all_projects = g_hash_table_new(g_str_hash, g_str_equal);

	if ((f = fopen("/home/knshare/langlist", "r")) == NULL) {
		perror("/home/knshare/langlist");
		exit(1);
	}

	while (fgets(lang, sizeof lang, f)) {
		lang[strlen(lang) - 1] = '\0';
		g_hash_table_insert(all_langs, g_strdup(lang), &one);
	}

	fclose(f);

	g_hash_table_insert(all_projects, "wikipedia", &one);
	g_hash_table_insert(all_projects, "wikibooks", &one);
	g_hash_table_insert(all_projects, "wikinews", &one);
	g_hash_table_insert(all_projects, "wikionary", &one);
	g_hash_table_insert(all_projects, "wikisource", &one);
	g_hash_table_insert(all_projects, "wikiquote", &one);

	refer_groups = g_list_alloc();
	agent_groups = g_list_alloc();

	refer_groups = read_group("/etc/logwood/refer_groups");
	agent_groups = read_group("/etc/logwood/agent_groups");

	if ((dbuser = readfile("/etc/logwood/dbuser")) == NULL) {
		fprintf(stderr, "Database name not specified\n");
		exit(1);
	}

	if ((dbhost = readfile("/etc/logwood/dbhost")) == NULL) {
		fprintf(stderr, "Database host not specified\n");
		exit(1);
	}

	if ((dbpass = readfile("/etc/logwood/dbpass")) == NULL) {
		fprintf(stderr, "Database password not specified");
		exit(1);
	}

	if ((dbname = readfile("/etc/logwood/dbname")) == NULL) {
		fprintf(stderr, "Database name not specified");
		exit(1);
	}

	for (argv++; *argv; argv++) {
		process_file(*argv);
	}

	fprintf(stderr, "Processed %lu lines from %ld seconds (%.02f lines/sec)\n",
		lines, (long)(time(NULL) - start), (double)lines/(time(NULL) - start));
	return 0;
}

static void
process_file(name)
const char	*name;
{
FILE		*in;
char		 line[65535];
char		*reqtype, *reqtime, *status, *url, *host, *path, *lang, *agent = NULL, *refer = NULL;
char		*nagent, *nrefer;
char		*s;
struct tm	*tm;
time_t		 tmt;
MYSQL		 connection;

MYSQL_STMT	*stmt_insert_site;
MYSQL_BIND	 bind_insert_site[1];
unsigned long	 bind_insert_site_si_site_length;

MYSQL_STMT	*stmt_insert_url;
MYSQL_BIND	 bind_insert_url[3];
unsigned long	 bind_insert_url_length;

MYSQL_STMT	*stmt_query_site;
MYSQL_BIND	 bind_query_site[1];
MYSQL_BIND	 bind_query_site_result[1];
my_ulonglong	 bind_query_site_si_id;
my_bool		 bind_query_site_si_id_is_null;
unsigned long	 bind_query_site_si_id_length;
unsigned long	 bind_query_site_si_site_length;

MYSQL_STMT	*stmt_query_url;
MYSQL_BIND	 bind_query_url[2];
MYSQL_BIND	 bind_query_url_result[1];
my_ulonglong	 bind_query_url_ur_id;
my_bool		 bind_query_url_ur_id_is_null;
unsigned long	 bind_query_url_ur_id_length;
unsigned long	 bind_query_url_ur_url_length;

MYSQL_STMT	*stmt_insert_count;
MYSQL_BIND	 bind_insert_count[1];

MYSQL_STMT	*stmt_incr_count;
MYSQL_BIND	 bind_incr_count[1];

MYSQL_STMT	*stmt_query_hour;
MYSQL_BIND	 bind_query_hour[2];
MYSQL_BIND	 bind_query_hour_result[1];
my_ulonglong	 bind_query_hour_hr_id;
my_ulonglong	 bind_query_hour_hr_hour;
my_bool		 bind_query_hour_hr_id_is_null;
unsigned long	 bind_query_hour_hr_id_length;

MYSQL_STMT	*stmt_insert_hour;
MYSQL_BIND	 bind_insert_hour[2];

MYSQL_STMT	*stmt_incr_hour;
MYSQL_BIND	 bind_incr_hour[1];

MYSQL_STMT	*stmt_query_ref;
MYSQL_BIND	 bind_query_ref[2];
MYSQL_BIND	 bind_query_ref_result[1];
my_ulonglong	 bind_query_ref_id;
unsigned long	 bind_query_ref_length;
my_bool		 bind_query_ref_id_is_null;
unsigned long	 bind_query_ref_url_length;

MYSQL_STMT	*stmt_insert_ref;
MYSQL_BIND	 bind_insert_ref[3];
my_ulonglong	 bind_insert_ref_was_grouped;

MYSQL_STMT	*stmt_insert_ref_count;
MYSQL_BIND	 bind_insert_ref_count[1];

MYSQL_STMT	*stmt_incr_ref;
MYSQL_BIND	 bind_incr_ref[1];

MYSQL_STMT	*stmt_query_agent;
MYSQL_BIND	 bind_query_agent[2];
MYSQL_BIND	 bind_query_agent_result[1];
my_ulonglong	 bind_query_agent_id;
unsigned long	 bind_query_agent_length;
my_bool		 bind_query_agent_id_is_null;
unsigned long	 bind_query_agent_name_length;

MYSQL_STMT	*stmt_insert_agent;
MYSQL_BIND	 bind_insert_agent[3];
my_ulonglong	 bind_insert_agent_was_grouped;

MYSQL_STMT	*stmt_insert_agent_count;
MYSQL_BIND	 bind_insert_agent_count[1];

MYSQL_STMT	*stmt_incr_agent;
MYSQL_BIND	 bind_incr_agent[1];

	bind_incr_ref[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_incr_ref[0].buffer = &bind_query_ref_id;
	bind_incr_ref[0].is_null = 0;
	bind_incr_ref[0].length = 0;

	bind_insert_ref_count[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_insert_ref_count[0].buffer = &bind_query_ref_id;
	bind_insert_ref_count[0].length = 0;
	bind_insert_ref_count[0].is_null = 0;

	bind_insert_ref[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_insert_ref[0].buffer = &bind_query_site_si_id;
	bind_insert_ref[0].is_null = 0;
	bind_insert_ref[0].length = 0;

	bind_insert_ref[1].buffer_type = MYSQL_TYPE_STRING;
	bind_insert_ref[1].is_null = 0;
	bind_insert_ref[1].length = &bind_query_ref_url_length;

	bind_insert_ref[2].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_insert_ref[2].buffer = &bind_insert_ref_was_grouped;
	bind_insert_ref[2].is_null = 0;
	bind_insert_ref[2].length = 0;

	bind_query_ref_result[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_query_ref_result[0].buffer = &bind_query_ref_id;
	bind_query_ref_result[0].is_null = &bind_query_ref_id_is_null;
	bind_query_ref_result[0].length = &bind_query_ref_length;

	bind_query_ref[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_query_ref[0].buffer = &bind_query_site_si_id;
	bind_query_ref[0].is_null = 0;
	bind_query_ref[0].length = 0;

	bind_query_ref[1].buffer_type = MYSQL_TYPE_STRING;
	bind_query_ref[1].is_null = 0;
	bind_query_ref[1].length = &bind_query_ref_url_length;

	bind_incr_agent[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_incr_agent[0].buffer = &bind_query_agent_id;
	bind_incr_agent[0].is_null = 0;
	bind_incr_agent[0].length = 0;

	bind_insert_agent_count[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_insert_agent_count[0].buffer = &bind_query_agent_id;
	bind_insert_agent_count[0].length = 0;
	bind_insert_agent_count[0].is_null = 0;

	bind_insert_agent[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_insert_agent[0].buffer = &bind_query_site_si_id;
	bind_insert_agent[0].is_null = 0;
	bind_insert_agent[0].length = 0;

	bind_insert_agent[1].buffer_type = MYSQL_TYPE_STRING;
	bind_insert_agent[1].is_null = 0;
	bind_insert_agent[1].length = &bind_query_agent_name_length;

	bind_insert_agent[2].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_insert_agent[2].buffer = &bind_insert_agent_was_grouped;
	bind_insert_agent[2].is_null = 0;
	bind_insert_agent[2].length = 0;

	bind_query_agent_result[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_query_agent_result[0].buffer = &bind_query_agent_id;
	bind_query_agent_result[0].is_null = &bind_query_agent_id_is_null;
	bind_query_agent_result[0].length = &bind_query_agent_length;

	bind_query_agent[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_query_agent[0].buffer = &bind_query_site_si_id;
	bind_query_agent[0].is_null = 0;
	bind_query_agent[0].length = 0;

	bind_query_agent[1].buffer_type = MYSQL_TYPE_STRING;
	bind_query_agent[1].is_null = 0;
	bind_query_agent[1].length = &bind_query_agent_name_length;

	bind_query_site_result[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_query_site_result[0].buffer = (void *)&bind_query_site_si_id;
	bind_query_site_result[0].is_null = &bind_query_site_si_id_is_null;
	bind_query_site_result[0].length = &bind_query_site_si_id_length;

	bind_query_url_result[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_query_url_result[0].buffer = (void *)&bind_query_url_ur_id;
	bind_query_url_result[0].is_null = &bind_query_url_ur_id_is_null;
	bind_query_url_result[0].length = &bind_query_url_ur_id_length;

	bind_query_hour[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_query_hour[0].buffer = &bind_query_site_si_id;
	bind_query_hour[0].is_null = 0;
	bind_query_hour[0].length = 0;

	bind_query_hour[1].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_query_hour[1].buffer = &bind_query_hour_hr_hour;
	bind_query_hour[1].is_null = 0;
	bind_query_hour[1].length = 0;

	bind_incr_hour[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_incr_hour[0].buffer = &bind_query_hour_hr_id;
	bind_incr_hour[0].is_null = 0;
	bind_incr_hour[0].length = 0;

	bind_query_hour_result[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_query_hour_result[0].buffer = &bind_query_hour_hr_id;
	bind_query_hour_result[0].is_null = &bind_query_hour_hr_id_is_null;
	bind_query_hour_result[0].length = &bind_query_hour_hr_id_length;

	bind_insert_hour[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_insert_hour[0].buffer = &bind_query_site_si_id;
	bind_insert_hour[0].is_null = 0;
	bind_insert_hour[0].length = 0;

	bind_insert_hour[1].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_insert_hour[1].buffer = &bind_query_hour_hr_hour;
	bind_insert_hour[1].is_null = 0;
	bind_insert_hour[1].length = 0;

	mysql_init(&connection);
	if (!mysql_real_connect(&connection, dbhost, dbuser, dbpass, dbname, 0, NULL, 0)) {
		fprintf(stderr, "MySQL connection error: %s\n", mysql_error(&connection));
		exit(1);
	}

	stmt_insert_site = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_insert_site, STMT_INSERT_SITE, strlen(STMT_INSERT_SITE));

	stmt_insert_url = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_insert_url, STMT_INSERT_URL, strlen(STMT_INSERT_URL));

	stmt_query_url = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_query_url, STMT_QUERY_URL, strlen(STMT_QUERY_URL));
	mysql_stmt_bind_result(stmt_query_url, bind_query_url_result);

	stmt_query_site = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_query_site, STMT_QUERY_SITE, strlen(STMT_QUERY_SITE));

	mysql_stmt_bind_result(stmt_query_site, bind_query_site_result);

	stmt_insert_count = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_insert_count, STMT_INSERT_COUNT, strlen(STMT_INSERT_COUNT));

	stmt_incr_count = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_incr_count, STMT_INCR_COUNT, strlen(STMT_INCR_COUNT));

	stmt_query_hour = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_query_hour, STMT_QUERY_HOUR, strlen(STMT_QUERY_HOUR));
	mysql_stmt_bind_param(stmt_query_hour, bind_query_hour);
	mysql_stmt_bind_result(stmt_query_hour, bind_query_hour_result);

	stmt_insert_hour = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_insert_hour, STMT_INSERT_HOUR, strlen(STMT_INSERT_HOUR));
	mysql_stmt_bind_param(stmt_insert_hour, bind_insert_hour);

	stmt_incr_hour = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_incr_hour, STMT_INCR_HOUR, strlen(STMT_INCR_HOUR));
	mysql_stmt_bind_param(stmt_incr_hour, bind_incr_hour);

	stmt_query_ref = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_query_ref, STMT_QUERY_REF, strlen(STMT_QUERY_REF));
	mysql_stmt_bind_param(stmt_query_ref, bind_query_ref);
	mysql_stmt_bind_result(stmt_query_ref, bind_query_ref_result);

	stmt_insert_ref = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_insert_ref, STMT_INSERT_REF, strlen(STMT_INSERT_REF));
	mysql_stmt_bind_param(stmt_insert_ref, bind_insert_ref);

	stmt_insert_ref_count = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_insert_ref_count, STMT_INSERT_REF_COUNT, strlen(STMT_INSERT_REF_COUNT));
	mysql_stmt_bind_param(stmt_insert_ref_count, bind_insert_ref_count);

	stmt_incr_ref = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_incr_ref, STMT_INCR_REF, strlen(STMT_INCR_REF));
	mysql_stmt_bind_param(stmt_incr_ref, bind_incr_ref);

	stmt_query_agent = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_query_agent, STMT_QUERY_AGENT, strlen(STMT_QUERY_AGENT));
	mysql_stmt_bind_param(stmt_query_agent, bind_query_agent);
	mysql_stmt_bind_result(stmt_query_agent, bind_query_agent_result);

	stmt_insert_agent = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_insert_agent, STMT_INSERT_AGENT, strlen(STMT_INSERT_AGENT));
	mysql_stmt_bind_param(stmt_insert_agent, bind_insert_agent);

	stmt_insert_agent_count = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_insert_agent_count, STMT_INSERT_AGENT_COUNT, strlen(STMT_INSERT_AGENT_COUNT));
	mysql_stmt_bind_param(stmt_insert_agent_count, bind_insert_agent_count);

	stmt_incr_agent = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_incr_agent, STMT_INCR_AGENT, strlen(STMT_INCR_AGENT));
	mysql_stmt_bind_param(stmt_incr_agent, bind_incr_agent);

	if ((in = fopen(name, "r")) == NULL) {
		perror(name);
		return;
	}

	/*
	 * A logfile line looks like this:
	 *   1121479203.630     31 ip.ip.ip.ip TCP_HIT/200 3954 GET http://host/ path - NONE/- text/html [MIME headers]
	 * Each MIME header is separated by a literal "\r\n".
	 */

	while ((s = fgets(line, sizeof line, in)) != NULL) {
		refer = agent = NULL;
		reqtime = s;
		if ((s = strchr(s, ' ')) == NULL)
			continue;
		*s++ = '\0';
		while (isspace(*s))
			s++;
		/* request time */
		if ((s = strchr(s, ' ')) == NULL)
			continue;
		*s++ = '\0';
		while (isspace(*s))
			s++;
		/* IP */
		if ((s = strchr(s, ' ')) == NULL)
			continue;
		*s++ = '\0';
		while (isspace(*s))
			s++;
		/* status */
		status = s;
		if ((s = strchr(s, ' ')) == NULL)
			continue;
		*s++ = '\0';
		while (isspace(*s))
			s++;
		/* file size */
		if ((s = strchr(s, ' ')) == NULL)
			continue;
		*s++ = '\0';
		while (isspace(*s))
			s++;
		/* request type */
		reqtype = s;
		if ((s = strchr(s, ' ')) == NULL)
			continue;
		*s++ = '\0';
		while (isspace(*s))
			s++;
		/* url */
		url = s;
		if ((s = strchr(s, ' ')) == NULL)
			continue;
		*s++ = '\0';
		while (isspace(*s))
			s++;
		/* ? */
		if ((s = strchr(s, ' ')) == NULL)
			continue;
		*s++ = '\0';
		while (isspace(*s))
			s++;
		/* parent */
		if ((s = strchr(s, ' ')) == NULL)
			continue;
		*s++ = '\0';
		while (isspace(*s))
			s++;
		/* content type */
		s = strchr(s, ' ');
		if (s) {
			*s++ = '\0';
			while (isspace(*s))
				s++;
		}

		/* MIME hrs */
		if (s && *s == '[') {
			char *next;
			s++;
			do {
				if (!*s)
					break;
				if ((next = strstr(s, "\\r\\n")) != NULL) {
					*next = '\0';
					next += 2;
				}
#define REFERSTR "Referer: "
#define REFERLEN (sizeof(REFERSTR) - 1)
#define AGENTSTR "User-Agent: "
#define AGENTLEN (sizeof(AGENTSTR) - 1)
				if (!strncasecmp(s, REFERSTR, REFERLEN)) {
					refer = s + REFERLEN;
				} else if (!strncasecmp(s, AGENTSTR, AGENTLEN)) {
					agent = s + AGENTLEN;
				}
			} while (next && *next != ']');
		}
				
		if ((status = strchr(status, '/')) == NULL)
			continue;
		status++;
		if (strcmp(reqtype, "GET"))
			continue;
		if (strncmp(url, "http://", 7))
			continue;
		url += 7;
		host = url;
		if ((s = strchr(url, '/')) == NULL)
			continue;
		*s++ = '\0';
		path = s;

		if (!host_ok(host) || !path_ok(path))
			continue;

		tmt = atoi(reqtime);
		tm = gmtime(&tmt);

		/* 
		 * Line looks okay, insert it to DB 
		 */
		if (mysql_query(&connection, "BEGIN")) {
			fprintf(stderr, "MySQL error (BEGIN): %s\n", mysql_error(&connection));
			exit(1);
		}
		

		/*
		 * Check if the site exists already.
		 */
		bind_query_site[0].buffer_type = MYSQL_TYPE_STRING;
		bind_query_site[0].buffer = host;
		bind_query_site[0].is_null = 0;
		bind_query_site[0].length = &bind_query_site_si_site_length;
		bind_query_site_si_site_length = strlen(host);

		if (mysql_stmt_bind_param(stmt_query_site, bind_query_site)) {
			fprintf(stderr, "MySQL error (bind_query_site): %s\n", mysql_error(&connection));
			exit(1);
		}
		if (mysql_stmt_execute(stmt_query_site)) {
			fprintf(stderr, "MySQL error (executing stmt_query_site): %s\n", mysql_error(&connection));
			exit(1);
		}
				
		if (mysql_stmt_fetch(stmt_query_site)) {
			/*
			 * No site, insert it.
			 */
			bind_insert_site[0].buffer_type = MYSQL_TYPE_STRING;
			bind_insert_site[0].buffer = host;
			bind_insert_site[0].is_null = 0;
			bind_insert_site[0].length = &bind_insert_site_si_site_length;
			bind_insert_site_si_site_length = strlen(host);

			if (mysql_stmt_bind_param(stmt_insert_site, bind_insert_site)) {
				fprintf(stderr, "MySQL error (bind_insert_site): %s\n", mysql_error(&connection));
				exit(1);
			}
			if (mysql_stmt_execute(stmt_insert_site)) {
				fprintf(stderr, "MySQL error (executing stmt_insert_site): %s\n", mysql_error(&connection));
				exit(1);
			}
			bind_query_site_si_id = mysql_stmt_insert_id(stmt_insert_site);
		}
		mysql_stmt_free_result(stmt_query_site);
		if (mysql_query(&connection, "COMMIT")) {
			fprintf(stderr, "MySQL error (COMMIT): %s\n", mysql_error(&connection));
			exit(1);
		}

		/*
		 * Insert a URL, if none.
		 */
		mysql_query(&connection, "BEGIN");

		bind_query_url[0].buffer_type = MYSQL_TYPE_LONGLONG;
		bind_query_url[0].buffer = &bind_query_site_si_id;
		bind_query_url[0].is_null = 0;
		bind_query_url[0].length = 0;

		bind_query_url[1].buffer_type = MYSQL_TYPE_STRING;
		bind_query_url[1].buffer = path;
		bind_query_url[1].is_null = 0;
		bind_query_url[1].length = &bind_query_url_ur_url_length;
		bind_query_url_ur_url_length = strlen(path);

		if (mysql_stmt_bind_param(stmt_query_url, bind_query_url)) {
			fprintf(stderr, "MySQL error (bind_query_url): %s\n", mysql_stmt_error(stmt_query_url));
			exit(1);
		}
		if (mysql_stmt_execute(stmt_query_url)) {
			fprintf(stderr, "MySQL error (executing stmt_query_url): %s\n", mysql_error(&connection));
			exit(1);
		}
		
		if (mysql_stmt_fetch(stmt_query_url)) {
			unsigned long zero = 0;

			/*
			 * URL doesn't exist.
			 */			
			bind_insert_url[0].buffer_type = MYSQL_TYPE_LONGLONG;
			bind_insert_url[0].buffer = (char *)&bind_query_site_si_id;
			bind_insert_url[0].is_null = 0;
			bind_insert_url[0].length = 0;

			bind_insert_url[1].buffer_type = MYSQL_TYPE_STRING;
			bind_insert_url[1].buffer = path;
			bind_insert_url[1].is_null = 0;
			bind_insert_url[1].length = &bind_insert_url_length;
			bind_insert_url_length = strlen(path);

			bind_insert_url[2].buffer_type = MYSQL_TYPE_LONG;
			bind_insert_url[2].buffer = &zero;
			bind_insert_url[2].is_null = 0;
			bind_insert_url[2].length = 0;
			
			mysql_stmt_bind_param(stmt_insert_url, bind_insert_url);
			if (mysql_stmt_execute(stmt_insert_url)) {
				fprintf(stderr, "MySQL error (executing stmt_insert_url): %s\n", mysql_stmt_error(stmt_insert_url));
				exit(1);
			}
			bind_query_url_ur_id = mysql_stmt_insert_id(stmt_insert_url);

			bind_insert_count[0].buffer_type = MYSQL_TYPE_LONGLONG;
			bind_insert_count[0].buffer = &bind_query_url_ur_id;
			bind_insert_count[0].is_null = 0;
			bind_insert_count[0].length = 0;

			mysql_stmt_bind_param(stmt_insert_count, bind_insert_count);
			if (mysql_stmt_execute(stmt_insert_count)) {
				fprintf(stderr, "MySQL error (executing stmt_insert_url): %s\n", 
					mysql_stmt_error(stmt_insert_count));
				exit(1);
			}
		}

		mysql_stmt_free_result(stmt_query_url);

		bind_incr_count[0].buffer_type = MYSQL_TYPE_LONGLONG;
		bind_incr_count[0].buffer = &bind_query_url_ur_id;
		bind_incr_count[0].is_null = 0;
		bind_incr_count[0].length = 0;
		mysql_stmt_bind_param(stmt_incr_count, bind_incr_count);
		if (mysql_stmt_execute(stmt_incr_count)) {
			fprintf(stderr, "MySQL error (executing stmt_incr_count): %s\n", mysql_stmt_error(stmt_incr_count));
			exit(1);
		}
		mysql_query(&connection, "COMMIT");

		/*
		 * Insert the hour.
		 */
		bind_query_hour_hr_hour = tm->tm_hour;
		mysql_query(&connection, "BEGIN");
		if (mysql_stmt_execute(stmt_query_hour)) {
			fprintf(stderr, "MySQL error (executing stmt_query_hour): %s\n", mysql_stmt_error(stmt_query_hour));
			exit(1);
		}
		if (mysql_stmt_fetch(stmt_query_hour)) {
			if (mysql_stmt_execute(stmt_insert_hour)) {
				fprintf(stderr, "MySQL error (executing stmt_insert_hour): %s\n", 
					mysql_stmt_error(stmt_insert_hour));
				exit(1);
			}
			bind_query_hour_hr_id = mysql_stmt_insert_id(stmt_insert_hour);
		}
		mysql_stmt_free_result(stmt_query_hour);
		if (mysql_stmt_execute(stmt_incr_hour)) {
			fprintf(stderr, "MySQL error (executing stmt_incr_hour): %s\n", mysql_stmt_error(stmt_incr_hour));
			exit(1);
		}
		mysql_query(&connection, "COMMIT");

		/*
		 * Insert the referer.
		 */
		if (refer) {
			nrefer = consider_grouping(refer_groups, refer);
			
			if (nrefer) {
				bind_insert_ref_was_grouped = 1;
			} else {
				nrefer = refer;
				bind_insert_ref_was_grouped = 0;
			}
			refer = nrefer;
			if (!strcmp(refer, "<ignore>"))
				refer = NULL;
		}

		if (refer) {
			mysql_query(&connection, "BEGIN");
			bind_query_ref[1].buffer = refer;
			bind_insert_ref[1].buffer = refer;
			bind_query_ref[1].length = &bind_query_ref_url_length;
			bind_insert_ref[1].length = &bind_query_ref_url_length;
			bind_query_ref_url_length = strlen(refer);
			mysql_stmt_bind_param(stmt_query_ref, bind_query_ref);
			mysql_stmt_execute(stmt_query_ref);
			if (mysql_stmt_fetch(stmt_query_ref)) {
				mysql_stmt_free_result(stmt_query_ref);
				mysql_stmt_bind_param(stmt_insert_ref, bind_insert_ref);
				mysql_stmt_execute(stmt_insert_ref);
				bind_query_ref_id = mysql_stmt_insert_id(stmt_insert_ref);
				mysql_stmt_bind_param(stmt_insert_ref_count, bind_insert_ref_count);
				mysql_stmt_execute(stmt_insert_ref_count);
			} else
				mysql_stmt_free_result(stmt_query_ref);
			mysql_stmt_bind_param(stmt_incr_ref, bind_incr_ref);
			mysql_stmt_execute(stmt_incr_ref);
			mysql_query(&connection, "COMMIT");
		}

		/*
		 * Insert the user-agent.
		 */
		if (agent) {
			nagent = consider_grouping(agent_groups, agent);
			
			if (nagent) {
				bind_insert_agent_was_grouped = 1;
			} else {
				nagent = agent;
				bind_insert_agent_was_grouped = 0;
			}
			agent = nagent;
			if (!strcmp(agent, "<ignore>"))
				agent = NULL;
		}

		if (agent) {
			mysql_query(&connection, "BEGIN");
			bind_query_agent[1].buffer = agent;
			bind_insert_agent[1].buffer = agent;
			bind_query_agent[1].length = &bind_query_agent_name_length;
			bind_insert_agent[1].length = &bind_query_agent_name_length;
			bind_query_agent_name_length = strlen(agent);
			mysql_stmt_bind_param(stmt_query_agent, bind_query_agent);
			mysql_stmt_execute(stmt_query_agent);
			if (mysql_stmt_fetch(stmt_query_agent)) {
				mysql_stmt_free_result(stmt_query_agent);
				mysql_stmt_bind_param(stmt_insert_agent, bind_insert_agent);
				mysql_stmt_execute(stmt_insert_agent);
				bind_query_agent_id = mysql_stmt_insert_id(stmt_insert_agent);
				mysql_stmt_bind_param(stmt_insert_agent_count, bind_insert_agent_count);
				mysql_stmt_execute(stmt_insert_agent_count);
			} else
				mysql_stmt_free_result(stmt_query_agent);
			mysql_stmt_bind_param(stmt_incr_agent, bind_incr_agent);
			mysql_stmt_execute(stmt_incr_agent);
			mysql_query(&connection, "COMMIT");
		}
		++lines;
	}
}

static int
path_ok(path)
const char	*path;
{
	return !strncmp(path, "wiki/", 5);
}

static int
host_ok(host_)
const char	*host_;
{
char	*lang, *proj, *s;
char	*host;
int	 ok;

	lang = host = g_strdup(host_);
	if ((proj = strchr(host, '.')) == NULL) {
		g_free(host);
		return 0;
	}
	*proj++ = '\0';
	if ((s = strchr(proj, '.')) == NULL) {
		g_free(host);
		return 0;
	}
	*s = '\0';
	ok = g_hash_table_lookup(all_langs, lang)
	     && g_hash_table_lookup(all_projects, proj);
	g_free(host);
	return ok;
}

static GList *
read_group(filename)
const char	*filename;
{
GList	*list = NULL;
FILE	*f;

	if ((f = fopen(filename, "r")) != NULL) {
		char line[2048];
		while (fgets(line, sizeof line, f)) {
		char		*s;
		int		 erroff;
		const char	*err;
		struct group	*gr;

			line[strlen(line) - 1] = '\0';
			if ((s = strstr(line, "=")) == NULL)
				continue;
			*s++ = 0;
			gr = calloc(sizeof(struct group), 1);
			gr->gr_pattern = strdup(line);
			gr->gr_name = strdup(s);
			if ((gr->gr_re = pcre_compile(line, 0, &err, &erroff, NULL)) == NULL) {
				fprintf(stderr, "Invalid RE: at \"%s\": %s\n", gr->gr_pattern + erroff, err);
				exit(1);
			}
			gr->gr_study = pcre_study(gr->gr_re, 0, &err);
			
			list = g_list_prepend(list, gr);
		}
		fclose(f);
	}

	return list;
}

static char *
consider_grouping(list, str)
GList		*list;
char	*str;
{
struct group	*gr;

	for (; list; list = list->next) {
		gr = list->data;
		
		if (pcre_exec(gr->gr_re, gr->gr_study, str, strlen(str), 0, 0, NULL, 0) >= 0) {
			return gr->gr_name;
		}
	}
	return NULL;
}

static char *
readfile(name)
const char	*name;
{
FILE	*f;
char	 str[256];

	if ((f = fopen(name, "r")) == NULL)
		return NULL;

	if (!fgets(str, sizeof str, f)) {
		fclose(f);
		return NULL;
	}

	str[strlen(str) - 1] = '\0';
	fclose(f);
	return g_strdup(str);
}

