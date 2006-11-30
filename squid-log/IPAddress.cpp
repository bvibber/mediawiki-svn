#include "IPAddress.h"
#include "Exception.h"
#include "SocketAddress.h"

std::string & IPAddress::ToString()
{
	if (!presentation.size()) {
		char buffer[BUFSIZE];
		// Paranoia
		buffer[0] = '\0';
		const char *ret = inet_ntop(GetType(), GetBinaryData(), buffer, BUFSIZE);
		if (!ret) {
			throw libc_error("Error in inet_ntop");
		}
		// More paranoia
		buffer[BUFSIZE-1] = '\0';
		presentation = buffer;
	}
	return presentation;
}

IPAddress::IPAddress(int type, char *presentation)
{
	int ret = inet_pton(type, presentation, &data);
	if (!ret ) {
		throw std::runtime_error("Invalid IP address");
	} else if (ret < 0) {
		throw libc_error("Error in inet_ptoa");
	}
}

boost::shared_ptr<SocketAddress> IPAddress::NewSocketAddress(unsigned short int port)
{
	if (type == AF_INET) {
		sockaddr_in v4;
		v4.sin_family = AF_INET;
		v4.sin_addr = data.v4;
		v4.sin_port = htons(port);
		return boost::shared_ptr<SocketAddress>(new SocketAddress(&v4));
	} else if (type == AF_INET) {
		sockaddr_in6 v6;
		v6.sin6_family = AF_INET6;
		v6.sin6_addr = data.v6;
		v6.sin6_port = htons(port);
		return boost::shared_ptr<SocketAddress>(new SocketAddress(&v6));
	} else {
		throw std::runtime_error("IPAddress::NewSocketAddress: invalid address type");
	}
}
