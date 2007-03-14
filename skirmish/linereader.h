#ifndef LINEREADER_H
#define LINEREADER_H

#include <string>
#include <map>

struct linereader {
	linereader();
	~linereader();

	bool readline(std::string &, std::string const &);
	void set_prompt_variable(std::string const &var, std::string const &value);

private:
	std::string form_prompt(std::string const &);

	std::map<std::string, std::string> promptvars;
};

#endif
