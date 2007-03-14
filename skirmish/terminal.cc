#include <stdio.h>
#include <unistd.h>
#include <readline/readline.h>
#include <readline/history.h>

#include <cstdlib>
#include <iostream>
#include <sstream>

#include <sys/types.h>
#include <sys/ioctl.h>
#include <termio.h>

#include "terminal.h"

terminal::terminal(void)
	: rows_output(0)
	, rows(0)
	, cols(0)
{
	if (isatty(STDOUT_FILENO)) {
		struct winsize wz;
		ioctl(STDOUT_FILENO, TIOCGWINSZ, &wz);
		rows = wz.ws_row;
		cols = wz.ws_col;
	}

	tcgetattr(0, &norm);
	std::memcpy(&raw, &norm, sizeof(norm));
	cfmakeraw(&raw);
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

	std::istringstream strm(line);
	std::string rest;
	while (std::getline(strm, rest)) {
		while (rest.size() > cols) {
			really_put_line(rest.substr(0, cols));
			rest = rest.substr(cols);
		}
		really_put_line(rest);
	}
}

void
terminal::really_put_line(std::string const &line)
{
	if (rows - 1 == rows_output) {
		rows_output = 0;
		std::cout << "-- More --" << std::flush;
		char c;
		if (!rawread(c))
			return;
		switch (c) {
		case ' ':
			rows_output = 0;
			break;
		default:
		case '\n':
			rows_output = rows - 2;
			break;
		}
		std::cout << '\r';
	}
	std::cout << line << '\n';
	++rows_output;
}

void
terminal::reset_pager(void)
{
	rows_output = 0;
}

bool
terminal::rawread(char &c)
{
	tcsetattr(0, TCSANOW, &raw);
	if (read(0, &c, 1) < 1) {
		tcsetattr(0, TCSANOW, &norm);
		return false;
	}
	tcsetattr(0, TCSANOW, &norm);
	return true;
}
