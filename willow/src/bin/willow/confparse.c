/* @(#) $Header$ */
/* From ircd-ratbox: newconf.c,v 7.209 2005/04/05 01:22:57 leeh Exp */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * confparse: config parser implementation.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Header$"
#endif

#include <stdarg.h>
#include <stdio.h>
#include <string.h>
#include <syslog.h>

#include "willow.h"
#include "confparse.h"
#include "queue.h"
#include "wlog.h"
#include "wbackend.h"
#include "wconfig.h"

#define CF_TYPE(x) ((x) & CF_MTYPE)

int nerrors;
struct top_conf *conf_cur_block;
char *conf_cur_block_name;

static LIST_HEAD(conf_items_head, top_conf) conf_items =
		LIST_HEAD_INITIALIZER(conf_items);

static struct conf_entry *find_conf_item(struct top_conf *top, const char *);
static void conf_set_generic_int(void *, void *);
static void conf_set_generic_string(void *, int, void *);
static void add_top_conf(const char *name, int (*) (struct top_conf *),
	int (*) (struct top_conf *), struct conf_entry *);

void
conf_report_error(const char *fmt, ...)
{
	char	buf[1024];
	va_list	ap;
	
	va_start(ap, fmt);
	(void)vsnprintf(buf, 1024, fmt, ap);
	va_end(ap);
	
	wlog(WLOG_ERROR, "\"%s\", line %d: %s", current_file, lineno, buf);
}

void
yyerror(msg)
	const char *msg;
{
	conf_report_error("%s", msg);
}

static const char *
conf_strtype(type)
	int type;
{
	switch (type & CF_MTYPE)
	{
	case CF_INT:
		return "integer value";
	case CF_STRING:
		return "unquoted string";
	case CF_YESNO:
		return "yes/no value";
	case CF_QSTRING:
		return "quoted string";
	case CF_TIME:
		return "time/size value";
	default:
		return "unknown type";
	}
}

static void
add_top_conf(name, sfunc, efunc, items)
	const char *name;
	int (*sfunc) (struct top_conf *);
	int (*efunc) (struct top_conf *);
	struct conf_entry *items;
{
struct	top_conf *tc;

	tc = wmalloc(sizeof(struct top_conf));

	tc->tc_name = wstrdup(name);
	tc->tc_sfunc = sfunc;
	tc->tc_efunc = efunc;
	tc->tc_entries = items;

	LIST_INSERT_HEAD(&conf_items, tc, entries);
}

static struct top_conf *
find_top_conf(name)
	const char *name;
{
struct	top_conf	*tc;

	LIST_FOREACH(tc, &conf_items, entries) {
		if (!strcasecmp(tc->tc_name, name))
			return tc;
	}
			
	return NULL;
}

static struct conf_entry *
find_conf_item(top, name)
	struct top_conf *top;
	const char *name;
{
struct	conf_entry *cf;

	if (top->tc_entries) {
		int i;

		for(i = 0; top->tc_entries[i].cf_type; i++)
		{
			cf = &top->tc_entries[i];

			if(!strcasecmp(cf->cf_name, name))
				return cf;
		}
	}

	LIST_FOREACH(cf, &top->tc_items, entries) {
		if(strcasecmp(cf->cf_name, name) == 0)
			return cf;
	}

	return NULL;
}

/*ARGSUSED*/
int
conf_call_set(tc, item, value, type)
	struct top_conf *tc;
	char *item;
	conf_parm_t *value;
	int type;
{
struct	conf_entry	*cf;
	conf_parm_t	*cp;

	if (!tc)
		return -1;

	if ((cf = find_conf_item(tc, item)) == NULL) {
		conf_report_error("Non-existant configuration setting %s::%s.", 
				tc->tc_name, (char *) item);
		nerrors++;
		return -1;
	}

	/*
	 * If it takes one thing, make sure they only passed one thing,
	 * and handle as needed.
	 */
	if (value->type & CF_FLIST && !cf->cf_type & CF_FLIST) {
		conf_report_error("Option %s::%s does not take a list of values.", 
				tc->tc_name, item);
		nerrors++;
		return -1;
	}

	cp = value->v.list;

	if (CF_TYPE(value->v.list->type) != CF_TYPE(cf->cf_type)) {
                /* 
		 * If it expects a string value, but we got a yesno, 
                 * convert it back
                 */
		if((CF_TYPE(value->v.list->type) == CF_YESNO) &&
		   (CF_TYPE(cf->cf_type) == CF_STRING)) {
			value->v.list->type = CF_STRING;

			if(cp->v.number == 1)
				cp->v.string = wstrdup("yes");
                        else
				cp->v.string = wstrdup("no");
		}

                /*
		 * Maybe it's a CF_TIME and they passed CF_INT --
                 * should still be valid.
		 */
		else if(!((CF_TYPE(value->v.list->type) == CF_INT) &&
			  (CF_TYPE(cf->cf_type) == CF_TIME))) {
			conf_report_error("Wrong type for %s::%s (expected %s, got %s)",
				tc->tc_name, (char *) item,
				conf_strtype(cf->cf_type), conf_strtype(value->v.list->type));
			nerrors++;
			return -1;
		}
	}

	if (cf->cf_type & CF_FLIST) {
		/* just pass it the extended argument list */
		cf->cf_func(value->v.list);
        } else {
		/* it's old-style, needs only one arg */
		switch (cf->cf_type) {
		case CF_INT:
		case CF_TIME:
		case CF_YESNO:
			if(cf->cf_arg)
				conf_set_generic_int(&cp->v.number, cf->cf_arg);
			else
				cf->cf_func(&cp->v.number);
			break;
		case CF_STRING:
		case CF_QSTRING:
			if(!*cp->v.string)
				conf_report_error("Ignoring %s::%s -- empty field",
					tc->tc_name, item);
			else if(cf->cf_arg)
				conf_set_generic_string(cp->v.string, cf->cf_len, cf->cf_arg);
			else
				cf->cf_func(cp->v.string);
			break;
		}
	}

	return 0;
}

int
conf_start_block(block, name)
	const char *block, *name;
{
	if ((conf_cur_block = find_top_conf(block)) == NULL) {
		conf_report_error("Configuration block '%s' is not defined.", block);
		nerrors++;
		return -1;
	}

	if (name)
		conf_cur_block_name = wstrdup(name);
	else
		conf_cur_block_name = NULL;

	if (conf_cur_block->tc_sfunc)
		if (conf_cur_block->tc_sfunc(conf_cur_block) < 0)
			return -1;

	return 0;
}

int
conf_end_block(tc)
	struct top_conf *tc;
{
	if(tc->tc_efunc)
		return tc->tc_efunc(tc);

	wfree(conf_cur_block_name);
	return 0;
}


static void
conf_set_generic_int(data, location)
	void *data, *location;
{
	*((int *) location) = *((unsigned int *) data);
}

static void
conf_set_generic_string(data, len, location)
	void *data, *location;
	int len;
{
	char **loc   = location;
        char  *input = data;

	if(len && strlen(input) > len)
		input[len] = '\0';

	wfree(*loc);
	*loc = wstrdup(input);
}

static int backend_port;

/*ARGSUSED*/
static int
conf_begin_backend(tc)
	struct top_conf *tc;
{
	if (conf_cur_block_name == NULL) {
		conf_report_error("backend name not specified");
		nerrors++;
		return -1;
	}
	
	return 0;
}

/*ARGSUSED*/
static int
conf_end_backend(tc)
	struct top_conf *tc;
{
	add_backend(conf_cur_block_name, backend_port);
	backend_port = 0;
	
	return 0;
}

static int listen_port;

/*ARGSUSED*/
static int
conf_begin_listen(tc)
	struct top_conf *tc;
{
	if (conf_cur_block_name == NULL) {
		conf_report_error("listener host not specified");
		nerrors++;
		return -1;
	}
	
	return 0;
}

/*ARGSUSED*/
static int
conf_end_listen(tc)
	struct top_conf *tc;
{
	add_listener(conf_cur_block_name, listen_port);
	listen_port = 0;
	
	return 0;
}

static int cachedir_size;

/*ARGSUSED*/
static int
conf_begin_cachedir(tc)
	struct top_conf *tc;
{
	if (conf_cur_block_name == NULL) {
		conf_report_error("cache directory not specified");
		nerrors++;
		return -1;
	}
	
	return 0;
}

/*ARGSUSED*/
static int
conf_end_cachedir(tc)
	struct top_conf *tc;
{
	add_cachedir(conf_cur_block_name, cachedir_size);
	cachedir_size = 0;
	
	return 0;
}

static char *log_file, *log_syslog_facility, *log_access_log;
static int log_level, log_syslog;

static struct syslog_facility {
	char	 *name;
	int	  fac;
} syslog_facilities[] = {
	{"user", LOG_USER},
	{"mail", LOG_MAIL},
        {"daemon", LOG_DAEMON},
	{"auth", LOG_AUTH},
	{"lpr", LOG_LPR},
	{"news", LOG_NEWS},
	{"uucp", LOG_UUCP},
	{"cron", LOG_CRON},
#ifdef LOG_AUDIT
	{"audit", LOG_AUDIT},
#endif
	{"local0", LOG_LOCAL0},	
	{"local1", LOG_LOCAL0},	
	{"local2", LOG_LOCAL0},	
	{"local3", LOG_LOCAL0},	
	{"local4", LOG_LOCAL0},	
	{"local5", LOG_LOCAL0},	
	{"local6", LOG_LOCAL0},	
	{"local7", LOG_LOCAL0},	
	{NULL, 0},
};

/*ARGSUSED*/
static int
conf_end_log(tc)
	struct top_conf *tc;
{
	if (log_file) {
		logging.file = wstrdup(log_file);
	}
	
	if (log_syslog) {
		struct syslog_facility *fac = syslog_facilities;

		logging.syslog = 1;

		if (log_syslog_facility) {
			for (; fac->name; fac++) {
				if (!strcmp(fac->name, log_syslog_facility)) {
					logging.facility = fac->fac;
					break;
				}
			}
			if (!fac->name) {
				conf_report_error("unrecognised syslog facility \"%s\"", log_syslog_facility);
				nerrors++;
			}
		} else
			logging.facility = LOG_DAEMON;
	}
	
	logging.level = log_level;
	if (log_access_log)
		config.access_log = wstrdup(log_access_log);
	return 0;
}

static int cache_expire_threshold = 25;
static int cache_expire_every = 60;
static int cache_compress, cache_complevel = 6, cache_private;
static int backend_retry;
char *cache_user, *cache_group;

static int
conf_end_cache(tc)
	struct top_conf *tc;
{
	if (cache_expire_threshold < 0 || cache_expire_threshold > 100) {
		conf_report_error("cache::expire_threshold must be between 0 and 100");
		nerrors++;
	}
	if (cache_expire_every < 0) {
		conf_report_error("cache::expire_every must be greater than 0");
		nerrors++;
	}

	config.cache_expevery = cache_expire_every;
	config.cache_expthresh = cache_expire_threshold;
	config.suid = cache_user;
	config.sgid = cache_group;
	config.compress = cache_compress;
	if (cache_complevel < 1 || cache_complevel > 9) {
		conf_report_error("cache::compress_level must be between 1 and 9");
		nerrors++;
	}
	config.complevel = cache_complevel;
	config.backend_retry = backend_retry;
	config.cache_private = cache_private;
	return 0;
}

static struct conf_entry conf_backend_table[] = {
	{ "port", 	CF_INT, NULL, 0, &backend_port	},
	{ NULL }
};

static struct conf_entry conf_listen_table[] = {
	{ "port",	CF_INT, NULL, 0, &listen_port },
	{ NULL }
};

static struct conf_entry conf_cachedir_table[] = {
	{ "size",	CF_TIME, NULL, 0, &cachedir_size },
	{ NULL }
};

static struct conf_entry conf_log_table[] = {
	{ "level",	CF_INT,		NULL, 0, &log_level	},
	{ "file",	CF_QSTRING,	NULL, 0, &log_file	},
	{ "syslog",	CF_YESNO,	NULL, 0, &log_syslog	},
	{ "facility",	CF_STRING,	NULL, 0, &log_syslog_facility	},
	{ "access-log",	CF_QSTRING,	NULL, 0, &log_access_log	},
	{ NULL }
};

static struct conf_entry conf_cache_table[] = {
	{ "expire_every",	CF_TIME,	NULL, 0, &cache_expire_every		},
	{ "expire_threshold",	CF_INT,		NULL, 0, &cache_expire_threshold	},
	{ "user",		CF_STRING,	NULL, 0, &cache_user			},
	{ "group",		CF_STRING,	NULL, 0, &cache_group			},
	{ "compress",		CF_YESNO,	NULL, 0, &cache_compress		},
	{ "compress_level",	CF_INT,		NULL, 0, &cache_complevel		},
	{ "backend_retry",	CF_TIME,	NULL, 0, &backend_retry			},
	{ "cache_private",	CF_YESNO,	NULL, 0, &cache_private			},
	{ NULL }
};

void
newconf_init(void)
{
	add_top_conf("backend", conf_begin_backend, conf_end_backend, conf_backend_table);
	add_top_conf("listen", conf_begin_listen, conf_end_listen, conf_listen_table);
	add_top_conf("cache-dir", conf_begin_cachedir, conf_end_cachedir, conf_cachedir_table);
	add_top_conf("log", NULL, conf_end_log, conf_log_table);
	add_top_conf("cache", NULL, conf_end_cache, conf_cache_table);
}
