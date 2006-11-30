/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* a_cachedir: class representing a single cache directory.		*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

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
