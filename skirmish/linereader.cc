#include <stdio.h>
#include <readline/readline.h>
#include <readline/history.h>

#include <cstdlib>
#include <iostream>

#include "linereader.h"

linereader::linereader(void)
{
}

linereader::~linereader(void)
{
}

bool
linereader::readline(std::string &r, std::string const &prompt)
{
	char *s = ::readline(form_prompt(prompt).c_str());
	if (s == NULL)
		return false;
	r = s;
	if (*s)
		add_history(s);
	std::free(s);
	return true;
}

std::string
linereader::form_prompt(std::string const &src)
{
	std::string::size_type i, last = 0;
	std::string pr;

	while ((i = src.find('$', last)) != std::string::npos) {
		pr.append(src.substr(last, i - last));
		if (i == src.size()-1)
			return pr + "$";
		switch (src[i + 1]) {
		case '$':
			pr += '$';
			last = i + 2;
			continue;
		case '(':
			break;
		default:
			return src;
		}
		if ((last = src.find(')', i)) == std::string::npos)
			return src;
		std::string key = src.substr(i + 2, last - i - 2);
		std::string value;
		std::map<std::string, std::string>::iterator it;
		if ((it = promptvars.find(key)) != promptvars.end())
			value = it->second;
		pr += value;
		last++;
	}
	pr += src.substr(last);
	return pr;
}

void
linereader::set_prompt_variable(std::string const &var, std::string const &key)
{
	promptvars[var] = key;
}
