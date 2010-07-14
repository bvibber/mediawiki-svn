#ifndef SOCKET_ADDRESS_H
#define SOCKET_ADDRESS_H

#include <netinet/in.h>
#include "IPAddress.h"
#include <boost/shared_ptr.hpp>
#include <stdexcept>
#include <cstring>

class SocketAddress 
{
public:
	enum {BUFSIZE = sizeof(short int) + 4096};
	
	static struct sockaddr * GetBuffer() {
		return (struct sockaddr*)&buffer;
	}

	static size_t GetBufferLength() {
		return BUFSIZE;
	}

	static boost::shared_ptr<SocketAddress> NewFromBuffer();

	SocketAddress(struct sockaddr_in * s)
		: length(sizeof(sockaddr_in))
	{
		data.v4 = *s;
		data.v4.sin_family = AF_INET;
	}

	SocketAddress(struct sockaddr_in6 * s)
		: length(sizeof(sockaddr_in6))
	{
		data.v6 = *s;
		data.v6.sin6_family = AF_INET6;
	}

	SocketAddress(IPAddress & ip, unsigned short int port) 
	{
		switch (ip.GetType()) {
			case AF_INET:
				data.v4.sin_family = AF_INET;
				std::memcpy(&data.v4.sin_addr, ip.GetBinaryData(), ip.GetBinaryLength());
				data.v4.sin_port = htons(port);
				length = sizeof(sockaddr_in);
				break;
			case AF_INET6:
				data.v6.sin6_family = AF_INET6;
				std::memcpy(&data.v6.sin6_addr, ip.GetBinaryData(), ip.GetBinaryLength());
				data.v6.sin6_port = htons(port);
				length = sizeof(sockaddr_in6);
				break;
			default:
				throw std::runtime_error("Invalid address type");
		}
	}

	struct sockaddr * GetBinaryData() { return (struct sockaddr*)&data; }
	size_t GetBinaryLength() { return length; }
	sa_family_t GetType() { return data.v4.sin_family; }

	std::string ToString();
protected:
	static union BufferType {
		char c[BUFSIZE];
		struct sockaddr s;
	} buffer;

	union {
		struct sockaddr_in v4;
		struct sockaddr_in6 v6;
	} data;
	int length;
};
#endif
