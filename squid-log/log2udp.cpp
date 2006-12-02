#include <iostream>
#include <fstream>
#include <stdint.h>
#include <stdio.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/stat.h>
#include <boost/program_options.hpp>
#include <memory>
#include "HostEntry.h"
#include "Socket.h"

int OpenFifo(const char * path);


int main(int argc, char** argv) 
{
	using namespace std;
	using namespace boost::program_options;
	using namespace boost::program_options::command_line_style;

	unsigned int port = 8420;
	string host("localhost");
	unsigned int sampleFactor = 1;
	string fifoPath("-");

	positional_options_description posDesc;
	options_description optDesc;
	posDesc.add("fifo-path", 1);
	optDesc.add_options()
		("host,h", value<string>(&host)->default_value(host), "host")
		("port,p", value<unsigned int>(&port)->default_value(port), "port")
		("factor,f", value<unsigned int>(&sampleFactor)->default_value(sampleFactor), "sampling factor")
		("fifo-path", value<string>(&fifoPath)->default_value(fifoPath), "fifo path");

	try {
		variables_map vm;
		store(command_line_parser(argc, argv).options(optDesc).positional(posDesc).run(), vm);
		notify(vm);   
	} catch (exception & e) {
		cerr << e.what() << endl;
		cerr << "Usage: " << argv[0] << 
			" [-h <host>] [-p <port>] [-f <sampling-factor>] [<fifo-path>]\n";
		return 1;
	}
	if (port > 65535) {
		cerr << "Invalid port number \"" << argv[2] << "\"\n";
		return 1;
	}

	HostEntry entry((char*)host.c_str());
	if (entry.NumAddresses() == 0) {
		cerr << "Unable to get address for host \"" << host << "\"\n";
		return 1;
	}
	UDPSocket socket(entry.GetAddress(0), port);
	if (!socket) {
		cerr << "Unable to open socket\n";
		return 1;
	}

	// Ignore any further socket errors
	socket.IgnoreAll();
	unsigned long long counter = 0;
	int fd;
	do {
		if (fifoPath == "-") {
			fd = STDIN_FILENO;
		} else {
			fd = OpenFifo(fifoPath.c_str());
		}

		FILE * stream = fdopen(fd, "r");
		if (!stream) {
			throw libc_error("fdopen");
		}

		// buffer size ~= max UDP packet size
		const size_t outBufferSize = 65535;
		const size_t prefixLength = 21;
		size_t inBufferSize = outBufferSize - prefixLength;
		char outBuffer[outBufferSize];
		char * inBuffer = (char*)malloc(inBufferSize);

		// Start the main loop
		for (;; counter++) {
			/*
			ssize_t bytesRead = getline(&inBuffer, &inBufferSize, stream);
			if (bytesRead == -1) {
				if (errno) {
					// Warn 
					cerr << "read: " << strerror(errno) << endl;
				} // else EOF
				// Read error, close stream
				break;
			}
			*/

			if (!fgets(inBuffer, inBufferSize, stream)) {
				if (ferror(stream)) {
					// Warn 
					cerr << "read: " << strerror(errno) << endl;
				} // else EOF
				// Read error, close stream
				break;
			}
			ssize_t bytesRead = strlen(inBuffer);

			
			if (counter % sampleFactor == 0) {
				// Truncate the input buffer so that it fits in the output buffer
				if ((size_t)bytesRead > outBufferSize - prefixLength) {
					bytesRead = (ssize_t)(outBufferSize - prefixLength);
				}
				// Write the sequence number
				sprintf(outBuffer, "%20llu ", counter);
				memcpy(outBuffer + prefixLength, inBuffer, bytesRead);
				socket.Send(outBuffer, prefixLength + bytesRead);
			}
		}
		if (fd != STDIN_FILENO) {
			if (fclose(stream)) {
				// Error on close, warn and continue
				cerr << "close: " << strerror(errno) << endl;
			}
		}
		// exit if we were reading from stdin, otherwise reopen and wait
	} while (fd != STDIN_FILENO);

	return 0;
}

int OpenFifo(const char * path)
{
	using namespace std;
	struct stat fifoStat;

	if (stat(path, &fifoStat) == ENOENT) {
		if (mkfifo(path, 0666)) {
			throw libc_error("Error creating fifo");
		}
	}
	int fd = open(path, O_RDONLY);
	if (fd == -1) {
		if (errno == ENOENT) {
			if (mkfifo(path, 0666)) {
				throw libc_error("Error creating fifo");
			}
			fd = open(path, O_RDONLY);
			if (fd == -1) {
				throw libc_error("open");
			}
		} else {
			throw libc_error("open");
		}
	}
	if (fstat(fd, &fifoStat)) {
		throw libc_error("fstat");
	}
	if (!S_ISFIFO(fifoStat.st_mode)) {
		cerr << "Error: the file specified is not a fifo\n";
		exit(1);
	}
	return fd;
}

