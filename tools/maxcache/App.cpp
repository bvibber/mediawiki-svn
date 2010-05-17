#include "App.h"

#include <boost/bind.hpp>
#include <stdexcept>
#include <iostream>
#include <iterator>
#include <cstring>
#include <boost/array.hpp>

using namespace MaxCache;

const Time App::NO_TIME( boost::posix_time::not_a_date_time );

void App::run( int argc, char** argv ) {
	Endpoint endpoint( Tcp::v4(), 9999 );
	mAcceptor = AcceptorPtr( new Acceptor( mService, endpoint ) );
	startAccept();
	std::size_t numHandlers;
	do {
		resetTimeCache();
		numHandlers = mService.run_one();
	} while ( numHandlers );
}

void App::startAccept() {
	// Create the peer socket
	SocketPtr peer( new Socket( mService ) );
	// Start accepting connections
	mAcceptor->async_accept( *peer, boost::bind( &App::onAccept, this, peer, _1 ) );
}

void App::onAccept( SocketPtr peer, const ErrorCode & acceptError ) {
	if ( acceptError ) {
		throw SystemError( acceptError );
	}
	// Handle this connection
	startRead( peer );
	// Listen for another connection
	startAccept();
}

void App::startRead( SocketPtr peer ) {
	BufferPtr buffer( new Buffer( getMaxCmdLength() ) );
	Asio::async_read_until( *peer, *buffer, "\r\n",
		boost::bind( &App::onLineReady, this, peer, buffer, _1, _2 ) );
}

void App::onLineReady( SocketPtr peer, BufferPtr buffer,
	const ErrorCode & readError, std::size_t n )
{
	if ( readError ) {
		// Close connection
		return;
	}

	std::istream stream( buffer.get() );
	std::string command;

	// Formatted extraction requires a sensible locale to be set
	stream.imbue( std::locale( "C" ) );

	stream >> command;
	if ( handleTooFewParams( peer, stream ) ) return;

	if ( command == "get" ) {
		std::string key;
		stream >> key;
		if ( handleTooFewParams( peer, stream ) ) return;

		CacheEntry * entry = mCache.getEntry( key );
		if ( !entry ) {
			writeMessage( peer, "NO: Item not found\r\n" );
			return;
		}

		writeCacheEntry( peer, entry->getValue() );

	} else if ( command == "set" ) {
		// Usage: set <key> <expiry> <cost> <value>
		std::string key;
		boost::uint32_t expiryInterval, clientCost;
		StringPtr value( new std::string );

		stream.width( mMaxKeyLength + 1 );
		stream >> key;
		if ( handleTooFewParams( peer, stream ) ) return;
		if ( key.size() > mMaxKeyLength ) {
			writeMessage( peer, "ERROR: Key too long\r\n" );
			return;
		}

		stream >> expiryInterval;
		if ( handleTooFewParams( peer, stream ) ) return;
		if ( stream.fail() ) {
			writeMessage( peer, "ERROR: invalid expiry interval\r\n" );
			return;
		}

		// Read the client cost, which is later divided by the size to get the 
		// real cost
		stream >> clientCost;
		if ( handleTooFewParams( peer, stream ) ) return;
		if ( stream.fail() ) {
			writeMessage( peer, "ERROR: invalid clientCost\r\n" );
			return;
		}

		stream >> *value;
		if ( handleTooFewParams( peer, stream ) ) return;
		if ( value->size() > mCache.getMaxEntrySize() ) {
			writeMessage( peer, "ERROR: value too big\r\n" );
			return;
		}

		Time expiry;
		if ( expiryInterval ) {
			expiry = getTime() + boost::posix_time::microseconds( expiryInterval );
		} else {
			expiry = Time( boost::posix_time::pos_infin );
		}

		mCache.setEntry( key, value, clientCost, expiry );
		writeMessage( peer, "OK: value set\r\n" );
	} else if ( command == "delete" ) {
		std::string key;
		stream.width( mMaxKeyLength + 1 );
		stream >> key;
		if ( handleTooFewParams( peer, stream ) ) return;
		if ( key.size() > mMaxKeyLength ) {
			writeMessage( peer, "ERROR: Key too long\r\n" );
			return;
		}
		
		if ( mCache.deleteEntry( key ) ) {
			writeMessage( peer, "OK: deleted\r\n" );
		} else {
			writeMessage( peer, "NO: not found\r\n" );
		}
	} else if ( command == "quit" ) {
		// Let peer go out of scope and die
	} else if ( command == "stats" ) {
		writeStats( peer );
	} else if ( command == "kill" ) {
		// TODO: for debugging only, remove this
		mService.stop();
	} else {
		writeMessage( peer, "ERROR: Unknown command\r\n" );
	}
}

void App::writeMessage( SocketPtr peer, const char * msg ) {
	Asio::async_write( 
		*peer,
		Asio::const_buffers_1( msg, std::strlen( msg ) ),
		boost::bind( &App::onWriteMessageDone, this, peer, _1, _2 ) );
}

void App::onWriteMessageDone( SocketPtr peer, const ErrorCode & writeError, std::size_t n ) {
	if ( writeError ) {
		// Close connection
		return;
	}

	startRead( peer );
}

void App::writeCacheEntry( SocketPtr peer, StringPtr entry ) {
	const char prefix[] = "VALUE: ";
	boost::array<Asio::const_buffer, 3> buffers;
	buffers[0] = Asio::const_buffer( prefix, sizeof( prefix ) );
	buffers[1] = Asio::const_buffer( entry->data(), entry->size() );
	buffers[2] = Asio::const_buffer( "\r\n", 2 );

	Asio::async_write( *peer, buffers,
		boost::bind( 
			&App::onWriteCacheEntryDone, this, peer, 
			entry, // just to keep a reference in memory so buffers[1] doesn't dangle
			_1, _2
		) 
	);
}

void App::onWriteCacheEntryDone( SocketPtr peer, StringPtr entry, 
	const ErrorCode & writeError, std::size_t n ) 
{
	if ( writeError ) {
		// Close connection
		return;
	}
	startRead( peer );
	// entry will go out of scope here and may be deleted
}

void App::writeStats( SocketPtr peer ) {
	std::stringstream s;
	s << "STATS:"
		<< " num-bytes=" << mCache.getNumBytes()
		<< " num-entries=" << mCache.getSize()
		<< " load-factor=" << mCache.getLoadFactor()
		<< " max-bytes=" << mCache.getMaxBytes()
		<< " max-load-factor=" << mCache.getMaxLoadFactor()
		<< "\r\n";
	StringPtr sp( new std::string( s.str() ) );
	Asio::async_write(
		*peer, 
		Asio::const_buffers_1( sp->data(), sp->size() ),
		boost::bind( 
			&App::onWriteStatsDone, this, peer,
			sp, // just to keep a reference to sp
			_1, _2
		)
	);
}

void App::onWriteStatsDone( SocketPtr peer, StringPtr buffer,
	const ErrorCode & writeError, std::size_t n ) 
{
	if ( writeError ) {
		// Close connection
		return;
	}
	startRead( peer );
	// buffer will go out of scope here and will be deleted
}

