// UDP -> stdout receiver

#include <cstdio>
#include <iostream>
#include <fstream>
#include <boost/program_options.hpp>
#include <string>
#include <boost/shared_ptr.hpp>
#include <signal.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <pwd.h>
#include <cstdlib>

#include "../srclib/Exception.h"
#include "../srclib/Socket.h"
#include "Udp2LogConfig.h"

std::string configFileName("/etc/udp2log");
std::string logFileName("/var/log/udp2log/udp2log.log");
std::string pidFileName("/var/run/udp2log.pid");
std::string daemonUserName("udp2log");

Udp2LogConfig config;

void OnHangup(int) 
{
	config.reload = true;
}

void OnAlarm(int)
{
	config.fixBrokenProcessors = true;
}

pid_t Daemonize()
{
	// Open PID file as root
	std::ofstream pidFile(pidFileName.c_str());
	if (!pidFile.good()) {
		throw libc_error("Error opening PID file");
	}

	// Fetch details of new user
	struct passwd * userData = getpwnam(daemonUserName.c_str());
	if (!userData) {
		throw std::runtime_error(
			std::string("No such user: ") + daemonUserName);
	}

	// Change user
	if (setgid(userData->pw_gid) == -1
			|| setuid(userData->pw_uid) == -1) 
	{
		throw libc_error("Error changing user ID");
	}

	// Open files for new standard file handles
	int logFd = open(logFileName.c_str(), O_WRONLY | O_CREAT | O_APPEND, 0666);
	if (logFd == -1) {
		throw libc_error("Error opening log file");
	}
	int nullFd = open("/dev/null", O_WRONLY);
	if (nullFd == -1) {
		throw libc_error("Error opening /dev/null");
	}

	// Fork
	pid_t pid = fork();
	if (pid == -1) {
		throw libc_error("Error creating new process");
	} else if (pid) {
		// This is the parent process
		// Write PID file
		pidFile << pid << std::endl;
		return pid;
	}

	// Redirect standard file handles
	dup2(nullFd, STDIN_FILENO);
	dup2(logFd, STDOUT_FILENO);
	dup2(logFd, STDERR_FILENO);

	// Set session
	setsid();

	// Change directory
	chdir("/");
	return 0;
}

void MakeAbsolutePath(std::string & path) {
	if (path.size() && path[0] != '/' ) {
		char * cwd = getcwd(NULL, 0);
		if (cwd) {
			path = std::string(cwd) + "/" + path;
			free(cwd);
		}
	}
}

int main(int argc, char** argv)
{
	using namespace std;
	using namespace boost::program_options;
	unsigned int port = 8420;
	bool daemon = false;
	
	// Process command line
	options_description optDesc;
	optDesc.add_options()
		("help", "Show help message.")
		("port,p", value<unsigned int>(&port)->default_value(port), "UDP port.")
		("config-file,f", value<string>(&configFileName)->default_value(configFileName), 
		 	"Config file location.")
		("daemon", "Run as a background process.")
		("log-file", value<string>(&logFileName)->default_value(logFileName),
		 	"The log file, for internal udp2log messages. Used only if --daemon is specified.")
		("pid-file", value<string>(&pidFileName)->default_value(pidFileName),
		 	"The location to write the new PID, if --daemon is specified.")
		("user", value<string>(&daemonUserName)->default_value(daemonUserName),
		 	"User to switch to, after daemonizing");

	variables_map vm;
	try {
		store(command_line_parser(argc, argv).options(optDesc).run(), vm);
		notify(vm);   
	} catch (exception & e) {
		cerr << e.what() << endl;
		cerr << "Usage: " << argv[0] << 
			" [-p <port>] [-f <config_file>]\n";
		return 1;
	}
	if (vm.count("help")) {
		cerr << optDesc << "\n";
		return 1;
	}
	if (port > 65535) {
		cerr << "Invalid port number \"" << argv[2] << "\"\n";
		return 1;
	}
	if (vm.count("daemon")) {
		daemon = true;
	}

	// Guard against chdir("/")
	MakeAbsolutePath(configFileName);
	MakeAbsolutePath(logFileName);
	MakeAbsolutePath(pidFileName);
	
	// Fork
	if (daemon) {
		try {
			if (Daemonize()) {
				return 0;
			}
		} catch (runtime_error & e) {
			cerr << e.what() << endl;
			return 1;
		}
	}

	// Read configuration and open log files and pipes
	try {
		config.Open(configFileName);
	} catch (runtime_error & e) {
		cerr << e.what() << endl;
		return 1;
	}

	signal(SIGHUP, OnHangup);
	signal(SIGALRM, OnAlarm);
	signal(SIGPIPE, SIG_IGN);

	// Open the receiving socket
	IPAddress any(INADDR_ANY);
	SocketAddress saddr(any, (unsigned short int)port);
	UDPSocket socket;
	if (!socket) {
		cerr << "Unable to open socket\n";
		return 1;
	}
	socket.Bind(saddr);

	// Process received packets
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

