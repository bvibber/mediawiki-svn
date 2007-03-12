#ifndef LINEREADER_H
#define LINEREADER_H

#include <string>

struct linereader {
	linereader();
	~linereader();

	bool readline(std::string &, std::string const &);
};

#endif
