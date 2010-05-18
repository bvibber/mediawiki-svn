#include "Session.h"
#include "App.h"
#include <boost/bind.hpp>
#include <boost/foreach.hpp>

using namespace MaxCache;

Session::Session( App & app )
	: mApp( app ),
	mSocket( app.getService() ),
	mInputBuffer( app.getMaxCmdLength() )
{}

void Session::reset()
{
	if ( mSocket.is_open() ) {
		mSocket.shutdown( Socket::shutdown_both );
		mSocket.close();
	}
	if ( mInputBuffer.size() ) {
		mInputBuffer.consume( mInputBuffer.size() );
	}
	mWriteBuffer.clear();
}

void Session::startRead()
{
	Asio::async_read_until( mSocket, mInputBuffer, "\r\n",
		boost::bind( &Session::onLineReady, this, _1, _2 ) );
}

void Session::onLineReady( const ErrorCode & readError, std::size_t n )
{
	if ( readError ) {
		// Close connection
		mApp.freeSession( *this );
		return;
	}
	
	std::string line;
	line.reserve( n );
	BOOST_FOREACH( Asio::const_buffer block, mInputBuffer.data() ) {
		line.append( Asio::buffer_cast<const char*>( block ), Asio::buffer_size( block ) );
	}
	mInputBuffer.consume( n );
	std::stringstream stream( line );

	// Formatted extraction requires a sensible locale to be set
	stream.imbue( std::locale( "C" ) );

	std::string command;
	stream >> command;
	if ( handleTooFewParams( stream ) ) return;

	if ( command == "get" ) {
		std::string key;
		stream >> key;
		if ( handleTooFewParams( stream ) ) return;

		CacheEntry * entry = mApp.getCache().getEntry( key );
		if ( !entry ) {
			writeMessage( "NO: Item not found\r\n" );
			return;
		}

		writeCacheEntry( entry->getValue() );

	} else if ( command == "set" ) {
		// Usage: set <key> <expiry> <cost> <value>
		std::string key;
		boost::uint32_t expiryInterval, clientCost;
		StringPtr value( new std::string );

		stream.width( mApp.getMaxKeyLength() + 1 );
		stream >> key;
		if ( handleTooFewParams( stream ) ) return;
		if ( key.size() > mApp.getMaxKeyLength() ) {
			writeMessage( "ERROR: Key too long\r\n" );
			return;
		}

		stream >> expiryInterval;
		if ( handleTooFewParams( stream ) ) return;
		if ( stream.fail() ) {
			writeMessage( "ERROR: invalid expiry interval\r\n" );
			return;
		}

		// Read the client cost, which is later divided by the size to get the 
		// real cost
		stream >> clientCost;
		if ( handleTooFewParams( stream ) ) return;
		if ( stream.fail() ) {
			writeMessage( "ERROR: invalid clientCost\r\n" );
			return;
		}

		stream >> *value;
		if ( handleTooFewParams( stream ) ) return;
		if ( value->size() > mApp.getCache().getMaxEntrySize() ) {
			// This won't happen very often, usually the ASIO buffer limit will 
			// be exceeded instead.
			writeMessage( "ERROR: value too big\r\n" );
			return;
		}

		Time expiry;
		if ( expiryInterval ) {
			expiry = mApp.getTime() + boost::posix_time::microseconds( expiryInterval );
		} else {
			expiry = Time( App::INFIN_TIME );
		}

		mApp.getCache().setEntry( key, value, clientCost, expiry );
		writeMessage( "OK: value set\r\n" );
	} else if ( command == "delete" ) {
		std::string key;
		stream.width( mApp.getMaxKeyLength() + 1 );
		stream >> key;
		if ( handleTooFewParams( stream ) ) return;
		if ( key.size() > mApp.getMaxKeyLength() ) {
			writeMessage( "ERROR: Key too long\r\n" );
			return;
		}
		
		if ( mApp.getCache().deleteEntry( key ) ) {
			writeMessage( "OK: deleted\r\n" );
		} else {
			writeMessage( "NO: not found\r\n" );
		}
	} else if ( command == "quit" ) {
		mApp.freeSession( *this );
	} else if ( command == "stats" ) {
		writeStats();
	} else if ( command == "kill" ) {
		// TODO: for debugging only, remove this
		mApp.stop();
	} else {
		writeMessage( "ERROR: Unknown command\r\n" );
	}
}

void Session::writeMessage( const char * msg ) {
	Asio::async_write( 
		mSocket,
		Asio::const_buffers_1( msg, std::strlen( msg ) ),
		boost::bind( &Session::onWriteMessageDone, this, _1, _2 ) );
}

void Session::onWriteMessageDone( const ErrorCode & writeError, std::size_t n ) {
	if ( writeError ) {
		// Close connection
		mApp.freeSession( *this );
		return;
	}

	startRead();
}

void Session::writeCacheEntry( StringPtr entry ) {
	const char prefix[] = "VALUE: ";

	boost::array<Asio::const_buffer, 3> buffers;
	buffers[0] = Asio::const_buffer( prefix, sizeof( prefix ) );
	buffers[1] = Asio::const_buffer( entry->data(), entry->size() );
	buffers[2] = Asio::const_buffer( "\r\n", 2 );

	Asio::async_write( mSocket, buffers,
		boost::bind(
			&Session::onWriteCacheEntryDone, this, 
			entry, // just to keep a reference in memory so buffers[1] doesn't dangle
			_1, _2
		) 
	);
}

void Session::onWriteCacheEntryDone( StringPtr entry, const ErrorCode & writeError, 
		std::size_t n ) 
{
	if ( writeError ) {
		// Close connection
		mApp.freeSession( *this );
		return;
	}
	startRead();
	// entry will go out of scope here and may be deleted
}

void Session::writeStats() {
	std::stringstream s;
	s << "STATS:"
		<< " num-bytes=" << mApp.getCache().getNumBytes()
		<< " num-entries=" << mApp.getCache().getSize()
		<< " load-factor=" << mApp.getCache().getLoadFactor()
		<< " max-bytes=" << mApp.getCache().getMaxBytes()
		<< " max-load-factor=" << mApp.getCache().getMaxLoadFactor()
		<< "\r\n";
	mWriteBuffer = s.str();
	Asio::async_write(
		mSocket, 
		Asio::const_buffers_1( mWriteBuffer.data(), mWriteBuffer.size() ),
		boost::bind( &Session::onWriteStatsDone, this, _1, _2 ) 
	);
}

void Session::onWriteStatsDone( const ErrorCode & writeError, std::size_t n ) 
{
	if ( writeError ) {
		// Close connection
		mApp.freeSession( *this );
		return;
	}
	startRead();
	// buffer will go out of scope here and will be deleted
}

