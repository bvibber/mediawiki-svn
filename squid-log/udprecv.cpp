#include <stdio.h>
#include <iostream>
#include "Socket.h"

// Basic UDP -> stdout receiver
int main(int argc, char** argv)
{
	using namespace std;
	
	if (argc > 2) {
		cerr << "Usage: " << argv[0] << " <port>\n";
		return 1;
	}

	char *end;
	unsigned long port = 8420;
	if (argc > 1) {
		port = strtoul(argv[1], &end, 10);
		if (!end || *end != '\0' || port > 65535) {
			cerr << "Invalid port number \"" << argv[2] << "\"\n";
			return 1;
		}
	}
	IPAddress any(INADDR_ANY);
	SocketAddress saddr(any, (unsigned short int)port);
	UDPSocket socket;
	if (!socket) {
		cerr << "Unable to open socket\n";
		return 1;
	}
	socket.Bind(saddr);

	boost::shared_ptr<SocketAddress> address;
	const size_t bufSize = 65535;
	char buffer[bufSize];
	for (;;) {
		ssize_t bytesRead = socket.RecvFrom(buffer, bufSize, address);
		if (bytesRead > 0) {
			// Add connection prefix and write the packet
			cout << address->ToString() << " ";
			cout.write(buffer, bytesRead);
			// Write a line-ending if there wasn't one already
			if (buffer[bytesRead - 1] != '\n') {
				cout << "\n";
			}
		}
	}
}
