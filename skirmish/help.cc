#include <string>
#include "help.h"

namespace help {

std::string const *
get(std::string const &topic)
{
	std::map<std::string, std::string>::const_iterator it =
		help_text.find(topic);

	if (it == help_text.end())
		return 0;

	return &it->second;
}

std::vector<std::string>
list_topics()
{
	std::map<std::string, std::string>::const_iterator
		it = help_text.begin(), 
		end = help_text.end();

	std::vector<std::string> topics;

	for (; it != end; ++it)
		topics.push_back(it->first);

	return topics;
}

} // namespace help
