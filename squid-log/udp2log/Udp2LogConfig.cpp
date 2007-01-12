#include <inotifytools/inotifytools.h>
#include <inotifytools/inotify.h>
#include <fstream>
#include <cstring>
#include <functional>
#include <sstream>

#include "Udp2LogConfig.h"

Udp2LogConfig::Udp2LogConfig() 
	: reload(false), die(false)
{}

Udp2LogConfig::~Udp2LogConfig() {
	die = true;
	if (watcherThread.get()) {
		watcherThread->join();
	}
}

void Udp2LogConfig::Open(const std::string & name)
{	
	using namespace std;
	fileName = name;
	Load();
	StartWatcherThread();
}

void Udp2LogConfig::Load()
{
	using namespace std;
	const streamsize maxLineSize = 65536;
	char line[maxLineSize];
	char * type;
	char * params;
	boost::ptr_vector<LogProcessor> newProcessors;

	ifstream f(fileName.c_str());
	if (!f.good()) {
		throw runtime_error("Unable to open config file");
	}

	int lineNum = 1;
	try {
		// Parse all lines
		for (f.get(line, maxLineSize); f.good(); f.get(line, maxLineSize), lineNum++) {
			if (line[0] == '#') {
				continue;
			}
			type = strtok(line, " \t");
			if (!type) {
				continue;
			} else {
				params = strtok(NULL, "");
				LogProcessor * processor = NULL;
				if (!strcmp(type, "file")) {
					processor = FileProcessor::NewFromConfig(params);
				} else if (!strcmp(type, "pipe")) {
					processor = PipeProcessor::NewFromConfig(params);
				} else {
					throw ConfigError("Unrecognised log type");
				}

				if (processor) {
					newProcessors.push_back(processor);
				}
			}
		}

		// Swap in the new configuration
		// The old configuration will go out of scope, closing files and pipes
		processors.swap(newProcessors);
	} catch (ConfigError & e) {
		stringstream s;
		s << "Error in configuration file on line " << lineNum << ": " << e.what();
		throw runtime_error(s.str().c_str());
	}
}

void Udp2LogConfig::Reload() 
{
	if (reload) {
		Load();
		reload = false;
	}
}

void Udp2LogConfig::ProcessLine(char *buffer, size_t size, boost::shared_ptr<SocketAddress> address) 
{
	boost::ptr_vector<LogProcessor>::iterator i;
	for (i = processors.begin(); i != processors.end(); i++) {
		i->ProcessLine(buffer, size, address);
	}
}

void Udp2LogConfig::StartWatcherThread() 
{
	watcherThread = new boost::thread(
		std::bind1st(std::mem_fun(&Udp2LogConfig::WatchConfig), this));
}

void Udp2LogConfig::WatchConfig()
{
	static inotifyInitialised = false;
	if (!inotifyInitialised) {
		inotifytools_initialize();
	}
	inotifytools_watch_file(fileName.c_str(), IN_CLOSE_WRITE);
	
	while (!die) {
		if (inotifytools_next_event(1)) {
			reload = true;
		}
	}
}
