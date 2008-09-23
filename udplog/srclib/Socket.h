#ifndef SOCKET_H______
#define SOCKET_H______

#include <set>
#include <vector>
#include <boost/shared_ptr.hpp>
#include <sys/socket.h>
#include <cerrno>
#include "SocketAddress.h"
#include "Exception.h"


// Socket base class
// Do not instantiate this directly
class Socket
{
public:
	Socket(int domain, int type, int protocol)
		: fd(-1) 
	{
		fd = socket(domain, type, protocol);
		if (fd == -1) {
			RaiseError("Socket constructor");
			good = false;
		} else {
			good = true;
		}
	}

	operator bool() {
		return good;
	}

	int Connect(SocketAddress & s) {
		if (connect(fd, s.GetBinaryData(), s.GetBinaryLength()) < 0) {
			RaiseError("Socket::Connect");
			return errno;
		} else {
			return 0;
		}
	}

	int Shutdown(int how = 2) {
		if (shutdown(fd, how) < 0) {
			RaiseError("Socket::Shutdown");
			return errno;
		} else {
			return 0;
		}
	}

	// TODO
	// SocketAddress & Accept();

	int Bind(SocketAddress & s) {
		if (bind(fd, s.GetBinaryData(), s.GetBinaryLength()) < 0) {
			RaiseError("Socket::Bind");
			return errno;
		} else {
			return 0;
		}
	}

	boost::shared_ptr<SocketAddress> GetPeerAddress() {
		if (connect(fd, SocketAddress::GetBuffer(), SocketAddress::GetBufferLength()) < 0) {
			RaiseError("Socket::GetPeerAddress");
			peer = boost::shared_ptr<SocketAddress>((SocketAddress*)NULL);
		} else {
			peer = boost::shared_ptr<SocketAddress>(SocketAddress::NewFromBuffer());
		}
		return peer;
	}

	int Listen(unsigned int backlog = 50) {
		if (listen(fd, backlog) < 0) {
			RaiseError("Socket::Listen");
			return errno;
		} else {
			return 0;
		}
	}

	ssize_t Send(void * buf, size_t len, int flags = 0) {
		ssize_t length = send(fd, buf, len, flags);
		if (length == (ssize_t)-1) {
			RaiseError("Socket::Send");
		}
		return length;
	}

	ssize_t SendTo(void * buf, size_t len, SocketAddress & to, int flags = 0) {
		ssize_t length = sendto(fd, buf, len, flags, to.GetBinaryData(), to.GetBinaryLength());
		if (length == (ssize_t)-1) {
			RaiseError("Socket::SendTo");
		}
		return length;
	}

	ssize_t Recv(void *buf, size_t len, int flags = 0) {
		ssize_t length = recv(fd, buf, len, flags);
		if (length == (ssize_t)-1) {
			RaiseError("Socket::Recv");
		}
		return length;
	}

	ssize_t RecvFrom(void *buf, size_t len, boost::shared_ptr<SocketAddress> & to, int flags = 0) {
		socklen_t addrLength = SocketAddress::GetBufferLength();
		ssize_t length = recvfrom(fd, buf, len, flags, SocketAddress::GetBuffer(), &addrLength);
		if (length == (ssize_t)-1) {
			RaiseError("Socket::RecvFrom");
		}
		to = SocketAddress::NewFromBuffer();
		return length;
	}

	// Ignore a given set of errors
	void Ignore(boost::shared_ptr<std::set<int> > s) {
		ignoreErrors.push_back(s);
	}
		
	// Ignore all errors
	void IgnoreAll() {
		ignoreErrors.push_back(boost::shared_ptr<std::set<int> >((std::set<int>*)NULL));
	}
		
	// Restore the previous ignore set
	void PopIgnore() {
		ignoreErrors.pop_back();
	}

	void RaiseError(const char* msg);
protected:
	int fd;
	boost::shared_ptr<SocketAddress> peer;
	bool good;
	std::vector<boost::shared_ptr<std::set<int> > > ignoreErrors;
};

class UDPSocket : public Socket
{
public:
	UDPSocket(int domain = PF_INET)
		: Socket(domain, SOCK_DGRAM, 0) {}
	
	UDPSocket(IPAddress & addr, int port) 
		: Socket(addr.GetDomain(), SOCK_DGRAM, 0)
	{
		boost::shared_ptr<SocketAddress> saddr = addr.NewSocketAddress(port);
		if (Connect(*saddr)) {
			good = false;
		}
	}
};

#endif
