/* @(#) $Header$ */
/* From: $Nightmare: nightmare/include/config.h,v 1.32.2.2.2.2 2002/07/02 03:41:28 ejb Exp $ */
/* From: newconf.h,v 7.36 2005/03/21 22:42:10 leeh Exp */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * confparse: configuration parser.
 */

#ifndef CONFPARSE_H
#define CONFPARSE_H

#ifdef __SUNPRO_C
# pragma ident "@(#)$Header$"
#endif

#include "queue.h"

struct conf_entry {
const	char	*cf_name;
	int	 cf_type;
	void	(*cf_func) (void *);
	int	 cf_len;
	void	*cf_arg;
	LIST_ENTRY(conf_entry) entries;
};

struct top_conf {
	char	*tc_name;
	int	(*tc_sfunc) (struct top_conf *);
	int	(*tc_efunc) (struct top_conf *);
	LIST_HEAD(tc_items_head, conf_entry) tc_items;
	struct conf_entry *tc_entries;
	LIST_ENTRY(top_conf) entries;
};

#define CF_QSTRING	0x01
#define CF_INT		0x02
#define CF_STRING	0x03
#define CF_TIME		0x04
#define CF_YESNO	0x05
#define CF_LIST		0x06
#define CF_ONE		0x07

#define CF_MTYPE	0xFF

#define CF_FLIST	0x1000
#define CF_MFLAG	0xFF00

typedef struct conf_parm_t_stru
{
struct	conf_parm_t_stru	*next;
	int			 type;
	union {
		char		 *string;
		int		  number;
	struct	conf_parm_t_stru *list;
	} v;
} conf_parm_t;

extern struct top_conf *conf_cur_block;

extern const char *current_file;
extern int lineno;
extern int nerrors;

int read_config(char *);
int conf_start_block(const char *, const char *);
int conf_end_block(struct top_conf *);
int conf_call_set(struct top_conf *, char *, conf_parm_t *, int);
void conf_report_error(const char *, ...);
void newconf_init(void);
extern char *conf_cur_block_name;
int add_conf_item(const char *topconf, const char *name, int type, void (*func) (void *));
int remove_conf_item(const char *topconf, const char *name);


#endif
