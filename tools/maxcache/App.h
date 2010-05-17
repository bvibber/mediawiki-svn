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

namespace MaxCache {

namespace Asio = boost::asio;
typedef boost::asio::ip::tcp Tcp;
typedef boost::asio::io_service Service;
typedef Tcp::endpoint Endpoint;
typedef Tcp::acceptor Acceptor;
typedef boost::shared_ptr<Acceptor> AcceptorPtr;
typedef Tcp::socket Socket;
typedef boost::shared_ptr<Socket> SocketPtr;
typedef boost::system::error_code ErrorCode;
typedef boost::system::system_error SystemError;
typedef boost::asio::streambuf Buffer;
typedef boost::shared_ptr<Buffer> BufferPtr;
typedef boost::asio::buffers_iterator< Buffer::const_buffers_type > BufferIterator;

class App {
	public:
		App()
			: mMaxKeyLength( 1000 ), 
			mCache( *this, 100000000 )
		{}

		void run( int argc, char** argv );

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

		std::size_t getMaxCmdLength() const {
			return mCache.getMaxEntrySize() + mMaxKeyLength + sizeof( "blah\r\n" );
		}

		void startAccept();
		void onAccept( SocketPtr peer, const ErrorCode & acceptError );

		void startRead( SocketPtr peer );
		void onLineReady( SocketPtr peer, BufferPtr buffer, 
			const ErrorCode & readError, std::size_t n );

		void writeMessage( SocketPtr peer, const char * msg );
		void onWriteMessageDone( SocketPtr peer, const ErrorCode & writeMessage, std::size_t n );

		void writeCacheEntry( SocketPtr peer, StringPtr entry );
		void onWriteCacheEntryDone( SocketPtr peer, StringPtr entry,
			const ErrorCode & writeError, std::size_t n );

		void writeStats( SocketPtr peer );
		void onWriteStatsDone( SocketPtr peer, StringPtr buffer,
				const ErrorCode & writeError, std::size_t n );
	protected:
		bool handleTooFewParams( SocketPtr peer, std::istream & stream ) {
			if ( stream.eof() ) {
				writeMessage( peer, "ERROR: Not enough parameters\r\n" );
				return true;
			} else {
				return false;
			}
		}


		Service mService;
		AcceptorPtr mAcceptor;

		const std::size_t mMaxKeyLength;

		Cache mCache;
		Time mTime;

		const static Time NO_TIME;
};

}
#endif
