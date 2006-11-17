/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * a_cache: class representing a single cache directory.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#include "cache.h"

a_cachedir::a_cachedir(imstring const &path, int num)
	: _path(path)
	, _dnum(num)
{
	assert(_path.size());
}

cachefile *
a_cachedir::open(uint64_t num)
{
imstring	path = (format("%s/%d") % _path % num).str();
	return new cachefile(path, _dnum, num, false);
}

cachefile *
a_cachedir::nextfile(void)
{
int		n = _curfnum++;
imstring	path = (format("%s/%d") % _path % n).str();
	return new cachefile(path, _dnum, n, true);
}	
