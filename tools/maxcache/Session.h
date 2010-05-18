#ifndef MAXCACHE_SESSION_H
#define MAXCACHE_SESSION_H

#include "boost-config.h"
#include <boost/intrusive/list.hpp>
#include "typedefs.h"

using namespace boost::intrusive;

namespace MaxCache {

typedef Intrusive::list_member_hook<> ListHook;

class App;

class Session {
	public:
		Session( App & app );
		void reset();

		Socket & getSocket() {
			return mSocket;
		}

		void startRead();
		void onLineReady( const ErrorCode & readError, std::size_t n );

		void writeMessage( const char * msg );
		void onWriteMessageDone( const ErrorCode & writeError, std::size_t n );

		void writeCacheEntry( StringPtr entry );
		void onWriteCacheEntryDone( StringPtr entry, const ErrorCode & writeError, std::size_t n );

		void writeStats();
		void onWriteStatsDone( const ErrorCode & writeError, std::size_t n );

		ListHook mListHook;

	protected:
		bool handleTooFewParams( std::istream & stream ) {
			if ( stream.eof() ) {
				writeMessage( "ERROR: Not enough parameters\r\n" );
				return true;
			} else {
				return false;
			}
		}

		App & mApp;
		Socket mSocket;
		Buffer mInputBuffer;
		std::string mWriteBuffer;
};

typedef Intrusive::member_hook< Session, ListHook, &Session::mListHook > SessionListMemberOption;

}
#endif
