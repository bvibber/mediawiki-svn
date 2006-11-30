#include <iostream>
#include "HostEntry.h"
#include "Socket.h"

using namespace std;

int main(int argc, char** argv) 
{
	char *host;
	char * end = NULL;
	unsigned long port;
	unsigned long sampleFactor = 1;

	if (argc < 3) {
		cerr << "Usage: " << argv[0] << " <host> <port> [<sampling-factor>]\n";
		return 1;
	}
	host = argv[1];
	port = strtoul(argv[2], &end, 10);
	if (!end || *end != '\0' || port > 65535) {
		cerr << "Invalid port number \"" << argv[2] << "\"\n";
		return 1;
	}

	if (argc > 3) {
		sampleFactor = strtoul(argv[3], &end, 10);
		if (!end || *end != '\0' || port > 65535) {
			cerr << "Invalid sample factor \"" << argv[3] << "\"\n";
			return 1;
		}
	}

	HostEntry entry(host);
	if (entry.NumAddresses() == 0) {
		cerr << "Unable to get address for host \"" << host << "\"\n";
		return 1;
	}
	IPAddress & ip = entry.GetAddress(0);
	UDPSocket socket(ip, port);
	if (!socket) {
		cerr << "Unable to open socket\n";
		return 1;
	}

	// max line length ~= max UDP packet size
	const size_t maxLineLength = 65535;
	char buffer[maxLineLength];

	unsigned long sampleCounter = 0;
	// Start the main loop
	while (cin.good()) {
		cin.getline(buffer, maxLineLength);
		
		sampleCounter ++;
		if (sampleCounter >= sampleFactor) {
			sampleCounter = 0;
			socket.Send(buffer, cin.gcount());
		}
	}
	return 0;
}
