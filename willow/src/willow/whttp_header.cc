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

header::header(char const *n, char const *v)
{
int	nlen = strlen(n);
	hr_name = new char[nlen + strlen(v) + 2];
	hr_value = hr_name + nlen + 1;
	memcpy(hr_name, n, nlen + 1);
	strcpy(hr_value, v);
}

header::~header() {
	delete[] hr_name;
}

header_list::header_list()
	: hl_len(0)
{
	hl_hdrs.reserve(20);	/* should be enough for most requests */
}

void
header_list::append(header *h)
{
	hl_hdrs.push_back(h);
	hl_len += strlen(h->hr_name) + strlen(h->hr_value) + 4;
}

header_list::~header_list()
{
vector<header *>::iterator it, end;
	for (it = hl_hdrs.begin(), end = hl_hdrs.end(); it != end; ++it)
		delete *it;
}

void
header_list::add(char const *name, char const *value)
{
	append(new header(name, value));
}

void
header_list::append_last(const char *append)
{
header	*last;
char	*tmp;
	last = *hl_hdrs.rbegin();
	tmp = last->hr_value;
	last->hr_value = (char *)wmalloc(strlen(tmp) + strlen(append) + 2);
	sprintf(last->hr_value, "%s %s", tmp, append);
	wfree(tmp);
}
	
void
header_list::remove(const char *it)
{
vector<header *>::iterator vit, vend;
	for (vit = hl_hdrs.begin(), vend = hl_hdrs.end(); vit != vend; ++vit)
		if (strcasecmp(it, (*vit)->hr_value))
			break;

	delete *vit;
	std::swap(*vit, *hl_hdrs.rbegin());
	hl_hdrs.pop_back();
}

struct header *
header_list::find(const char *name)
{
vector<header *>::iterator vit, vend;
	for (vit = hl_hdrs.begin(), vend = hl_hdrs.end(); vit != vend; ++vit)
		if (!strcasecmp(name, (*vit)->hr_name))
			return *vit;
	return NULL;
}

char *
header_list::build(void)
{
char	*buf;
size_t	 bufsz;
size_t	 buflen = 0;
vector<header *>::iterator vit, vend;

	bufsz = hl_len + 3;
	if ((buf = (char *)wmalloc(bufsz)) == NULL)
		outofmemory();
	
	*buf = '\0';
	for (vit = hl_hdrs.begin(), vend = hl_hdrs.end(); vit != vend; ++vit)
		buflen += snprintf(buf + buflen, bufsz - buflen - 1, "%s: %s\r\n", (*vit)->hr_name, (*vit)->hr_value);
	if (strlcat(buf, "\r\n", bufsz) >= bufsz)
		abort();

	return buf;
}

void
header_list::dump(int fd)
{
vector<header *>::iterator vit, vend;
int i = 0;
	i = hl_hdrs.size();
	write(fd, &i, sizeof(i));	

	for (vit = hl_hdrs.begin(), vend = hl_hdrs.end(); vit != vend; ++vit) {
		int j, k;
		k = strlen((*vit)->hr_name);
		write(fd, &k, sizeof(k));
		j = strlen((*vit)->hr_value);
		write(fd, &j, sizeof(j));
		write(fd, (*vit)->hr_name, k);
		write(fd, (*vit)->hr_value, j);
	}
}

int
header_list::undump(int fd, off_t *len)
{
	int		 i = 0, j = 0, sz = 0;
	ssize_t		 r;
	
	*len = 0;
	hl_hdrs.clear();
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
		append(new header(n, wstrdup(v)));
	}
	
	return 0;
}
