#ifndef MAXCACHE_CACHEENTRY_H
#define MAXCACHE_CACHEENTRY_H

#include "boost-config.h"
#include <boost/cstdint.hpp>
#include <boost/shared_ptr.hpp>
#include <boost/intrusive/set.hpp>
#include <boost/intrusive/unordered_set.hpp>
#include <ctime>
#include <limits>
#include "typedefs.h"

using namespace boost::intrusive;

namespace MaxCache {

namespace Intrusive = boost::intrusive;

typedef Intrusive::unordered_set_member_hook< store_hash<true> > HashHook;
typedef Intrusive::set_base_hook<> TreeHook;

class Cache;

class CacheEntry {
	public:
		HashHook mKeyHook;
		TreeHook mCostHook;
		TreeHook mExpiryHook;

		struct KeysEqual {
			bool operator()( const CacheEntry & e1, const CacheEntry & e2 ) const {
				return e1.mKey == e2.mKey;
			}
		};

		struct KeyValueEqual {
			bool operator()( const std::string & key, const CacheEntry & entry ) const {
				return key == entry.mKey;
			}
		};

		struct HashFunction {
			boost::hash<std::string> mHash;

			std::size_t operator()( const CacheEntry & e ) const {
				return mHash( e.mKey );
			}
		};

		struct CompareCosts {
			bool operator()( const CacheEntry & e1, const CacheEntry & e2 ) const {
				if ( ( e1.mCost >= e1.mClock && e2.mCost >= e1.mClock ) 
					|| ( e1.mCost < e1.mClock && e2.mCost < e1.mClock ) )
				{
					return e1.mCost < e2.mCost;
				} else {
					return e2.mCost < e1.mClock && e1.mCost >= e1.mClock;
				}
			}
		};

		struct CompareExpiries {
			bool operator()( const CacheEntry & e1, const CacheEntry & e2 ) const {
				return e1.mExpiry < e2.mExpiry;
			}
		};

		CacheEntry( const Cache & parent, const std::string & key, StringPtr value, 
				boost::uint64_t cost, Time expiry );

		void incrementHitCount() {
			if ( mHitCount < std::numeric_limits<boost::uint32_t>::max() ) {
				mHitCount++;
			}
		}

		void adjustCost() {
			mCost = mClock + mHitCount * mOriginalCost;
		}

		boost::uint32_t getHitCount() const {
			return mHitCount;
		}

		boost::uint64_t getOriginalCost() const {
			return mOriginalCost;
		}

		boost::uint64_t getCost() const {
			return mCost;
		}

		Time getExpiry() const {
			return mExpiry;
		}

		const std::string & getKey() const {
			return mKey;
		}

		StringPtr getValue() {
			return mValue;
		}

		std::size_t getSize() {
			return sizeof(*this) + 
				sizeof(std::string) + // dynamically allocated value structure
				mKey.size() + // key storage
				mValue->size(); // value storage
		}

	protected:
		std::string mKey;
		StringPtr mValue;

		boost::uint32_t mHitCount;
		boost::uint64_t mOriginalCost;
		boost::uint64_t mCost;
		Time mExpiry;
		const boost::uint64_t & mClock;
};

typedef Intrusive::member_hook< CacheEntry, HashHook, &CacheEntry::mKeyHook > KeyMemberOption;
typedef Intrusive::member_hook< CacheEntry, TreeHook, &CacheEntry::mCostHook > CostMemberOption;
typedef Intrusive::member_hook< CacheEntry, TreeHook, &CacheEntry::mExpiryHook > ExpiryMemberOption;

}

#endif
