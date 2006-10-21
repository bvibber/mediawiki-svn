/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp_header: header processing implementation.
 */

#ifndef WHTTP_HEADER
#define WHTTP_HEADER

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include "config.h"
#include "willow.h"

struct header : freelist_allocator<header> {
	header(char *n, char *v);
	~header() {}

	char	*hr_name;
	char	*hr_value;
	header	*hr_next;
};

struct header_list {
	header_list();
	~header_list() {};

	void	 add		(char *, char *);
	void	 add		(char *, size_t, char *, size_t);
	void	 append_last	(const char *);
	char	*build		(void);
	void	 remove		(const char *);
	void	 dump		(int);
	int	 undump		(int, off_t *);
struct header	*find		(const char *name);

	header			*hl_last;
	vector<header>		 hl_hdrs;
	int			 hl_len;
};

struct qvalue {
	float	 val;
const	char	*name;

	bool operator< (qvalue const &rhs) const {
		return val < rhs.val;
	}
};

#endif
