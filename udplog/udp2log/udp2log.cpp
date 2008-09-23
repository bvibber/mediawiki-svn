// UDP -> stdout receiver

#include <stdio.h>
#include <iostream>
#include <boost/program_options.hpp>
#include <string>
#include <boost/shared_ptr.hpp>
#include <signal.h>
#include "../srclib/Socket.h"
#include "Udp2LogConfig.h"


std::string configFileName("/etc/udp2log");

Udp2LogConfig config;

void OnHangup(int) 
{
	config.reload = true;
}

void OnAlarm(int)
{
	config.fixBrokenProcessors = true;
}

int main(int argc, char** argv)
{
	using namespace std;
	using namespace boost::program_options;
	unsigned int port = 8420;
	
	options_description optDesc;
	optDesc.add_options()
		("port,p", value<unsigned int>(&port)->default_value(port), "port")
		("config-file,f", value<string>(&configFileName)->default_value(configFileName), 
		 	"config file location" );

	try {
		variables_map vm;
		store(command_line_parser(argc, argv).options(optDesc).run(), vm);
		notify(vm);   
	} catch (exception & e) {
		cerr << e.what() << endl;
		cerr << "Usage: " << argv[0] << 
			" [-p <port>] [-f <config_file>]\n";
		return 1;
	}
	if (port > 65535) {
		cerr << "Invalid port number \"" << argv[2] << "\"\n";
		return 1;
	}
	try {
		config.Open(configFileName);
	} catch (runtime_error & e) {
		cerr << e.what() << endl;
		return 1;
	}

	signal(SIGHUP, OnHangup);
	signal(SIGALRM, OnAlarm);
	signal(SIGPIPE, SIG_IGN);

	IPAddress any(INADDR_ANY);
	SocketAddress saddr(any, (unsigned short int)port);
	UDPSocket socket;
	if (!socket) {
		cerr << "Unable to open socket\n";
		return 1;
	}
	socket.Bind(saddr);

	boost::shared_ptr<SocketAddress> address;
	const size_t bufSize = 65536;
	char buffer[bufSize];
	char *line1, *line2;
	ssize_t bytesRemaining, bytesRead;
	for (;;) {
		bytesRead = socket.RecvFrom(buffer, bufSize, address);
		if (bytesRead <= 0) {
			continue;
		}

		// Reload configuration
		try {
			config.Reload();
		} catch (runtime_error & e) {
			cerr << e.what() << endl;
			continue;
		}

		// Split into lines and hand off to the processors
		line1 = buffer;
		bytesRemaining = bytesRead;
		while (bytesRemaining) {
			// Find the next line break
			line2 = (char*)memchr(line1, '\n', bytesRemaining);
			if (line2) {
				// advance line2 to the start of the next line
				line2++;
				// Process the line
				config.ProcessLine(line1, line2 - line1);
				bytesRemaining -= line2 - line1;
			} else {
				// no more lines, process the remainder of the buffer
				config.ProcessLine(line1, bytesRemaining);
				bytesRemaining = 0;
			}
			line1 = line2;
		}
	}
}

