#ifndef HELP_H
#define HELP_H

#include <map>
#include <string>
#include <vector>

namespace help {

extern std::map<std::string, std::string> help_text;

std::string const	 *get(std::string const &topic);
std::vector<std::string>  list_topics();

}

#endif
