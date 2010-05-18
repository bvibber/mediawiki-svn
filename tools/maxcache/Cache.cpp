#include "Cache.h"
#include "App.h"

using namespace MaxCache;

Cache::Cache( App & app, std::size_t maxBytes )
	: mMaxLoadFactor( 0.75 ),
	mNumBuckets( 65536 ), // must be a power of 2
	mBuckets( new KeyTable::bucket_type[mNumBuckets] ),
	mKeyTable( KeyTable::bucket_traits( mBuckets.get(), mNumBuckets ) ),
	mClock( 0 ),
	mEntryPool( 1024 ),
	mNumBytes( mNumBuckets * sizeof( KeyTable::bucket_type ) ), 
	mMaxBytes( maxBytes ),
	mMaxEntrySizeLog2( 28 ),
	mApp( app )
{}

Cache::~Cache() {
	CostTree::iterator i;
	for ( i = mCostTree.begin(); i != mCostTree.end(); i = mCostTree.begin() ) {
		erase( *i );
	}
}

/**
 * Create a new CacheEntry from the specified params and insert it into 
 * the container.
 */
void Cache::setEntry( const std::string & key, StringPtr value, 
	boost::uint32_t clientCost, Time expiry )
{
	boost::uint64_t cost = ( (boost::uint64_t)clientCost << mMaxEntrySizeLog2 )
		/ value->size();
	CacheEntry * entry = newEntry( key, value, cost, expiry );
	setEntryPointer( entry );
}

/**
 * Insert an entry and take ownership of the pointer.
 * If necessary, evict entries from the cache to make room for the new one.
 */
void Cache::setEntryPointer( CacheEntry * entry ) {
	std::size_t entrySize = entry->getSize();
	if ( entrySize > mMaxBytes ) {
		// Too big, ignore this request.
		// This is equivalent to immediate eviction, so it doesn't break
		// the semantics of the insert operation.
		freeEntry( entry );
		return;
	}

	// Try to insert the item into the hashtable
	mNumBytes += entrySize;
	std::pair< KeyTable::iterator, bool > status = mKeyTable.insert( *entry );

	// If it failed, delete the offending entry
	if ( !status.second ) {
		erase( *status.first );
	} else {
		// Hashtable grew, does it need more buckets?
		if ( getLoadFactor() > mMaxLoadFactor ) {
			// Double the bucket count
			mNumBytes += sizeof( KeyTable::bucket_type ) * mNumBuckets;
			mNumBuckets <<= 1;
			boost::shared_array<KeyTable::bucket_type> newBuckets(
				new KeyTable::bucket_type[mNumBuckets] );
			mKeyTable.rehash( KeyTable::bucket_traits( newBuckets.get(), mNumBuckets ) );
			mBuckets.swap( newBuckets );
		}
	}

	// Evict entries until there is enough room
	if ( mNumBytes > mMaxBytes ) {
		evictUntilNormalSize();
		entry->adjustCost();
	}

	// Insert the item again if it failed before
	if ( !status.second ) {
		status = mKeyTable.insert( *entry );
		if ( !status.second ) {
			throw std::runtime_error( "Cache insertion failed unexpectedly" );
		}
	}

	// Insert it into the other indexes
	mCostTree.insert( *entry );
	mExpiryTree.insert( *entry );
}

void Cache::erase( CacheEntry & entry ) {
	mNumBytes -= entry.getSize();
	mKeyTable.erase( mKeyTable.iterator_to( entry ) );
	mCostTree.erase( mCostTree.iterator_to( entry ) );
	mExpiryTree.erase( mExpiryTree.iterator_to( entry ) );
	freeEntry( &entry );
}

void Cache::evictUntilNormalSize() {
	evictExpiredEntries();

	while ( mNumBytes > mMaxBytes ) {
		CostTree::iterator begin = mCostTree.begin();
		mClock = begin->getCost();
		erase( *begin );
	}
}

void Cache::evictExpiredEntries() {
	Time now = mApp.getTime();
	for ( ExpiryTree::iterator iter = mExpiryTree.begin(); 
		iter != mExpiryTree.end() && iter->getExpiry() < now ; 
		iter++ ) 
	{
		erase( *iter );
	}
}

CacheEntry * Cache::getEntry( const std::string & key ) {
	// Find the entry
	KeyTable::iterator iter = mKeyTable.find(
		key, boost::hash<std::string>(), CacheEntry::KeyValueEqual() );

	if ( iter == mKeyTable.end() ) {
		return NULL;
	}

	// Skip expiry check for keys that have no expiry
	if ( !iter->getExpiry().is_pos_infinity() ) {
		Time now = mApp.getTime();
		if ( iter->getExpiry() < mApp.getTime() ) {
			// Key is expired, delete it and return miss
			erase( *iter );
			return NULL;
		}
	}

	// Pull the entry out of the cost tree and recalculate its cost
	mCostTree.erase( mCostTree.iterator_to( *iter ) );
	iter->incrementHitCount();
	iter->adjustCost();

	// Reinsert the item into the cost tree
	mCostTree.insert( *iter );

	return &*iter;
}

bool Cache::deleteEntry( const std::string & key ) {
	// Find the entry
	KeyTable::iterator iter = mKeyTable.find(
		key, boost::hash<std::string>(), CacheEntry::KeyValueEqual() );
	if ( iter == mKeyTable.end() ) {
		return false;
	} else {
		erase( *iter );
		return true;
	}
}
