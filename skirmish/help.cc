#include <string>
#include "help.h"

std::string const *
get_help(std::string const &topic)
{
	std::map<std::string, std::string>::const_iterator it =
		help_text.find(topic);
	if (it == help_text.end())
		return 0;
	return &it->second;
}
