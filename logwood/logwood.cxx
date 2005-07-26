/*
 * Logwood: Convert Squid logs to MySQL database.
 */

#include <sys/types.h>

#include <cstdio>
#include <cstring>
#include <cctype>
#include <cstdlib>
#include <ctime>
#include <csignal>
#include <cerrno>
#include <utility>
#include <memory>
using std::auto_ptr;

#include <dirent.h>
#include <syslog.h>
#include <unistd.h>
#include <pthread.h>

#include <glib.h>
#include <mysql.h>
#include <pcre.h>

#include "lwsql.hxx"
using namespace sql;

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

static void *thread_run(void *ign);
static void process_file(FILE *);
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

int 
main(int argc, char *argv[])
{
FILE		*f;
char		 lang[32];
int		 c;
int		 Fflag = 0;

	while ((c = getopt(argc, argv, "d:F")) != -1) {
		switch (c) {
		case 'd':
			cfgdir = optarg;
			break;
		case 'F':
			Fflag++;
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

	g_hash_table_insert(all_projects, (void *)"wikipedia", (void *)&one);
	g_hash_table_insert(all_projects, (void *)"wikimedia", (void *)&one);
	g_hash_table_insert(all_projects, (void *)"wikibooks", (void *)&one);
	g_hash_table_insert(all_projects, (void *)"wikinews", (void *)&one);
	g_hash_table_insert(all_projects, (void *)"wiktionary", (void *)&one);
	g_hash_table_insert(all_projects, (void *)"wikisource", (void *)&one);
	g_hash_table_insert(all_projects, (void *)"wikiquote", (void *)&one);

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

	if (!Fflag)
		daemon(0, 0);
	openlog("logwood", LOG_PID | (Fflag ? LOG_PERROR : 0), LOG_DAEMON);
	syslog(LOG_NOTICE, "startup");
	for (c = 0; c < NTHREADS; ++c)
		pthread_create(&threads[c], NULL, thread_run, NULL);
	for (c = 0; c < NTHREADS; ++c)
		pthread_join(threads[c], NULL);

	return 0;
}

static void *
thread_run(void *ign)
{
	for (;;) {
	DIR             *dir;
	struct dirent   *de;
	char            *fname = NULL;
	char            **ldir;
	FILE		*in;

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

		for (int ok = 0; !ok; ) {
			try {
				ok = 1;
				process_file(in);
			} catch (std::exception &e) {
				syslog(LOG_WARNING, "MySQL error (reconnecting): %s", e.what());
				ok = 0;
			}
		}
		fclose(in);
		syslog(LOG_INFO, "finished %s", fname);
		g_free(fname);
	}
}

static void
process_file(FILE *in)
{
char		 line[65535];
char		*reqtype, *reqtime, *status, *url, *host, *path, *lang, *agent = NULL, *refer = NULL;
char		*nagent, *nrefer;
char		*s;
struct tm	*tm;
time_t		 tmt;
auto_ptr<connection>	 conn;

auto_ptr<statement>	 stmt_insert_url;
auto_ptr<statement>	 stmt_incr_hour;
auto_ptr<statement>	 stmt_incr_wday;
auto_ptr<statement>	 stmt_update_agent;
auto_ptr<statement>	 stmt_update_refer;

unsigned long long	 site_id;
int			 ref_was_grouped, agent_was_grouped;

	conn.reset(connection::create(dbname, dbhost, dbuser, dbpass));

	stmt_insert_url.reset		(conn->prepare("CALL update_url_count(?, ?, ?)"));
	stmt_incr_hour.reset		(conn->prepare("CALL update_hour_count(?, ?)"));
	stmt_incr_wday.reset		(conn->prepare("CALL update_wday_count(?, ?)"));
	stmt_update_agent.reset		(conn->prepare("CALL update_agent(?, ?, ?)"));
	stmt_update_refer.reset		(conn->prepare("CALL update_refer(?, ?, ?)"));

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
		 * Insert a URL, if none, and update count + touched.
		 */
		stmt_insert_url->bind<string>(0, host);
		stmt_insert_url->bind<string>(1, path);
		stmt_insert_url->bind(2, (sql::number)0);
		stmt_insert_url->execute();

		/*
		 * Insert the hour.
		 */
		stmt_incr_hour->bind<string>(0, host);
		stmt_incr_hour->bind(1, (sql::number)tm->tm_hour);
		stmt_incr_hour->execute();

		/*
		 * Insert day-of-week.
		 */

		stmt_incr_wday->bind<string>(0, host);
		stmt_incr_wday->bind(1, (sql::number)tm->tm_wday);
		stmt_incr_wday->execute();

		/*
		 * Insert the referer.
		 */
		if (refer && *refer) {
			nrefer = consider_grouping(refer_groups, refer);
			
			if (nrefer) {
				ref_was_grouped = 1;
			} else {
				nrefer = refer;
				ref_was_grouped = 0;
			}
			refer = nrefer;
			if (!strcmp(refer, "<ignore>"))
				refer = NULL;
		}

		if (refer && *refer) {
			stmt_update_refer->bind<string>(0, host);
			stmt_update_refer->bind<string>(1, refer);
			stmt_update_refer->bind(2, (sql::number)ref_was_grouped);
			stmt_update_refer->execute();
		}

		/*
		 * Insert the user-agent.
		 */
		if (agent && *agent) {
			nagent = consider_grouping(agent_groups, agent);
			
			if (nagent) {
				agent_was_grouped = 1;
			} else {
				nagent = agent;
				agent_was_grouped = 0;
			}
			agent = nagent;
			if (!strcmp(agent, "<ignore>"))
				agent = NULL;
		}

		if (agent) {
			stmt_update_agent->bind<string>(0, host);
			stmt_update_agent->bind<string>(1, agent);
			stmt_update_agent->bind(2, (sql::number)agent_was_grouped);
			stmt_update_agent->execute();
		}
	}
}

static int
path_ok(const char *path)
{
	return !strncmp(path, "wiki/", 5);
}

static int
host_ok(const char *host_)
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
read_group(const char *filename)
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
			gr = (struct group *)calloc(sizeof(struct group), 1);
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
consider_grouping(GList *list, char *str)
{
struct group	*gr;

	for (; list; list = list->next) {
		gr = (struct group *)list->data;
		
		if (pcre_exec(gr->gr_re, gr->gr_study, str, strlen(str), 0, 0, NULL, 0) >= 0) {
			return gr->gr_name;
		}
	}
	return NULL;
}

static char *
readfile(const char *name)
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
sigusr1(int)
{
	exitnow = 1;
}

