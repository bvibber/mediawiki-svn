#include "App.h"

#include <boost/bind.hpp>
#include <stdexcept>
#include <iostream>
#include <iterator>
#include <cstring>
#include <boost/array.hpp>

using namespace MaxCache;

const Time App::NO_TIME( boost::posix_time::not_a_date_time );
const Time App::INFIN_TIME( boost::posix_time::pos_infin );

App::~App() {
	SessionList::iterator i;
	for ( i = mActiveSessions.begin(); i != mActiveSessions.end(); i = mActiveSessions.begin() ) {
		Session & session = *i;
		mActiveSessions.erase( i );
		delete &session;
	}
	for ( i = mFreeSessions.begin(); i != mFreeSessions.end(); i = mFreeSessions.begin() ) {
		Session & session = *i;
		mFreeSessions.erase( i );
		delete &session;
	}
}

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

Session & App::activateSession() {
	Session * session;
	if ( mFreeSessions.empty() ) {
		session = new Session( *this );
		mActiveSessions.push_front( *session );
	} else {
		SessionList::iterator i = mFreeSessions.begin();
		mFreeSessions.erase( i );
		mActiveSessions.push_front( *i );
		session = &*i;
	}
	return *session;
}

void App::freeSession( Session & session ) {
	session.reset();
	mActiveSessions.erase( mActiveSessions.iterator_to( session ) );
	mFreeSessions.push_front( session );
}

void App::startAccept() {
	// Get a session
	Session & session = activateSession();
	
	// Start accepting connections
	mAcceptor->async_accept( session.getSocket(), 
			boost::bind( &App::onAccept, this, boost::ref( session ), _1 ) );
}

void App::onAccept( Session & session, const ErrorCode & acceptError ) {
	if ( acceptError ) {
		throw SystemError( acceptError );
	}
	// Handle this connection
	session.startRead();
	// Listen for another connection
	startAccept();
}

