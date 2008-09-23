#include <fstream>
#include <cstring>
#include <sstream>

#include "Udp2LogConfig.h"

Udp2LogConfig::Udp2LogConfig() 
	: reload(false)
{}

void Udp2LogConfig::Open(const std::string & name)
{	
	using namespace std;
	fileName = name;
	Load();
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
		for (f.getline(line, maxLineSize); f.good(); f.getline(line, maxLineSize), lineNum++) {
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

void Udp2LogConfig::FixBrokenProcessors()
{
	boost::ptr_vector<LogProcessor>::iterator i;
	for (i = processors.begin(); i != processors.end(); i++) {
		i->FixIfBroken();
	}
}

void Udp2LogConfig::Reload() 
{
	if (reload) {
		Load();
		reload = false;
		fixBrokenProcessors = false;
	} else if (fixBrokenProcessors) {
		FixBrokenProcessors();
		fixBrokenProcessors = false;
	}
}

void Udp2LogConfig::ProcessLine(char *buffer, size_t size) 
{
	boost::ptr_vector<LogProcessor>::iterator i;
	for (i = processors.begin(); i != processors.end(); i++) {
		i->ProcessLine(buffer, size);
	}
}

