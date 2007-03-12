#include <stdio.h>
#include <readline/readline.h>
#include <readline/history.h>

#include <cstdlib>

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
	char *s = ::readline(prompt.c_str());
	if (s == NULL)
		return false;
	r = s;
	if (*s)
		add_history(s);
	std::free(s);
	return true;
}
