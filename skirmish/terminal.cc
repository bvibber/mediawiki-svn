#include <stdio.h>
#include <readline/readline.h>
#include <readline/history.h>

#include <cstdlib>
#include <iostream>

#include <sys/types.h>
#include <sys/ioctl.h>
#include <termio.h>

#include "terminal.h"

terminal::terminal(void)
	: rows_output(0)
{
	struct winsize wz;
	ioctl(0, TIOCGWINSZ, &wz);
	rows = wz.ws_row;
	cols = wz.ws_col;
}

terminal::~terminal(void)
{
}

bool
terminal::readline(std::string &r, std::string const &prompt)
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
terminal::form_prompt(std::string const &src)
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
terminal::set_prompt_variable(std::string const &var, std::string const &key)
{
	promptvars[var] = key;
}

void
terminal::putline(std::string const &line)
{
	/*
	 * If we don't know the size of the terminal, just print it.
	 */
	if (rows == 0 || cols == 0) {
		std::cout << line << '\n';
		return;
	}

	std::string rest = line;
	while (rest.size() > cols) {
		really_put_line(rest.substr(0, cols));
		rest = rest.substr(cols);
	}
	really_put_line(rest);
}

void
terminal::really_put_line(std::string const &line)
{
	if (rows - 1 == rows_output) {
		rows_output = 0;
		std::cout << "-- More --";
		std::string dummy;
		std::getline(std::cin, dummy);
	}
	std::cout << line << '\n';
	++rows_output;
}
