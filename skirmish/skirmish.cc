#include <iostream>
#include <iomanip>
#include <map>
#include <boost/format.hpp>
#include <boost/assign/list_of.hpp>
#include <boost/function.hpp>
#include <boost/lexical_cast.hpp>

#include "linereader.h"
#include "db.h"

static db::connectionptr open_connection(std::string const &);
static void add_connection(std::string const &);
static void list_connections(std::string const &);
static void switch_connection(std::string const &);
static void close_connection(std::string const &);
static void show_connection();
static void handle_internal(std::string const &);

struct conndesc {
	db::connectionptr conn;
	std::string desc;
};

static std::vector<conndesc *> conns;
static int cnr = -1;

std::map<std::string, boost::function<void (std::string const &)> >
	commands = boost::assign::map_list_of
		("\\open",	add_connection)
		("\\ls",	list_connections)
		("\\sw",	switch_connection)
		("\\close",	close_connection)
	;

static db::connectionptr
open_connection(std::string const &where)
{
	db::connectionptr conn;
	conn = db::connection::create(where);
	conn->open();
	return conn;
}

static void
add_connection(std::string const &where)
{
	try {
		conndesc cd;
		cd.desc = where;
		cd.conn = open_connection(where);
		conns.push_back(new conndesc(cd));
		cnr = conns.size() - 1;
	} catch (db::error &e) {
		std::cerr << boost::format("skirmish: cannot connect to \"%s\": %s\n")
				% where % e.what();
	}
	show_connection();
}

int
main(int argc, char *argv[])
{
	linereader rl;
	for (int a = 1; argv[a]; ++a)
		add_connection(argv[a]);

	db::connectionptr conn;
	std::string input, prompt;
	for (;;) {
		if (cnr == -1)
			prompt = "skirmish (none)>";
		else
			prompt = str(boost::format("skirmish [%d]>") % cnr);

		if (!rl.readline(input, prompt))
			break;

		if (input.empty())
			continue;

		if (input[0] == '\\') {
			handle_internal(input);
			continue;
		}

		if (cnr == -1) {
			std::cerr << "No connection.\n";
			continue;
		}

		conn = conns[cnr]->conn;

		db::execution_result *res;
		int nrows = 0;

		if (input[input.size() - 1] == ';')
			input.resize(input.size() - 1);

		try {
			res = conn->execute_sql(input);
		} catch (db::error &e) {
			std::cerr << boost::format("Error: %s\n") % e.what();
			continue;
		}

		if (res->has_data()) {
			db::result_row *r;
			int ncols = res->num_fields();
			std::vector<int> sizes(ncols);
			std::vector<std::vector<std::string> > data;

			std::vector<std::string> names;
			for (int i = 0; i < ncols; ++i)
				names.push_back(res->field_name(i));
			data.push_back(names);

			while (r = res->next_row()) {
				std::vector<std::string> thisrow;
				for (int i = 0; i < ncols; ++i) {
					std::string v = r->string_value(i);
					thisrow.push_back(v);
				}
				data.push_back(thisrow);
				delete r;
				++nrows;
			}

			for (int row = 0; row < data.size(); ++row) {
				for (int col = 0; col < ncols; ++col) {
					if (data[row][col].size() > sizes[col])
						sizes[col] = data[row][col].size();
				}
			}

			for (int row = 0; row < data.size(); ++row) {
				for (int col = 0; col < ncols; ++col) {
					std::cout << ' ' << std::setw(sizes[col]) << std::left << data[row][col];
					if (col != (res->num_fields() - 1))
						std::cout << " |";
				}
				std::cout << '\n';

				if (row == 0) {
					for (int col = 0; col < ncols; ++col) {
						std::cout << std::string(sizes[col] + 2, '-');
						if (col == ncols-1)
							std::cout << '-';
						else
							std::cout << '+';
					}
					std::cout << '\n';
				}
			}
		}

		if (nrows)
			std::cout << boost::format("\nOK (%d rows).\n") % nrows;
		delete res;
	}
} 

static void
handle_internal(std::string const &s)
{
	std::string command, arg;
	std::string::size_type i;

	if ((i = s.find(' ')) != std::string::npos) {
		command = s.substr(0, i);
		arg = s.substr(i + 1);
	} else {
		command = s;
	}

	std::map<std::string, boost::function<void (std::string const &)> >::iterator it;
	if ((it = commands.find(command)) == commands.end()) {
		std::cerr << boost::format("Error: unknown command \"%s\"\n") % command;
		return;
	}

	it->second(arg);
}

static void
list_connections(std::string const &)
{
	for (int i = 0; i < conns.size(); ++i) {
		if (!conns[i])
			continue;
		std::cout << boost::format("%- 2d\t%s\n") % i % conns[i]->desc;
	}
}

static void
switch_connection(std::string const &arg)
{
	int to;
	try {
		to = boost::lexical_cast<int>(arg);
	} catch (boost::bad_lexical_cast &) {
		std::cerr << boost::format("Error: invalid connection number \"%s\".\n") % arg;
		return;
	}

	if (to > conns.size() - 1 || to < 0 || !conns[to]) {
		std::cerr << boost::format("Error: no connection %d\n") % to;
		return;
	}

	cnr = to;
	show_connection();
}

static void
close_connection(std::string const &arg)
{	
	int to;

	if (!arg.empty()) {
		try {
			to = boost::lexical_cast<int>(arg);
		} catch (boost::bad_lexical_cast &) {
			std::cerr << boost::format("Error: invalid connection number \"%s\".\n") % arg;
			return;
		}

		if (to > conns.size() - 1 || to < 0 || !conns[to]) {
			std::cerr << boost::format("Error: no connection %d\n") % to;
			return;
		}
	} else {
		if (cnr == -1) {
			std::cout << "Error: no connection.\n";
			return;
		}
		to = cnr;
	}

	conns[to]->conn->close();
	delete conns[to];
	conns[to] = 0;

	if (to == cnr) {
		cnr = -1;
		std::cout << "[not connected]\n";
	}
}

static void
show_connection(void)
{
	if (cnr == -1) {
		std::cout << "[not connected]\n";
		return;
	}

	std::cout << boost::format("[connected to %s]\n") % conns[cnr]->desc;
}
