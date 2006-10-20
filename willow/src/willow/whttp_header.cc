/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp_header: header processing implementation.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include <vector>
using std::vector;
#include <cstring>
using std::strlen;
#include <cerrno>

#include <event.h>

#include "config.h"
#include "whttp_entity.h"
#include "whttp_header.h"

header::header(char *n, char *v)
	: hr_name(n)
	, hr_value(v)
{
}

header_list::header_list()
	: hl_len(0)
{
	hl_hdrs.reserve(20);
}

void
header_list::append(header const &h)
{
	hl_hdrs.push_back(h);
	hl_last = &*hl_hdrs.rbegin();
	hl_len += strlen(h.hr_name) + strlen(h.hr_value) + 4;
}

void
header_list::add(char *name, char *value)
{
	append(header(name, value));
}

void
header_list::append_last(const char *append)
{
char	*tmp;
	assert(hl_last);
	tmp = hl_last->hr_value;
	hl_last->hr_value = (char *)wmalloc(strlen(tmp) + strlen(append) + 2);
	strcat(hl_last->hr_value, tmp);
	strcat(hl_last->hr_value, " ");
	strcat(hl_last->hr_value, append);
}

void
header_list::remove(const char *name)
{
vector<header>::iterator	it, end;
	for (it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it) {
		if (strcasecmp(it->hr_name, name))
			continue;
		hl_hdrs.erase(it);
		return;
	}
	
}

struct header *
header_list::find(const char *name)
{
vector<header>::iterator	it, end;
	for (it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it) {
		if (strcasecmp(it->hr_name, name))
			continue;
		return &*it;
	}
	return NULL;
}
#if 0
tst<char, header *>::iterator it;
	if ((it = hl_hdrs.find(name)) == hl_hdrs.end())
		return NULL;
	return it->second;
}
#endif

char *
header_list::build(void)
{
char	*buf;
size_t	 bufsz;
size_t	 buflen = 0;

	bufsz = hl_len + 3;
	if ((buf = (char *)wmalloc(bufsz)) == NULL)
		outofmemory();
	
	*buf = '\0';
	for (header *h = hl_last; h; h = h->hr_next) {
		buflen += snprintf(buf + buflen, bufsz - buflen - 1, "%s: %s\r\n", 
				h->hr_name, h->hr_value);
	}
	if (strlcat(buf, "\r\n", bufsz) >= bufsz)
		abort();

	return buf;
}

void
header_list::dump(int fd)
{
tst<char, header *>::iterator vit, vend;
int i = 0;
	i = hl_hdrs.size();
	write(fd, &i, sizeof(i));	

	for (header *h = hl_last; h; h = h->hr_next) {
		int j, k;
		k = strlen(h->hr_name);
		write(fd, &k, sizeof(k));
		j = strlen(h->hr_value);
		write(fd, &j, sizeof(j));
		write(fd, h->hr_name, k);
		write(fd, h->hr_value, j);
	}
}

int
header_list::undump(int fd, off_t *len)
{
	int		 i = 0, j = 0, sz = 0;
	ssize_t		 r;
	
	*len = 0;
	if ((r = read(fd, &sz, sizeof(sz))) < 0) {
		wlog(WLOG_WARNING, "reading cache file: %s", strerror(errno));
		return -1; /* XXX */
	}
	
	*len += r;
	WDEBUG((WLOG_DEBUG, "header_undump: %d entries", sz));

	while (sz--) {
	char	*n, *v, *s;
	int	 k;
	header	*h;
		*len += read(fd, &i, sizeof(i));	
		*len += read(fd, &j, sizeof(j));
		WDEBUG((WLOG_DEBUG, "header_undump: i=%d j=%d", i, j));
		n = (char *)wmalloc(i + j + 2);
		i = read(fd, n, i);
		*len += i;
		s = n + i;
		*s++ = '\0';
		v = s;
		k = read(fd, s, j);
		*len += k;
		s += k;
		*s = '\0';
		append(header(n, wstrdup(v)));
	}
	
	return 0;
}
