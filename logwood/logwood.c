/*
 * Logwood: Convert Squid logs to MySQL database.
 */

#include <sys/types.h>

#include <dirent.h>
#include <stdio.h>
#include <string.h>
#include <ctype.h>
#include <stdlib.h>
#include <time.h>
#include <unistd.h>
#include <pthread.h>
#include <syslog.h>
#include <signal.h>
#include <errno.h>

#include <glib.h>
#include <mysql.h>
#include <pcre.h>

static GHashTable *all_langs;
static GHashTable *all_projects;

static GHashTable *site_ids;

struct group {
	char		*gr_pattern;
	char		*gr_name;
	pcre		*gr_re;
	pcre_extra	*gr_study;
};

GList *refer_groups;
GList *agent_groups;

static void *process_file(void *ign);
static void sigusr1(int);
static int exitnow;

static char	*readfile	(const char *name);

static int	 host_ok	(const char *host);
static int	 path_ok	(const char *path);

static GList	*read_group		(const char *);
static char	*consider_grouping	(GList *, char *);

static int one = 1;
static char **logdirs;

static char *dbuser, *dbpass, *dbhost, *dbname;
static const char *cfgdir = "/etc/logwood";
static pthread_mutex_t logdir_mtx = PTHREAD_MUTEX_INITIALIZER;

#define NTHREADS 4
pthread_t	threads[NTHREADS];


#define	STMT_INSERT_SITE 	"INSERT IGNORE INTO sites (si_name) VALUES (LOWER(?))"
#define STMT_QUERY_SITE		"SELECT si_id FROM sites WHERE si_name = ?"

#define STMT_INSERT_COUNT	"INSERT IGNORE INTO url_count (uc_url_id, uc_count) VALUES (?, 0)"
#define STMT_INCR_COUNT		"UPDATE url_count SET uc_count = uc_count + 1 WHERE uc_url_id = ?"

#define STMT_QUERY_HOUR		"SELECT hr_id FROM hours WHERE hr_site = ? AND hr_hour = ?"
#define STMT_INSERT_HOUR	"INSERT IGNORE INTO hours (hr_site, hr_hour, hr_count) VALUES (?, ?, 0)"
#define STMT_INCR_HOUR		"UPDATE hours SET hr_count = hr_count + 1 WHERE hr_id = ?"

#define STMT_QUERY_URL		"SELECT ur_id FROM url_id WHERE ur_site = ? AND ur_path = ?"
#define	STMT_INSERT_URL 	"INSERT IGNORE INTO url_id (ur_site, ur_path, ur_grouped) VALUES (?, ?, ?)"
#define STMT_URL_TOUCHED_DEL	"DELETE FROM url_touched WHERE ur_url_id = ?"
#define STMT_URL_TOUCHED_INS	"INSERT INTO url_touched (ur_url_id) VALUES (?)"

#define STMT_QUERY_REF		"SELECT ref_id FROM ref_ids WHERE ref_site = ? AND ref_url = ?"
#define STMT_INSERT_REF		"INSERT IGNORE INTO ref_ids (ref_site, ref_url, ref_grouped) VALUES (?, ?, ?)"
#define STMT_INSERT_REF_COUNT	"INSERT INTO ref_count (ref_id, ref_count) VALUES (?, 0)"
#define STMT_INCR_REF		"UPDATE ref_count SET ref_count = ref_count + 1, ref_touched = NOW() WHERE ref_id = ?"

#define STMT_QUERY_AGENT	"SELECT ag_id FROM agent_ids WHERE ag_site = ? AND ag_name = ?"
#define STMT_INSERT_AGENT	"INSERT IGNORE INTO agent_ids (ag_site, ag_name, ag_grouped) VALUES (?, ?, ?)"
#define STMT_INSERT_AGENT_COUNT	"INSERT INTO agent_count (ac_id, ac_count) VALUES (?, 0)"
#define STMT_INCR_AGENT		"UPDATE agent_count SET ac_count = ac_count + 1, ac_touched = NOW() WHERE ac_id = ?"

#define STMT_QUERY_WDAY		"SELECT COUNT(*) FROM wdays WHERE wd_site = ? AND wd_day = ?"
#define STMT_INSERT_WDAY	"INSERT IGNORE INTO wdays (wd_site, wd_day, wd_hits) VALUES (?, ?, 0)"
#define STMT_UPDATE_WDAY	"UPDATE wdays SET wd_hits = wd_hits + 1 WHERE wd_site = ? AND wd_day = ?"

int 
main(argc, argv)
int 	 argc;
char 	*argv[];
{
FILE		*f;
char		 lang[32];
int		 c;

	while ((c = getopt(argc, argv, "d:")) != -1) {
		switch (c) {
		case 'd':
			cfgdir = optarg;
			break;
		default:
			exit(1);
		}
	}
	argc -= optind;
	argv += optind;

	all_langs = g_hash_table_new(g_str_hash, g_str_equal);
	all_projects = g_hash_table_new(g_str_hash, g_str_equal);
	site_ids = g_hash_table_new(g_str_hash, g_str_equal);

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
	g_hash_table_insert(all_projects, "wikimedia", &one);
	g_hash_table_insert(all_projects, "wikibooks", &one);
	g_hash_table_insert(all_projects, "wikinews", &one);
	g_hash_table_insert(all_projects, "wiktionary", &one);
	g_hash_table_insert(all_projects, "wikisource", &one);
	g_hash_table_insert(all_projects, "wikiquote", &one);

	refer_groups = g_list_alloc();
	agent_groups = g_list_alloc();

	refer_groups = read_group("refer_groups");
	agent_groups = read_group("agent_groups");

	if ((dbuser = readfile("dbuser")) == NULL) {
		fprintf(stderr, "Database name not specified\n");
		exit(1);
	}

	if ((dbhost = readfile("dbhost")) == NULL) {
		fprintf(stderr, "Database host not specified\n");
		exit(1);
	}

	if ((dbpass = readfile("dbpass")) == NULL) {
		fprintf(stderr, "Database password not specified");
		exit(1);
	}

	if ((dbname = readfile("dbname")) == NULL) {
		fprintf(stderr, "Database name not specified");
		exit(1);
	}

	signal(SIGUSR1, sigusr1);

	logdirs = argv;
	if (argc < 1) {
		fprintf(stderr, "usage: %s <logdir>[,<logdir>...]\n", argv[0]);
		exit(1);
	}

	daemon(0, 0);
	openlog("logwood", LOG_PID, LOG_DAEMON);
	syslog(LOG_NOTICE, "startup");
	for (c = 0; c < NTHREADS; ++c)
		pthread_create(&threads[c], NULL, process_file, NULL);
	for (c = 0; c < NTHREADS; ++c)
		pthread_join(threads[c], NULL);

	return 0;
}

static void *
process_file(ign)
void		*ign;
{
FILE		*in;
char		 line[65535];
char		*reqtype, *reqtime, *status, *url, *host, *path, *lang, *agent = NULL, *refer = NULL;
char		*nagent, *nrefer;
char		*s;
struct tm	*tm;
time_t		 tmt;
MYSQL		 connection;
my_ulonglong	 zero = 0;

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
my_ulonglong	*siteid;

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

MYSQL_STMT	*stmt_query_wday;
MYSQL_BIND	 bind_query_wday[2];
MYSQL_BIND	 bind_query_wday_result[1];
my_ulonglong	 bind_query_wday_day;
my_ulonglong	 bind_query_wday_count;
my_bool		 bind_query_wday_count_is_null;

MYSQL_STMT	*stmt_insert_wday;
MYSQL_BIND	 bind_insert_wday[2];

MYSQL_STMT	*stmt_update_wday;
MYSQL_BIND	 bind_update_wday[2];

MYSQL_STMT	*stmt_url_touched_del;
MYSQL_STMT	*stmt_url_touched_ins;
MYSQL_BIND	 bind_url_touched[1];

#define BIND_LONG(bind, l)					\
	do {							\
		bzero(&bind, sizeof(bind));			\
		bind.buffer_type = MYSQL_TYPE_LONGLONG;		\
		bind.buffer = &l;				\
	} while (0)
#define BIND_LONG_R(bind, l, in)				\
	do {							\
		BIND_LONG(bind, l);				\
		bind.is_null = &in;				\
	} while (0)

	BIND_LONG(bind_query_wday[0], bind_query_site_si_id);
	BIND_LONG(bind_query_wday[1], bind_query_wday_day);

	BIND_LONG_R(bind_query_wday_result[0], bind_query_wday_count, 
			bind_query_wday_count_is_null);

	BIND_LONG_R(bind_query_wday_result[0], bind_query_wday_count,
			bind_query_wday_count_is_null);

	BIND_LONG(bind_insert_wday[0], bind_query_site_si_id);
	BIND_LONG(bind_insert_wday[1], bind_query_wday_day);

	BIND_LONG(bind_url_touched[0], bind_query_url_ur_id);

	bzero(bind_update_wday, sizeof(bind_update_wday));
	bind_update_wday[0].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_update_wday[0].buffer = &bind_query_site_si_id;

	bind_update_wday[1].buffer_type = MYSQL_TYPE_LONGLONG;
	bind_update_wday[1].buffer = &bind_query_wday_day;

	bzero(bind_incr_ref, sizeof(bind_incr_ref));
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
		syslog(LOG_ERR, "MySQL connection error: %s\n", mysql_error(&connection));
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

	stmt_query_wday = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_query_wday, STMT_QUERY_WDAY, strlen(STMT_QUERY_WDAY));
	mysql_stmt_bind_param(stmt_query_wday, bind_query_wday);
	mysql_stmt_bind_result(stmt_query_wday, bind_query_wday_result);

	stmt_insert_wday = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_insert_wday, STMT_INSERT_WDAY, strlen(STMT_INSERT_WDAY));
	mysql_stmt_bind_param(stmt_insert_wday, bind_insert_wday);

	stmt_update_wday = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_update_wday, STMT_UPDATE_WDAY, strlen(STMT_UPDATE_WDAY));
	mysql_stmt_bind_param(stmt_update_wday, bind_update_wday);

	stmt_url_touched_ins = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_url_touched_ins, STMT_URL_TOUCHED_INS, strlen(STMT_URL_TOUCHED_INS));
	mysql_stmt_bind_param(stmt_url_touched_ins, bind_url_touched);

	stmt_url_touched_del = mysql_stmt_init(&connection);
	mysql_stmt_prepare(stmt_url_touched_del, STMT_URL_TOUCHED_DEL, strlen(STMT_URL_TOUCHED_DEL));
	mysql_stmt_bind_param(stmt_url_touched_del, bind_url_touched);

	for (;;) {
	DIR             *dir;
	struct dirent   *de;
	char            *fname;
	char            **ldir;

		if (exitnow) {
			syslog(LOG_NOTICE, "exiting on signal");
			return NULL;
		}

		pthread_mutex_lock(&logdir_mtx);

		for (ldir = logdirs; *ldir; ++ldir) {
			chdir(*ldir);

			if ((dir = opendir(".")) == NULL) {
				syslog(LOG_ERR, "%s: %s", *ldir, strerror(errno));
				return NULL;
			}

			while ((de = readdir(dir)) != NULL) {
				if (*de->d_name == '.')
					continue;
				fname = g_strdup(de->d_name);
				break;
			}
			closedir(dir);

			if (fname)
				break;
		}

		if (fname == NULL) {
			pthread_mutex_unlock(&logdir_mtx);
			sleep(10);
			continue;
		}

		if ((in = fopen(fname, "r")) == NULL) {
			syslog(LOG_ERR, "%s: %s", fname, strerror(errno));
			pthread_mutex_unlock(&logdir_mtx);
			return NULL;
		}

		unlink(fname);
		syslog(LOG_INFO, "processing %s", fname);

		pthread_mutex_unlock(&logdir_mtx);

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
			char *next = s;
			next++;
			do {	
				s = next;
				if ((next = strstr(next, "\\r\\n")) != NULL) {
					*next = '\0';
					next += 4;
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
			} while (next && *next && *next != ']');
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

		s = strchr(host, ':');
		if (s)	
			*s = '\0';

		if (!host_ok(host) || !path_ok(path))
			continue;

		if (refer && strncasecmp(refer, "http://", 7))
			refer = NULL;

		tmt = atoi(reqtime);
		tm = gmtime(&tmt);

		/* 
		 * Line looks okay, insert it to DB 
		 */
		

		/*
		 * Check if the site exists already.
		 */
		siteid = g_hash_table_lookup(site_ids, host);
		if (siteid) {
			bind_query_site_si_id = *(my_ulonglong *)siteid;
		} else {
			bind_insert_site[0].buffer_type = MYSQL_TYPE_STRING;
			bind_insert_site[0].buffer = host;
			bind_insert_site[0].is_null = 0;
			bind_insert_site[0].length = &bind_insert_site_si_site_length;
			bind_insert_site_si_site_length = strlen(host);
			mysql_stmt_bind_param(stmt_insert_site, bind_insert_site);
			mysql_stmt_execute(stmt_insert_site);

			if (!mysql_stmt_affected_rows(stmt_insert_site)) {
				/*
				 * Already existed, check the id.
				 */
				bind_query_site[0].buffer_type = MYSQL_TYPE_STRING;
				bind_query_site[0].buffer = host;
				bind_query_site[0].is_null = 0;
				bind_query_site[0].length = &bind_query_site_si_site_length;
				bind_query_site_si_site_length = strlen(host);

				mysql_stmt_bind_param(stmt_query_site, bind_query_site);
				mysql_stmt_bind_result(stmt_query_site, bind_query_site_result);
				mysql_stmt_execute(stmt_query_site);
				mysql_stmt_fetch(stmt_query_site);
				mysql_stmt_free_result(stmt_query_site);
			} else
				bind_query_site_si_id = mysql_stmt_insert_id(stmt_insert_site);

			siteid = malloc(sizeof(my_ulonglong));
			*siteid = bind_query_site_si_id;
			g_hash_table_insert(site_ids, host, siteid);
		}

		/*
		 * Insert a URL, if none.
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
		mysql_stmt_execute(stmt_insert_url);

		if (!mysql_stmt_affected_rows(stmt_insert_url)) {
			/*
			 * Already existed.
			 */
			bind_query_url[0].buffer_type = MYSQL_TYPE_LONGLONG;
			bind_query_url[0].buffer = &bind_query_site_si_id;
			bind_query_url[0].is_null = 0;
			bind_query_url[0].length = 0;

			bind_query_url[1].buffer_type = MYSQL_TYPE_STRING;
			bind_query_url[1].buffer = path;
			bind_query_url[1].is_null = 0;
			bind_query_url[1].length = &bind_query_url_ur_url_length;
			bind_query_url_ur_url_length = strlen(path);

			mysql_stmt_bind_param(stmt_query_url, bind_query_url);
			mysql_stmt_bind_result(stmt_query_url, bind_query_url_result);
			mysql_stmt_execute(stmt_query_url);
			mysql_stmt_fetch(stmt_query_url);
			mysql_stmt_free_result(stmt_query_url);

		} else {
			bind_query_url_ur_id = mysql_stmt_insert_id(stmt_insert_url);

			bind_insert_count[0].buffer_type = MYSQL_TYPE_LONGLONG;
			bind_insert_count[0].buffer = &bind_query_url_ur_id;
			bind_insert_count[0].is_null = 0;
			bind_insert_count[0].length = 0;

			mysql_stmt_bind_param(stmt_insert_count, bind_insert_count);
			mysql_stmt_execute(stmt_insert_count);
		}

		bind_incr_count[0].buffer_type = MYSQL_TYPE_LONGLONG;
		bind_incr_count[0].buffer = &bind_query_url_ur_id;
		bind_incr_count[0].is_null = 0;
		bind_incr_count[0].length = 0;
		mysql_stmt_bind_param(stmt_incr_count, bind_incr_count);
		mysql_stmt_execute(stmt_incr_count);

		/*
		 * Update last touched.
		 */
		mysql_stmt_execute(stmt_url_touched_del);
		mysql_stmt_execute(stmt_url_touched_ins);

		/*
		 * Insert the hour.
		 */
		bind_query_hour_hr_hour = tm->tm_hour;
		mysql_stmt_bind_param(stmt_insert_hour, bind_insert_hour);
		mysql_stmt_execute(stmt_insert_hour);

		if (!mysql_stmt_affected_rows(stmt_insert_hour)) {
			mysql_stmt_bind_param(stmt_query_hour, bind_query_hour);
			mysql_stmt_bind_result(stmt_query_hour, bind_query_hour_result);
			mysql_stmt_execute(stmt_query_hour);
			mysql_stmt_fetch(stmt_query_hour);
			mysql_stmt_free_result(stmt_query_hour);
		} else
			bind_query_hour_hr_id = mysql_stmt_insert_id(stmt_insert_hour);
		mysql_stmt_bind_param(stmt_incr_hour, bind_incr_hour);
		mysql_stmt_execute(stmt_incr_hour);

		/*
		 * Insert day-of-week.
		 */

		bind_query_wday_day = tm->tm_wday;

		mysql_stmt_bind_param(stmt_insert_wday, bind_insert_wday);
		mysql_stmt_execute(stmt_insert_wday);

		mysql_stmt_bind_param(stmt_update_wday, bind_update_wday);
		mysql_stmt_execute(stmt_update_wday);

		/*
		 * Insert the referer.
		 */
		if (refer && *refer) {
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

		if (refer && *refer) {
			bind_query_ref_url_length = strlen(refer);
			bind_query_ref[1].buffer = refer;
			bind_insert_ref[1].buffer = refer;
			bind_query_ref[1].length = &bind_query_ref_url_length;
			bind_insert_ref[1].length = &bind_query_ref_url_length;
			bind_query_ref_url_length = strlen(refer);

			mysql_stmt_bind_param(stmt_insert_ref, bind_insert_ref);
			mysql_stmt_execute(stmt_insert_ref);
			if (!mysql_stmt_affected_rows(stmt_insert_ref)) {
				mysql_stmt_bind_param(stmt_query_ref, bind_query_ref);
				mysql_stmt_bind_result(stmt_query_ref, bind_query_ref_result);
				mysql_stmt_execute(stmt_query_ref);
				mysql_stmt_fetch(stmt_query_ref);
				mysql_stmt_free_result(stmt_query_ref);
			} else {
				bind_query_ref_id = mysql_stmt_insert_id(stmt_insert_ref);
				mysql_stmt_bind_param(stmt_insert_ref_count, bind_insert_ref_count);
				mysql_stmt_execute(stmt_insert_ref_count);
			}

			mysql_stmt_bind_param(stmt_incr_ref, bind_incr_ref);
			mysql_stmt_execute(stmt_incr_ref);
		}

		/*
		 * Insert the user-agent.
		 */
		if (agent && *agent) {
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
			bind_query_agent_name_length = strlen(agent);
			bind_query_agent[1].buffer = agent;
			bind_insert_agent[1].buffer = agent;
			bind_query_agent[1].length = &bind_query_agent_name_length;
			bind_insert_agent[1].length = &bind_query_agent_name_length;

			mysql_stmt_bind_param(stmt_insert_agent, bind_insert_agent);
			mysql_stmt_execute(stmt_insert_agent);

			if (!mysql_stmt_affected_rows(stmt_insert_agent)) {
				mysql_stmt_bind_param(stmt_query_agent, bind_query_agent);
				mysql_stmt_bind_result(stmt_query_agent, bind_query_agent_result);
				mysql_stmt_execute(stmt_query_agent);
				mysql_stmt_fetch(stmt_query_agent);
				mysql_stmt_free_result(stmt_query_agent);
			} else {
				bind_query_agent_id = mysql_stmt_insert_id(stmt_insert_agent);
				mysql_stmt_bind_param(stmt_insert_agent_count, bind_insert_agent_count);
				mysql_stmt_execute(stmt_insert_agent_count);
			}
			mysql_stmt_bind_param(stmt_incr_agent, bind_incr_agent);
			mysql_stmt_execute(stmt_incr_agent);
		}
	}
	fclose(in);
	syslog(LOG_INFO, "finished %s", fname);
	g_free(fname);
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
	*s++ = '\0';
	ok = !strcmp(s, "org")
	     && g_hash_table_lookup(all_langs, lang)
	     && g_hash_table_lookup(all_projects, proj);
	g_free(host);
	return ok;
}

static GList *
read_group(filename)
const char	*filename;
{
GList	*list = NULL;
char	*fname;
FILE	*f;

	fname = g_strdup_printf("%s/%s", cfgdir, filename);

	if ((f = fopen(fname, "r")) != NULL) {
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
				syslog(LOG_ERR, "Invalid RE: at \"%s\": %s\n", gr->gr_pattern + erroff, err);
				exit(1);
			}
			gr->gr_study = pcre_study(gr->gr_re, 0, &err);
			
			list = g_list_prepend(list, gr);
		}
		fclose(f);
	}

	g_free(fname);
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
char	*fname;
char	 str[256];

	fname = g_strdup_printf("%s/%s", cfgdir, name);

	if ((f = fopen(fname, "r")) == NULL) {
		g_free(fname);
		return NULL;
	}

	if (!fgets(str, sizeof str, f)) {
		g_free(fname);
		fclose(f);
		return NULL;
	}

	str[strlen(str) - 1] = '\0';
	fclose(f);
	g_free(fname);
	return g_strdup(str);
}

static void
sigusr1(sig)
int    sig;
{
	exitnow = 1;
}

