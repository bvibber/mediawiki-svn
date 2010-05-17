#include "CacheEntry.h"
#include "Cache.h"

MaxCache::CacheEntry::CacheEntry( const Cache & parent, const std::string & key, StringPtr value, 
		boost::uint64_t cost, Time expiry )
	: mKey( key ), mValue( value ), mHitCount( 0 ), mOriginalCost( cost ), mExpiry( expiry ),
	mClock( parent.getClock() )
{
	adjustCost();
}

