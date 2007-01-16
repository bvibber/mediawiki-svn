#include "SocketAddress.h"
#include <stdexcept>
#include <boost/lexical_cast.hpp>

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

std::string SocketAddress::ToString()
{
	std::string s;
	switch (data.v4.sin_family) {
		case AF_INET:
			s = IPAddress(&data.v4.sin_addr).ToString() +
				std::string(":") + boost::lexical_cast<std::string>(data.v4.sin_port);
			break;
		case AF_INET6:
			s = IPAddress(&data.v6.sin6_addr).ToString() +
				std::string(":") + boost::lexical_cast<std::string>(data.v6.sin6_port);
			break;
		default:
			throw std::runtime_error("SocketAddress::ToString: Invalid address family");
	}
	return s;
}
