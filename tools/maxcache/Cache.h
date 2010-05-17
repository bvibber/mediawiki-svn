#ifndef MAXCACHE_CACHE_H
#define MAXCACHE_CACHE_H

#include "boost-config.h"
#include "CacheEntry.h"
#include <boost/shared_array.hpp>

namespace MaxCache {

typedef Intrusive::unordered_set<
	CacheEntry,
	KeyMemberOption,
	equal< CacheEntry::KeysEqual >,
	hash< CacheEntry::HashFunction >,
	power_2_buckets< true >
	> KeyTable;

typedef Intrusive::multiset<
	CacheEntry,
	CostMemberOption,
	compare< CacheEntry::CompareCosts > 
	> CostTree;

typedef Intrusive::multiset<
	CacheEntry,
	ExpiryMemberOption,
	compare< CacheEntry::CompareExpiries >
	> ExpiryTree;

class App;

class Cache {
	public:
		/**
		 * Get a reference to the clock value
		 */
		const boost::uint64_t & getClock() const { 
			return mClock; 
		}
		boost::uint64_t & getClock() { 
			return mClock; 
		}

		/**
		 * Constructor.
		 */
		Cache( App & app, std::size_t maxBytes );

		/**
		 * Destructor.
		 */
		~Cache();

		/**
		 * Create an entry and insert it
		 */
		void setEntry( const std::string & key, StringPtr value, 
			boost::uint32_t clientCost, Time expiry );

		/**
		 * Insert an entry and take ownership of the pointer.
		 * If necessary, evict entries from the cache to make room for the new one.
		 */
		void setEntryPointer( CacheEntry * entry );

		/**
		 * Get an entry from the cache and update frequency data.
		 * Returns NULL if the entry does not exist
		 */
		CacheEntry * getEntry( const std::string & key );

		/**
		 * Delete an entry. Returns true if it existed, false otherwise.
		 */
		bool deleteEntry( const std::string & key );

		float getMaxLoadFactor() const {
			return mMaxLoadFactor;
		}

		float getLoadFactor() const {
			return (float)getSize() / getNumBuckets();
		}

		std::size_t getNumBuckets() const { 
			return mNumBuckets; 
		}

		std::size_t getNumBytes() const {
			return mNumBytes;
		}

		std::size_t getMaxBytes() const {
			return mMaxBytes;
		}

		std::size_t getSize() const {
			return mKeyTable.size();
		}

		std::size_t getMaxEntrySize() const {
			return (size_t)1 << mMaxEntrySizeLog2;
		}
		
	protected:

		/**
		 * Erase an item which is in the container
		 */
		void erase( CacheEntry & entry );

		/**
		 * Evict entries until the size of the container is less than the maximum
		 */
		void evictUntilNormalSize();

		/**
		 * Evict any entries that have expired
		 */
		void evictExpiredEntries();

		float mMaxLoadFactor;
		std::size_t mNumBuckets;
		boost::shared_array<KeyTable::bucket_type> mBuckets;
		KeyTable mKeyTable;

		CostTree mCostTree;
		ExpiryTree mExpiryTree;
		boost::uint64_t mClock;

		std::size_t mNumBytes;
		std::size_t mMaxBytes;
		unsigned char mMaxEntrySizeLog2;

		App & mApp;
};

}

#endif
