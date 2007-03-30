#ifndef HELP_H
#define HELP_H

#include <map>
#include <string>

extern std::map<std::string, std::string> help_text;
std::string const *get_help(std::string const &topic);

#endif
