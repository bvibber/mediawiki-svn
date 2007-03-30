#ifndef TERMINAL_H
#define TERMINAL_H

#include <string>
#include <map>

#include <termio.h>

struct terminal {
	terminal();
	~terminal();

	bool readline(std::string &, std::string const &);
	void set_prompt_variable(std::string const &var, std::string const &value);
	void putline(std::string const &line);
	void reset_pager(void);
	bool rawread(char &c);

	int rows() const;
	int cols() const;

private:
	std::string form_prompt(std::string const &);
	void really_put_line(std::string const &line);

	std::map<std::string, std::string> promptvars;
	int _rows, _cols;
	int rows_output;
	struct termios norm, raw;
};

#endif
