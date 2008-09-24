#ifndef LOGPROCESSOR_H
#define LOGPROCESSOR_H

#include <fstream>
#include <sys/time.h>
#include "../srclib/Socket.h"

class LogProcessor
{
public:
	virtual void ProcessLine(char *buffer, size_t size) = 0;
	virtual void FixIfBroken() {}
	virtual ~LogProcessor() {}

protected:
	LogProcessor(int factor_, bool flush_)
		: counter(0), factor(factor_), flush(flush_)
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
	bool flush;
};

class FileProcessor : public LogProcessor
{
public:
	static LogProcessor * NewFromConfig(char * params, bool flush);
	virtual void ProcessLine(char *buffer, size_t size);

	FileProcessor(char * filename, int factor_, bool flush_) 
		: LogProcessor(factor_, flush_)
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
	static LogProcessor * NewFromConfig(char * params, bool flush);
	virtual void ProcessLine(char *buffer, size_t size);
	virtual void FixIfBroken();

	PipeProcessor(char * command_, int factor_, bool flush_) 
		: LogProcessor(factor_, flush_)
	{
		command = strdup(command_);
		f = popen(command, "w");
	}

	~PipeProcessor() 
	{
		free(command);
		if (f) {
			pclose(f);
		}
	}

	bool IsOpen() 
	{
		return (bool)f;
	}

	FILE * f;
	char * command;
	enum {RESTART_INTERVAL = 5};
};


#endif
