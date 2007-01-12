#ifndef UDP2LOGCONFIG_H
#define UDP2LOGCONFIG_H

#include <string>
#include <boost/ptr_vector.hpp>
#include <stdexcept>
#include "LogProcessor.h"

class Udp2LogConfig
{
public:
	Udp2LogConfig();
	~Udp2LogConfig();
	void Open(const std::string & name);
	void Load();
	void ProcessLine(char *buffer, size_t size, boost::shared_ptr<SocketAddress> address);

	std::string fileName;
	boost::ptr_vector<LogProcessor> processors;
	std::auto_ptr<boost::thread> watcherThread;
	bool reload;
	bool die;
protected:
	void WatchConfig();
	void StartWatcherThread();
};

class ConfigWatcher
{
public:
	ConfigWatcher();
	void operator()();

	int * reloaded;
};

class ConfigError : public std::runtime_error
{
	ConfigError(const char * s) 
		: runtime_error(s)
	{}
}

#endif
