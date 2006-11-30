#include "SocketAddress.h"
#include <stdexcept>

boost::shared_ptr<SocketAddress> SocketAddress::NewFromBuffer()
{
	switch (buffer.s.sa_family) {
		case AF_INET:
			return boost::shared_ptr<SocketAddress>(
					new SocketAddress((struct sockaddr_in*)&buffer));
		case AF_INET6:
			return boost::shared_ptr<SocketAddress>(
					new SocketAddress((struct sockaddr_in6*)&buffer));
		default:
			throw std::runtime_error("SocketAddress::NewFromBuffer: Invalid address family");
	}
}

SocketAddress::BufferType SocketAddress::buffer = {{0}};

