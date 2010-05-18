#ifndef MAXCACHE_TYPEDEFS_H
#define MAXCACHE_TYPEDEFS_H

#include "boost-config.h"
#include <string>
#include <boost/shared_ptr.hpp>
#include <boost/date_time/posix_time/posix_time_types.hpp>
#include <boost/intrusive/intrusive_fwd.hpp>
#include <boost/asio.hpp>

namespace MaxCache {
	// Misc
	typedef boost::shared_ptr<std::string> StringPtr;
	typedef boost::posix_time::ptime Time;

	// Intrusive
	namespace Intrusive = boost::intrusive;

	// Asio
	namespace Asio = boost::asio;

	typedef Asio::ip::tcp Tcp;
	typedef Asio::io_service Service;
	typedef Tcp::endpoint Endpoint;
	typedef Tcp::acceptor Acceptor;
	typedef boost::shared_ptr<Acceptor> AcceptorPtr;
	typedef boost::system::error_code ErrorCode;
	typedef boost::system::system_error SystemError;

	typedef Asio::streambuf Buffer;
	typedef Asio::buffers_iterator< Buffer::const_buffers_type > BufferIterator;
	typedef Tcp::socket Socket;
	
}

#endif
