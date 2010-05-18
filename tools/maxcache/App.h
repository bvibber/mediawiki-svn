#ifndef MAXCACHE_APP_H
#define MAXCACHE_APP_H

#include "boost-config.h"
#include <boost/asio.hpp>
#include <boost/shared_ptr.hpp>
#include <boost/system/system_error.hpp>
#include <list>
#include <string>
#include "typedefs.h"
#include "Cache.h"
#include "Session.h"

namespace MaxCache {

typedef Intrusive::list< Session, SessionListMemberOption > SessionList;

class App {
	public:
		App()
			: mMaxKeyLength( 1000 ), 
			mCache( *this, 100000000 )
		{}

		~App();

		void run( int argc, char** argv );

		Session & activateSession();
		void freeSession( Session & session );

		void stop() {
			mService.stop();
		}

		Service & getService() {
			return mService;
		}

		/**
		 * Get the time with a cache reset on every select()
		 */
		Time getTime() {
			if ( mTime == NO_TIME ) {
				mTime = boost::posix_time::microsec_clock::universal_time();
			}
			return mTime;
		}

		/**
		 * Reset the time cache
		 */
		void resetTimeCache() {
			mTime = NO_TIME;
		}

		std::size_t getMaxKeyLength() const { return mMaxKeyLength; }

		std::size_t getMaxCmdLength() const {
			return mCache.getMaxEntrySize() + mMaxKeyLength + sizeof( "blah\r\n" );
		}

		const Cache & getCache() const { return mCache; }
		Cache & getCache() { return mCache; }

		void startAccept();
		void onAccept( Session & session, const ErrorCode & acceptError );

		const static Time NO_TIME;
		const static Time INFIN_TIME;
	protected:

		Service mService;
		AcceptorPtr mAcceptor;
		SessionList mFreeSessions;
		SessionList mActiveSessions;

		const std::size_t mMaxKeyLength;

		Cache mCache;
		Time mTime;
};

}
#endif
