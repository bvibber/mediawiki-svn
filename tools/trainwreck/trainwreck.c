/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id$ */

/*
 * trainwreck: MySQL replication tool.
 */

#include	<stdio.h>
#include	<string.h>
#include	<errno.h>
#include	<stdlib.h>
#include	<stdarg.h>
#include	<unistd.h>
#include	<regex.h>
#include	<pthread.h>
#include	<door.h>
#include	<port.h>

#include	<my_global.h>
#include	<mysql.h>

#include	"queue.h"
#include	"status.h"
#include	"config.h"

typedef uint32_t logpos_t;

#define BINLOG_NAMELEN	512

#define MASTER_RETRY	30

static void strdup_free(char **, char const *);
static void usage(void);

static void set_thread_name(char const *);
static char const *get_thread_name(void);
static pthread_key_t threadname;

static void *read_master_logs(void *);
static void *slave_write_thread(void *);
static int process_master_logs_once(void);
static int start_slave_write_thread(void);
static int start_master_read_thread(void);
static void stop_slave_write_thread(void);
static void stop_master_read_thread(void);
static void setup_status_door(void);
static void logmsg(char const *fmt, ...);
static MYSQL *master_conn;
static int debug;

static pthread_t master_thread;

static char *binlog_file;
static int64_t binlog_pos = 4;

static int execute_query(MYSQL *, char const *);

#define ET_QUERY 2
#define ET_INTVAR 5
#define ET_ROTATE 4

typedef struct logentry {
	int		 le_type;
	uint32_t	 le_time;
	char		*le_file;
	int64_t		 le_pos;
	char		 le_database[128];
	char		*le_info;
	int64_t	 	 le_insert_id;
	uint64_t	 le_log_pos;
} logentry_t;

static logentry_t *parse_binlog(char unsigned const *, uint32_t len);
static void free_log_entry(logentry_t *);

typedef struct lq_entry {
	logentry_t	*lqe_item;
	struct lq_entry	*lqe_next;
	TAILQ_ENTRY(lq_entry) lqe_q;
} lq_entry_t;

TAILQ_HEAD(lqhead, lq_entry);

typedef struct le_queue {
	pthread_mutex_t		lq_mtx;
	pthread_cond_t		lq_cond;
	struct lqhead		lq_head;
	int			lq_entries;
} le_queue_t;

static void lq_init(le_queue_t *);
static void lq_put(le_queue_t *, logentry_t *);
static logentry_t *lq_get(le_queue_t *);

typedef struct writer {
	pthread_t	 wr_thread;
	logpos_t	 wr_last_executed_pos;
	char		*wr_last_executed_file;
	uint32_t	 wr_last_executed_time;
	le_queue_t 	 wr_log_queue;
	MYSQL		*wr_conn;
	int		 wr_rstat;
	int		 wr_status;
} writer_t;

writer_t writer;

static void writer_init(writer_t *);
static int retrieve_binlog_position(writer_t *);

static pthread_mutex_t rst_mtx = PTHREAD_MUTEX_INITIALIZER,
		       wst_mtx = PTHREAD_MUTEX_INITIALIZER;
static status_t reader_st = ST_STOPPED;
static int master_thread_stop;

static void slave_process_logs(writer_t *);
static int slave_connect(writer_t *);
static void executed_up_to(writer_t *, char const *, logpos_t);
static void sync_ack(writer_t *);
static int nsyncs;
static pthread_mutex_t sync_mtx = PTHREAD_MUTEX_INITIALIZER;
static pthread_cond_t sync_cond = PTHREAD_COND_INITIALIZER;

static int ctl_port;

static int writers_initialising = 1;
static pthread_mutex_t wi_mtx = PTHREAD_MUTEX_INITIALIZER;
static pthread_cond_t wi_cond = PTHREAD_COND_INITIALIZER;
static logpos_t lowest_log_pos;

#define CTL_STOP	1
#define CTL_START	2
#define CTL_SHUTDOWN	3

int
main(argc, argv)
	int argc;
	char *argv[];
{
int		 c, i;
port_event_t	 pe;
char const	*cfgfile = NULL;

	(void) pthread_key_create(&threadname, NULL);
	set_thread_name("main");

	while ((c = getopt(argc, argv, "f:F:p:Dau")) != -1) {
		switch (c) {
		case 'a':
			autostart = 1;
			break;

		case 'f':
			cfgfile = optarg;
			break;

		case 'F':
			strdup_free(&binlog_file, optarg);
			break;

		case 'p':
			binlog_pos = strtoll(optarg, NULL, 10);
			break;

		case 'D':
			debug = 1;
			break;

		case 'u':
			unsynced = 1;
			break;

		default:
			usage();
			exit(1);
		}
	}

	if (!cfgfile) {
		(void) fprintf(stderr, "no configuration file specified\n");
		return 1;
	}

	if ((ctl_port = port_create()) == -1) {
		(void) fprintf(stderr, "cannot create control port: %s\n",
			       strerror(errno));
		return 1;
	}

	if (read_configuration(cfgfile) == -1) {
		(void) fprintf(stderr, "cannot read configuration file\n");
		return 1;
	}

	if (!statedir) {
		(void) fprintf(stderr, "error: statedir not specified in configuration file\n");
		return 1;
	}

	setup_status_door();

	writer_init(&writer);

	if (autostart) {
		(void) start_slave_write_thread();
		(void) start_master_read_thread();
	}

	while (port_get(ctl_port, &pe, NULL) == 0) {
		if (pe.portev_source != PORT_SOURCE_USER) {
			logmsg("got ctl_port event from unknown source?");
			continue;
		}

		switch (pe.portev_events) {
		case CTL_STOP:
			stop_master_read_thread();
			stop_slave_write_thread();
			break;

		case CTL_SHUTDOWN:
			stop_master_read_thread();
			stop_slave_write_thread();
			logmsg("exiting");
			return 0;

		case CTL_START:
			if (start_slave_write_thread() == -1) 
				logmsg("slave thread is already running");
			if (start_master_read_thread() == -1)
				logmsg("master thread is already running");
			break;

		default:
			logmsg("got ctl_port event with unknown request");
			break;
		}
	}
	
	return 0;
}

static void
usage()
{
	(void) fprintf(stderr,
		       "usage: trainwreck [-adu] [-f <cfg>] [-F binlog] [-p binlogpos]\n");
}


static void
strdup_free(s, new)
	char **s;
	char const *new;
{
	if (*s)
		free(*s);
	*s = strdup(new);
}

static void
stop_master_read_thread()
{
	master_thread_stop = 1;
	if (pthread_join(master_thread, NULL) == -1) {
		logmsg("cannot join master thread: %s",
				strerror(errno));
		return;
	}
	logmsg("master thread stopped");
}

static void
stop_slave_write_thread()
{
int	i;
	(void) pthread_cancel(writer.wr_thread);
	if (pthread_join(writer.wr_thread, NULL) == -1) {
		logmsg("cannot join slave thread: %s", strerror(errno));
	}
	mysql_close(writer.wr_conn);
	writer.wr_status = ST_STOPPED;
	logmsg("slave threads stopped");
}

static int
master_connect(void)
{
	if ((master_conn = mysql_init(NULL)) == NULL) {
		logmsg("out of memory in mysql_init");
		reader_st = ST_STOPPED;
		return -1;
	}

	mysql_options(master_conn, MYSQL_READ_DEFAULT_GROUP, "trainwreck-master");

	if (mysql_real_connect(master_conn, master_host, master_user, master_pass, NULL,
				master_port, NULL, 0) == NULL) {
		logmsg("cannot connect to master %s:%d: %s",
				master_host, master_port, mysql_error(master_conn));
		reader_st = ST_STOPPED;
		return -1;
	}

	return 0;
}

static int
start_master_read_thread()
{
	(void) pthread_mutex_lock(&rst_mtx);
	if (reader_st != ST_STOPPED)
		return -1;
	
	reader_st = ST_INITIALISING;
	(void) pthread_mutex_unlock(&rst_mtx);

	for (;;) {
		if (master_connect() == 0)
			break;

		logmsg("master connect failed; sleeping for retry");
		(void) sleep(MASTER_RETRY);
	}

	(void) pthread_create(&master_thread, NULL, read_master_logs, NULL);

	return 0;
}

/*ARGSUSED*/
static void *
read_master_logs(p)
	void *p;
{
	set_thread_name("reader");

	(void) pthread_mutex_lock(&wi_mtx);
	logmsg("waiting for writer to become ready...", writers_initialising);
	while (writers_initialising > 0)
		(void) pthread_cond_wait(&wi_cond, &wi_mtx);
	(void) pthread_mutex_unlock(&wi_mtx);

	logmsg("all writers ready");

	if (binlog_file) {
		binlog_pos = lowest_log_pos;
		logmsg("resuming replication at %s,%lu",
			binlog_file, (unsigned long) binlog_pos);
	}

	for (;;) {
		if (process_master_logs_once() == 0)
			break;

	reconnect:
		(void) sleep(MASTER_RETRY);
		logmsg("reconnecting to master...");
		mysql_close(master_conn);
		if (master_connect() == -1)
			goto reconnect;
	}

	reader_st = ST_STOPPED;
	return NULL;
}

static int
process_master_logs_once()
{
char		 lastdb[128];

	/*
	 * To retrieve binlogs we use the MySQL binlog dump protocol 
	 * (COM_BINLOG_DUMP).  This continues sending binlogs in realtime.
	 *
	 * We remember the database name so that ET_INTVAR events can have a 
	 * dbname (MySQL doesn't provide one).  This works because they are 
	 * always proceeded by a BEGIN or another query on the same database.
	 */

char		 buf[BINLOG_NAMELEN + 10];
	/* So we don't hold rst_mtx across simple_command() */
char		*curfile;
logpos_t	 curpos = 0;
unsigned long	 len;

	logmsg("starting binlog dump...");

	(void) pthread_mutex_lock(&rst_mtx);
	curfile = strdup(binlog_file);
	curpos = binlog_pos;
	(void) pthread_mutex_unlock(&rst_mtx);

	int4store(buf, (uint32_t) curpos);
	int2store(buf + 4, (uint16_t) 0);
	int4store(buf + 6, (uint32_t) server_id);
	len = strlen(curfile);
	if (len > BINLOG_NAMELEN) {
		logmsg("%s,%lu: binlog name is too long",
				curfile, (unsigned long) curpos);
		exit(1);
	}

	(void) memcpy(buf + 10, curfile, len);

	if (simple_command(master_conn, COM_BINLOG_DUMP, buf, len + 10, 1) != 0) {
		logmsg("%s,%lu: error retrieving binlogs from server: (%d) %s",
				curfile, (unsigned long) curpos,
				mysql_errno(master_conn), mysql_error(master_conn));
		return -1;
	}

	for (;;) {
	logentry_t	*ent;
		(void) pthread_mutex_lock(&rst_mtx);
		reader_st = ST_WAIT_FOR_MASTER;
		(void) pthread_mutex_unlock(&rst_mtx);

		len = cli_safe_read(master_conn);

		(void) pthread_mutex_lock(&rst_mtx);
		reader_st = ST_QUEUEING;
		(void) pthread_mutex_unlock(&rst_mtx);

		if (master_thread_stop) {
			mysql_close(master_conn);

			(void) pthread_mutex_lock(&rst_mtx);
			logmsg("shutting down; read up to %s,%lu",
				curfile, (unsigned long) curpos);
			reader_st = ST_STOPPED;
			master_thread_stop = 0;
			free(curfile);
			free(binlog_file);
			binlog_file = NULL;
			(void) pthread_mutex_unlock(&rst_mtx);

			return 0;
		}

		if (len == packet_error) {
			logmsg("%s,%lu: error retrieving binlogs from server: (%d) %s",
				curfile, (unsigned long) curpos,
				mysql_errno(master_conn), mysql_error(master_conn));
			return -1;
		}

		if ((ent = parse_binlog(master_conn->net.read_pos + 1, len - 1)) == NULL) {
			logmsg("failed parsing binlog");
			return -1;
		}

		if (debug)
			logmsg("got binlog event");

		ent->le_file = strdup(curfile);
		curpos = ent->le_pos;

		if (ent->le_database[0])
			(void) strlcpy(lastdb, ent->le_database, sizeof(lastdb));
		else
			(void) strlcpy(ent->le_database, lastdb, sizeof(ent->le_database));

		if (ent->le_type == ET_ROTATE && ent->le_time != 0) {
		int	i;
			executed_up_to(&writer, ent->le_info, 4);

			strdup_free(&curfile, ent->le_info);
			curpos = 4;
			logmsg("rotating to %s,4", curfile);
			free_log_entry(ent);

			(void) pthread_mutex_lock(&rst_mtx);
			strdup_free(&binlog_file, curfile);
			binlog_pos = 4;
			(void) pthread_mutex_unlock(&rst_mtx);
		} else {
			(void) pthread_mutex_lock(&rst_mtx);
			binlog_pos = curpos;
			(void) pthread_mutex_unlock(&rst_mtx);

			if ((db_regex == NULL || regexec(db_regex, ent->le_database, 0, NULL, 0) == 0) &&
			    (ignore_regex == NULL || regexec(ignore_regex, ent->le_database, 0, NULL, 0) != 0) &&
			    (ent->le_type == ET_INTVAR || ent->le_type == ET_QUERY)) {
				lq_put(&writer.wr_log_queue, ent);
			} else {
				free_log_entry(ent);
			}
		}

	}
}

static logentry_t *
parse_binlog(buf, len)
	char unsigned const *buf;
	uint32_t len;
{
uchar		 type;
logentry_t	*ent;

	/*
	 * Header:
	 *   4 bytes, timestamp
	 *   1 byte, event type
	 *     1: START_EVENT
	 *     2: QUERY_EVENT
	 *   4 bytes, server id
	 *   4 bytes, event total size
	 *   4 bytes, this event's offset
	 *   2 bytes, flags
	 *
	 * Total length: 19
	 */
	if (len < 19) {
		logmsg("binlog is too short");
		return NULL;
	}

	ent = calloc(1, sizeof(*ent));
	
#define ADVANCE(n) { buf += (n);  len -= (n); }
	ent->le_time = uint4korr(buf);
	ADVANCE(4);
	type = *buf;
	ent->le_type = type;
	ADVANCE(1);
	/* server id, don't care */
	ADVANCE(4);
	/* event size, don't care */
	ADVANCE(4);
	/* offset */
	ent->le_pos = uint4korr(buf);
	ADVANCE(4);
	/* flags, don't care */
	ADVANCE(2);

	switch (type) {
	case ET_ROTATE:
		/*
		 * 8 bytes position (always 4)
		 * rest of data is filename
		 */
		if (len <= 9) {
			logmsg("binlog is truncated");
			goto err;
		}

		ent->le_log_pos = uint8korr(buf);

		ADVANCE(8);
		ent->le_info = malloc(len + 1);
		(void) memcpy(ent->le_info, buf, len);
		ent->le_info[len] = '\0';
		return ent;

	case ET_QUERY: {
	int	namelen;
		/*
		 * 4 bytes: thread id
		 * 4 bytes: execution time
		 * 1 byte: length of database name
		 * 2 bytes: error code
		 * rest of data is database name, NUL, query
		 */
		if (len <= 11) {
			logmsg("binlog is truncated");
			goto err;
		}

		ADVANCE(4); /* ignore thread id */
		ADVANCE(4); /* ignore execution time */

		namelen = *buf;
		ADVANCE(1);

		ADVANCE(2);
		if (len <= namelen + 2) {
			logmsg("binlog is truncated");
			goto err;
		}

		(void) strlcpy(ent->le_database, (char const *) buf, sizeof(ent->le_database));
		ADVANCE(namelen + 1);
		ent->le_info = calloc(1, len + 1);
		(void) memcpy(ent->le_info, buf, len);
		return ent;
	}

	case ET_INTVAR:
		/*
		 * 1: id type
		 * 8 bytes: next INSERT_ID
		 */
		 if (len < 9) {
			 logmsg("binlog is truncated");
			 goto err;
		 }

		 ADVANCE(1);
		 ent->le_insert_id = uint8korr(buf);

		 if (debug)
			 (void) printf("got intvar: %llu (%llx)\n", (unsigned long long) ent->le_insert_id,
						 (unsigned long long) ent->le_insert_id);
		 return ent;

	default:
		 /* don't fill anything in, we don't know/care */
		 return ent;
	}

#undef ADVANCE
err:
	free(ent);
	return NULL;
}

static void
logmsg(char const *msg, ...)
{
va_list	ap;
static pthread_mutex_t lock = PTHREAD_MUTEX_INITIALIZER;

	(void) pthread_mutex_lock(&lock);
	(void) fprintf(stderr, "[%s] ", get_thread_name());
	va_start(ap, msg);
	(void) vfprintf(stderr, msg, ap);
	va_end(ap);
	(void) fputs("\n", stderr);
	(void) pthread_mutex_unlock(&lock);
}

static void
free_log_entry(e)
	logentry_t *e;
{
	if (e->le_info)
		free(e->le_info);
	if (e->le_file)
		free(e->le_file);
	free(e);
}

static void
lq_init(q)
	le_queue_t *q;
{
	TAILQ_INIT(&q->lq_head);
	q->lq_entries = 0;
}

static void
lq_put(q, e)
	le_queue_t *q;
	logentry_t *e;
{
lq_entry_t	*entry;
	(void) pthread_mutex_lock(&q->lq_mtx);
	while (q->lq_entries >= max_buffer) {
		if (debug)
			logmsg("queue is full, sleeping...");
		(void) pthread_mutex_unlock(&q->lq_mtx);
	/*	(void) sleep(5);*/
		(void) usleep(5000);
		(void) pthread_mutex_lock(&q->lq_mtx);
	}

	entry = calloc(1, sizeof(lq_entry_t));
	entry->lqe_item = e;
	TAILQ_INSERT_TAIL(&q->lq_head, entry, lqe_q);
	q->lq_entries++;
	(void) pthread_cond_signal(&q->lq_cond);
	(void) pthread_mutex_unlock(&q->lq_mtx);
}

static void
lq_get_cleanup(mtx)
	void *mtx;
{
	logmsg("shutting down");
	(void) pthread_mutex_unlock((pthread_mutex_t *) mtx);
}

static logentry_t *
lq_get(q)
	le_queue_t *q;
{
lq_entry_t	*qe;
logentry_t	*ent;
	/*
	 * This function is a cancellation point.  Read the comment in 
	 * slave_write_thread for details.
	 */
	(void) pthread_mutex_lock(&q->lq_mtx);

	pthread_cleanup_push(lq_get_cleanup, &q->lq_mtx);
	(void) pthread_setcancelstate(PTHREAD_CANCEL_ENABLE, NULL);

	while (TAILQ_EMPTY(&q->lq_head)) {
		(void) pthread_cond_wait(&q->lq_cond, &q->lq_mtx);
	}

	(void) pthread_setcancelstate(PTHREAD_CANCEL_DISABLE, NULL);
	pthread_cleanup_pop(0);

	qe = TAILQ_FIRST(&q->lq_head);
	TAILQ_REMOVE(&q->lq_head, qe, lqe_q);
	q->lq_entries--;

	(void) pthread_mutex_unlock(&q->lq_mtx);

	ent = qe->lqe_item;
	free(qe);
	return ent;
}

static int
start_slave_write_thread()
{
int	i;
	(void) pthread_mutex_lock(&wst_mtx);
	if (writer.wr_status != ST_STOPPED)
		return -1;

	(void) pthread_mutex_unlock(&wst_mtx);

	(void) pthread_create(&writer.wr_thread, NULL, slave_write_thread, &writer);
	return 0;
}

int
slave_connect(self)
	writer_t	*self;
{
	if ((self->wr_conn = mysql_init(NULL)) == NULL) {
		logmsg("out of memory in mysql_init");
		return -1;
	}

	mysql_options(self->wr_conn, MYSQL_READ_DEFAULT_GROUP, "trainwreck-slave");

	if (mysql_real_connect(self->wr_conn, slave_host, slave_user, slave_pass, NULL,
				slave_port, NULL, 0) == NULL) {
		logmsg("cannot connect to slave %s:%d: %s",
				slave_host, slave_port, mysql_error(self->wr_conn));
		mysql_close(self->wr_conn);
		return -1;
	}

	return 0;
}

static void *
slave_write_thread(p)
	void *p;
{
writer_t	*self = p;
char		 namebuf[16];

	/*
	 * This thread is cancelled by the main thread when we want to stop 
	 * replication.  However, we disallow cancellation at all times except 
	 * during lq_get().  This is okay, because most operations should be 
	 * fairly short; lq_get is the only one that blocks.  lq_get enables 
	 * cancellation while it's running and will do proper cleanup for us.
	 */
	(void) pthread_setcancelstate(PTHREAD_CANCEL_DISABLE, NULL);

	(void) snprintf(namebuf, sizeof(namebuf), "writer");
	set_thread_name(namebuf);

	self->wr_status = ST_INITIALISING;

	for (;;) {
		if (slave_connect(self) == 0)
			break;
		sleep(30);
	}

	if (retrieve_binlog_position(self) != 0) {
		logmsg("could not retrieve binlog position");
		mysql_close(self->wr_conn);
		return NULL;
	}

	(void) pthread_mutex_lock(&wi_mtx);
	writers_initialising--;
	(void) pthread_cond_signal(&wi_cond);
	(void) pthread_mutex_unlock(&wi_mtx);

	for (;;) {
		slave_process_logs(self);
		mysql_close(self->wr_conn);
		sleep(30);

		for (;;) {
			if (slave_connect(self) == 0)
				break;
			sleep(30);
		}
	}
}

void
slave_process_logs(self)
	writer_t	*self;
{
logentry_t	*e;
	self->wr_status = ST_WAIT_FOR_ENTRY;
	while ((e = lq_get(&self->wr_log_queue)) != NULL) {
		if (debug)
			logmsg("%s,%lu [%s]", e->le_file, (unsigned long) e->le_pos,
				e->le_database);

		self->wr_status = ST_EXECUTING;
		self->wr_last_executed_time = e->le_time;
		
		if (e->le_database && *e->le_database)
			if (mysql_select_db(self->wr_conn, e->le_database) != 0) {
				logmsg("%s,%lu: cannot select \"%s\": %s",
					e->le_file, (unsigned long) e->le_pos,
					e->le_database, mysql_error(self->wr_conn));
				mysql_close(self->wr_conn);
				return;
			}

		switch (e->le_type) {
		case ET_INTVAR: {
		char	query[128];
			(void) snprintf(query, sizeof(query), "SET INSERT_ID=%llu",
					(unsigned long long) e->le_insert_id);
			if (execute_query(self->wr_conn, query) != 0) {
				logmsg("%s,%lu: %s: query failed (%u: %s): \"%s\"",
					e->le_file, (unsigned long) e->le_pos,
					e->le_database,
					mysql_errno(self->wr_conn), mysql_error(self->wr_conn), 
					query);
				return;
			}

			break;
		}

		case ET_QUERY: {
		char	*query;
			query = e->le_info;
			if (execute_query(self->wr_conn, query) != 0) {
				logmsg("%s,%lu: %s: query failed (%u: %s): \"%s\"",
					e->le_file, (unsigned long) e->le_pos,
					e->le_database,
					mysql_errno(self->wr_conn), mysql_error(self->wr_conn), 
					query);
				return;
			}
			executed_up_to(self, e->le_file, e->le_pos);

			break;
		}
		}
		free_log_entry(e);
		self->wr_status = ST_WAIT_FOR_ENTRY;
	}
	return;
}

/*ARGSUSED*/
static void
sync_ack(wr)
	writer_t *wr;
{
	(void) pthread_mutex_lock(&sync_mtx);
	--nsyncs;
	(void) pthread_cond_signal(&sync_cond);
	(void) pthread_mutex_unlock(&sync_mtx);
}

static int
execute_query(conn, q)
	MYSQL *conn;
	char const *q;
{
unsigned	i;

	if (debug) {
		(void) printf("would execute: [%s]\n", q);
		return 0;
	}

	if ((i = mysql_query(conn, q)) == 0)
		return 0;

	if (can_ignore_errno(mysql_errno(conn)))
		return 0;

	return i;
}

static void
set_thread_name(name)
	char const *name;
{
char	*s;
	if ((s = pthread_getspecific(threadname)) != NULL)
		free(s);
	(void) pthread_setspecific(threadname, strdup(name));
}

static char const *
get_thread_name()
{
char	*s;
	if ((s = pthread_getspecific(threadname)) == NULL)
		return "unknown";
	return s;
}

static void
executed_up_to(writer, log, pos)
	writer_t *writer;
	char const *log;
	logpos_t pos;
{
char	buf[2048];
int	fd;

	(void) pthread_mutex_lock(&wst_mtx);
	if (writer->wr_last_executed_file && !strcmp(log, writer->wr_last_executed_file)) {
		if (pos == writer->wr_last_executed_pos) {
			(void) pthread_mutex_unlock(&wst_mtx);
			return;
		}
	} else
		strdup_free(&writer->wr_last_executed_file, log);
	writer->wr_last_executed_pos = pos;
	(void) pthread_mutex_unlock(&wst_mtx);

	if (!writer || writer->wr_rstat == 0) {
		(void) snprintf(buf, sizeof(buf), "%s/logpos", statedir);
		if (unlink(buf) == -1 && errno != ENOENT) {
			logmsg("cannot remove old state file \"%s\": %s",
					buf, strerror(errno));
			exit(1);
		}

		if ((fd = open(buf, O_WRONLY | O_CREAT | O_EXCL, 0600)) == -1) {
			logmsg("cannot open state file \"%s\": %s",
					buf, strerror(errno));
			exit(1);
		}

		if (writer)
			writer->wr_rstat = fd;
	} else
		(void) lseek(writer->wr_rstat, 0, SEEK_SET);

	(void) ftruncate(fd, 0);
	int4store(buf, pos);
	(void) strlcpy(buf + 4, log, sizeof(buf) - 4);
	(void) write(fd, buf, 4 + strlen(log));
	if (!unsynced)
		(void) fdatasync(fd);
}

static int
retrieve_binlog_position(writer)
	writer_t *writer;
{
int		 rstat;
ssize_t		 n;
struct stat	 st;
char		*buf;
char		 sname[2048];
	(void) snprintf(sname, sizeof(sname), "%s/logpos", statedir);
	if ((rstat = open(sname, O_RDONLY)) == -1) {
		logmsg("cannot open state file \"%s\": %s",
				sname, strerror(errno));
		if (errno != ENOENT)
			return 1;

		/*
		 * Use the binlog position specified on command line.
		 */
		if (!binlog_file) {
			logmsg("no stored binlog position and none on command line; exiting");
			return 1;
		}

		writer->wr_last_executed_pos = binlog_pos;
		strdup_free(&writer->wr_last_executed_file, binlog_file);
		(void) pthread_mutex_lock(&rst_mtx);
		if (lowest_log_pos == 0 || writer->wr_last_executed_pos < lowest_log_pos)
			lowest_log_pos = writer->wr_last_executed_pos;
		(void) pthread_mutex_unlock(&rst_mtx);
		return 0;
	}

	if (fstat(rstat, &st) == -1) {
		logmsg("cannot stat state file \"%s\": %s",
				sname, strerror(errno));
		exit(1);
	}

	buf = calloc(1, st.st_size + 1);
	if ((n = read(rstat, buf, st.st_size)) != st.st_size) {
		logmsg("short read on state file \"%s\": %s",
				sname, strerror(errno));
		exit(1);
	}
	
	writer->wr_last_executed_pos = uint4korr(buf);
	strdup_free(&writer->wr_last_executed_file, buf + 4);

	(void) pthread_mutex_lock(&rst_mtx);
	if (lowest_log_pos == 0 || writer->wr_last_executed_pos < lowest_log_pos)
		lowest_log_pos = writer->wr_last_executed_pos;
	if (binlog_file == NULL)
		binlog_file = strdup(buf + 4);
	(void) pthread_mutex_unlock(&rst_mtx);

	return 0;
}

static int status_door;
static void service_status_request(void *, char *, size_t, door_desc_t *, uint_t);

static void
setup_status_door()
{
int	i;

	if (!ctldoor) {
		logmsg("warning: no control door specificied");
		return;
	}

	if ((status_door = door_create(service_status_request,
					NULL, 0)) == -1) {
		logmsg("creating status door: %s", strerror(errno));
		exit(1);
	}

	(void) unlink(ctldoor);
	if ((i = open(ctldoor, O_EXCL | O_CREAT | O_RDWR, 0600)) == -1) {
		logmsg("creating status door \"%s\": %s",
				ctldoor, strerror(errno));
		exit(1);
	}
	(void) close(i);

	(void) fdetach(ctldoor);
	if (fattach(status_door, ctldoor) == -1) {
		logmsg("attaching status door to \"%s\": %s",
				ctldoor, strerror(errno));
		exit(1);
	}
}

/*ARGSUSED*/
static void
service_status_request(cookie, args, arglen, desc, ndesc)
	void *cookie;
	char *args;
	size_t arglen;
	door_desc_t *desc;
	uint_t ndesc;
{
char	 c[3];
uchar_t	*st;
size_t	 blen = 0, offs = 0;
int	 i;
	if (arglen < 1) {
		c[0] = RR_INVALID_QUERY;
		door_return(c, 1, NULL, 0);
		return;
	}

	switch (args[0]) {
	case RQ_PING:
		c[0] = RR_OK;
		door_return(c, 1, NULL, 0);
		return;

	case RQ_STATUS:
		st = malloc(2 + 1);
		st[0] = RR_OK;
		(void) pthread_mutex_lock(&rst_mtx);
		st[1] = reader_st;
		(void) pthread_mutex_unlock(&rst_mtx);

		(void) pthread_mutex_lock(&wst_mtx);
		st[2] = writer.wr_status;
		(void) pthread_mutex_unlock(&wst_mtx);

		door_return((char *) st, 2 + 1, NULL, 0);
		return;

	case RQ_READER_POSITION:
		(void) pthread_mutex_lock(&rst_mtx);
		if (!binlog_file) {
			(void) pthread_mutex_unlock(&rst_mtx);
			door_return(NULL, 0, NULL, 0);
			return;
		}

		blen = strlen(binlog_file);
		st = malloc(5 + blen);
		st[0] = RR_OK;
		int4store(st + 1, binlog_pos);
		(void) memcpy(st + 5, binlog_file, blen);
		(void) pthread_mutex_unlock(&rst_mtx);

		door_return((char *) st, 5 + blen, NULL, 0);
		return;

	case RQ_WRITER_POSITION:
		(void) pthread_mutex_lock(&wst_mtx);
		offs = 2;
		st = alloca(1 + (10 + BINLOG_NAMELEN));
		st[0] = RR_OK;
		st[1] = 1;

		if (!writer.wr_last_executed_file) {
			int4store(st + offs, (uint32_t) 0);
			offs += 4;
		} else {
			blen = strlen(writer.wr_last_executed_file);
			int4store(st + offs, writer.wr_last_executed_pos);
			int4store(st + offs + 4, (uint32_t) writer.wr_last_executed_time);
			int2store(st + offs + 8, (uint16_t) blen);
			(void) memcpy(st + offs + 10, writer.wr_last_executed_file, blen);
			offs += 10 + blen;
		}

		(void) pthread_mutex_unlock(&wst_mtx);
		door_return((char *) st, offs, NULL, 0);
		return;

	case RQ_START:
		(void) port_send(ctl_port, CTL_START, NULL);
		c[0] = RR_OK;
		door_return(c, 1, NULL, 0);
		return;

	case RQ_STOP:
		(void) port_send(ctl_port, CTL_STOP, NULL);
		c[0] = RR_OK;
		door_return(c, 1, NULL, 0);
		return;

	case RQ_SHUTDOWN:
		(void) port_send(ctl_port, CTL_SHUTDOWN, NULL);
		c[0] = RR_OK;
		door_return(c, 1, NULL, 0);
		return;

	default:
		c[0] = RR_INVALID_QUERY;
		door_return(c, 1, NULL, 0);
		return;
	}
}

static void
writer_init(wr)
	writer_t *wr;
{
	(void) memset(wr, 0, sizeof(*wr));
	lq_init(&wr->wr_log_queue);
	wr->wr_status = ST_STOPPED;
}
