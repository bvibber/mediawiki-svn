#include "LogProcessor.h"
#include <stdio.h>
//---------------------------------------------------------------------------
// FileProcessor
//---------------------------------------------------------------------------

virtual LogProcessor * FileProcessor::NewFromConfig(char * params)
{
	char * strFactor = strtok(params, " \t");
	if (strFactor == NULL) {
		throw ConfigError(
			"Invalid file specification, format is: file <sample-factor> <filename>"
		);
	}
	int factor = atoi(strFactor);
	if (factor <= 0) {
		throw ConfigError(
			"Invalid sample factor in file specification, must be a number greater than zero"
		);
	}
	char * filename = strtok(params, "");
	FileProcessor * fp = new FileProcessor(filename);
	if (!fp->IsOpen()) {
		delete fp;
		throw ConfigError("Unable to open file");
	}
	return (LogProcessor*)fp;
}

virtual void FileProcessor::ProcessLine(char *buffer, size_t size, boost::shared_ptr<SocketAddress> address)
{
	if (Sample()) {
		buffer[size] = '\n';
		f.write(buffer, size + 1);
	}
}

//---------------------------------------------------------------------------
// PipeProcessor
//---------------------------------------------------------------------------

virtual LogProcessor * PipeProcessor::NewFromConfig(char * params)
{
	char * strFactor = strtok(params, " \t");
	if (strFactor == NULL) {
		throw ConfigError(
			"Invalid pipe specification, format is: pipe <sample-factor> <command>"
		);
	}
	int factor = atoi(strFactor);
	if (factor <= 0) {
		throw ConfigError(
			"Invalid sample factor in pipe specification, must be a number greater than zero"
		);
	}
	char * command = strtok(params, "");
	PipeProcessor * pp = new PipeProcessor(command);
	if (!pp->IsOpen()) {
		delete pp;
		throw ConfigError("Unable to open pipe");
	}
	return (LogProcessor*)pp;
}

virtual void PipeProcessor::ProcessLine(char *buffer, size_t size, boost::shared_ptr<SocketAddress> address)
{
	if (Sample()) {
		buffer[size] = '\n';
		fwrite(buffer, 1, size, f);
	}
}

