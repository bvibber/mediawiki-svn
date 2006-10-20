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
	header(char const *n, char const *v);
	~header();

	char		*hr_name;
	char		*hr_value;
};

struct header_list {
	header_list();
	~header_list();

	void	 append		(header *h);
	void	 add		(char const *, char const *);
	void	 append_last	(const char *);
	char	*build		(void);
	void	 remove		(const char *);
	void	 dump		(int);
	int	 undump		(int, off_t *);
struct header	*find		(const char *name);

	vector<header *> hl_hdrs;
	int		 hl_len;
};

struct qvalue {
	float	 val;
const	char	*name;

	bool operator< (qvalue const &rhs) const {
		return val < rhs.val;
	}
};

#endif
