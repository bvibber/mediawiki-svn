#ifndef IP_ADDRESS_H
#define IP_ADDRESS_H

#include <arpa/inet.h>
#include <netinet/in.h>
#include <string>
#include <boost/shared_ptr.hpp>

class SocketAddress;

class IPAddress {
public:
	IPAddress()
		: type(AF_INET), domain(PF_INET), length(sizeof(in_addr)) {}

	IPAddress(struct in_addr * src) 
		: type(AF_INET), domain(PF_INET), length(sizeof(in_addr))
	{ 
		data.v4 = *src;
	}

	IPAddress(struct in6_addr * src) 
		: type(AF_INET6), domain(PF_INET6), length(sizeof(in6_addr))
	{
		data.v6 = *src;
	}

	IPAddress(uint32_t addr)
		: type(AF_INET), domain(PF_INET), length(sizeof(in_addr))
	{
		data.v4.s_addr = addr;
	}

	IPAddress(int type, char * presentation);

	void* GetBinaryData() { return &data; }
	size_t GetBinaryLength() { return length; }
	int GetType() { return type; }
	int GetDomain() { return domain; }
	std::string & ToString();
	boost::shared_ptr<SocketAddress> NewSocketAddress(unsigned short int port);

protected:
	enum { BUFSIZE = 200 };

	std::string presentation;
	union {
		struct in6_addr v6;
		struct in_addr v4;
	} data;
	int type, domain, length;
};

#endif
