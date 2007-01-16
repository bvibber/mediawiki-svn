#ifndef LOGPROCESSOR_H
#define LOGPROCESSOR_H

#include <fstream>
#include <boost/shared_ptr.hpp>
#include "../srclib/Socket.h"

class LogProcessor
{
public:
	virtual void ProcessLine(char *buffer, size_t size, boost::shared_ptr<SocketAddress> address) = 0;
	virtual ~LogProcessor() {}

protected:
	LogProcessor(int factor_)
		: counter(0), factor(factor_)
	{}


	bool Sample() {
		if (factor != 1) {
			counter++;
			if (counter >= factor) {
				counter = 0;
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	int counter;
	int factor;
};

class FileProcessor : public LogProcessor
{
public:
	static LogProcessor * NewFromConfig(char * params);
	virtual void ProcessLine(char *buffer, size_t size, boost::shared_ptr<SocketAddress> address);

	FileProcessor(char * filename, int factor_) 
		: LogProcessor(factor_)
	{
		f.open(filename, std::ios::app | std::ios::out);
	}
	
	bool IsOpen() {
		return f.good();
	}

	std::ofstream f;
};

class PipeProcessor : public LogProcessor
{
public:
	static LogProcessor * NewFromConfig(char * params);
	virtual void ProcessLine(char *buffer, size_t size, boost::shared_ptr<SocketAddress> address);

	PipeProcessor(char * command, int factor_) 
		: LogProcessor(factor_)
	{
		f = popen(command, "w");
	}

	~PipeProcessor() 
	{
		if (f) {
			pclose(f);
		}
	}

	bool IsOpen() 
	{
		return (bool)f;
	}

	FILE * f;

};


#endif
