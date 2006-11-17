/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * cachedir_data_store: a dbwrap datastore for cache directories.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif
		
#include "cache.h"

/*
 * Create the cachedir list.
 */
cachedir_data_store::cachedir_data_store(void)
: _curdir(0)
{
	assert(config.cachedirs.size());
	vector<cachedir>::iterator it = config.cachedirs.begin(), end = config.cachedirs.end();
	for (; it != end; ++it)
		_cachedirs.push_back(new a_cachedir(config.cachedirs[0].dir, _cachedirs.size()));
}

/*
 * Open a file given the dir# and file#.
 */
cachefile *
cachedir_data_store::open(int dirn, uint64_t num)
{
	assert(dirn >= 0);
	if (dirn > (int)_cachedirs.size()) {
		wlog(WLOG_WARNING,
		     format("trying to open cachedir %d, but only %d configured")
			     % dirn % _cachedirs.size());
		wlog(WLOG_WARNING, 
		     "this probably means the cachedir configuration has been "
				     "changed but old cache-master data is being used");
		return NULL;
	}
	return _cachedirs[dirn]->open(num);
}

/*
 * Return a new, empty cached file, distributing load over all configured
 * cache dirs.
 */
cachefile *
cachedir_data_store::nextfile(void)
{
int	d;
	{	HOLDING(_lock);
		d = _curdir++;
		WDEBUG((WLOG_DEBUG, format("got cachedir %d of %d") % d % _cachedirs.size()));
		if (_curdir == _cachedirs.size())
			_curdir = 0;
	}
	return _cachedirs[d]->nextfile();
}

/*
 * dbwrap callback to populate an entity with its data after loading it from
 * the database.
 */
cachedentity *
cachedir_data_store::retrieve(pair<char const *, uint32_t> const &d)
{
db::marshaller<cachedentity>	m;
cachedentity	*ent;
	/*
	 * Read the cached data from the cachedir.
	 */
	ent = m.unmarshall(d);
	WDEBUG((WLOG_DEBUG, format("CACHE: unmarshalling a cached entity, dir=%d")
		% ent->cachedir()));
	if (ent == NULL)
		return NULL;
	WDEBUG((WLOG_DEBUG, format("CACHE: loading cache data for %s") % ent->url()));
	assert(ent->complete() && !ent->isvoid());
	ent->loadcachefile();
	return ent;
}

/*
 * dbwrap callback to write an entity's data to disk before saving it in the
 * database.
 */
pair<char const *, uint32_t>
cachedir_data_store::store(cachedentity &o)
{
db::marshaller<cachedentity>	m;
pair<char const *, uint32_t>	ret;
	assert(o.complete() && !o.isvoid());
	
	/*
	 * Write the cached data to the cachedir.
	 */
cachefile	*f = nextfile();
	o.savecachefile(f);
	WDEBUG((WLOG_DEBUG, format("CACHE: storing %s, dir=%d")
		% o.url() % o.cachedir()));
		ret = m.marshall(o);
	delete f;
	return ret;
}
