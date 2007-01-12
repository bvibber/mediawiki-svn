#ifndef LOGPROCESSOR_H
#define LOGPROCESSOR_H

class LogProcessor
{
public:
	virtual LogProcessor * NewFromConfig(char * params) = 0;
	virtual void ProcessLine(char *buffer, size_t size, boost::shared_ptr<SocketAddress> address) = 0;

protected:
	LogProcessor()
		: counter(0), factor(1)
	{}

	bool Sample() {
		if (factor != 1) {
			counter++;
			if (counter == factor) {
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
	virtual void LogProcessor * NewFromConfig(char * params);
	virtual void ProcessLine(char *buffer, size_t size, boost::shared_ptr<SocketAddress> address);

	FileProcessor(char * filename) {
		f.open(filename, std::ios::ate | std::ios::out);
	}
	
	bool IsOpen() {
		return f.good();
	}

	ofstream f;
};

class PipeProcessor : public LogProcessor
{
public:
	virtual void LogProcessor * NewFromConfig(char * params);
	virtual void ProcessLine(char *buffer, size_t size, boost::shared_ptr<SocketAddress> address);

	PipeProcessor(char * command) 
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
