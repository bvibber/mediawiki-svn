#include <iostream>
#include <fstream>
#include <cerrno>

#include <boost/format.hpp>

#include <sys/types.h>
#include <dirent.h>

#define HELPDIR "help"

namespace {

/*
 * Read text from <in> and output it to <out> as a C++ string called <name>.
 */
void
file_to_cxx(std::string const &name, std::istream &in, std::ostream &out)
{
	out << "char const *" << name << " = \"";

	char c;
	while (in.get(c)) {
		switch (c) {
		case '\n':
			out << "\\n";
			break;
		case '\\':
		case '"':
			out << '\\' << c;
			break;
		default:
			out << c;
		}
	}

	out << "\";\n";
}

} // anonymous namespace

int
main(int argc, char **argv)
{
	std::ofstream out("help_text.cc");
	if (!out) {
		std::cerr << boost::format("cannot open help.cc for writing: %s\n")
				% std::strerror(errno);
		return 1;
	}
	out <<
"/* This is an automatically generated file.  Do not edit. */\n"
"#include <map>\n"
"#include <string>\n"
"#include <boost/assign/list_of.hpp>\n"
"#include \"help.h\"\n"
;

	std::cout << "processing help files...\n";
	
	std::vector<std::string> helptopics;

	out << "namespace {\n";

	DIR *d;
	if ((d = opendir(HELPDIR)) == 0) {
		std::cerr << boost::format("cannot open %s: %s\n")
				% HELPDIR % std::strerror(errno);
		return 1;
	}

	dirent *de;
	while (de = readdir(d)) {
		if (de->d_name[0] == '.')
			continue;

		std::cout << '\t' << de->d_name << '\n';
		std::string file = str(boost::format("%s/%s") % HELPDIR % de->d_name);

		std::ifstream f(file.c_str());
		if (!f) {
			std::cerr << boost::format("cannot open %s for reading: %s\n")
					% std::strerror(errno);
			return 1;
		}

		file_to_cxx(str(boost::format("help_%s") % de->d_name), f, out);
		helptopics.push_back(de->d_name);
	}
	closedir(d);

	out << 
"}\n"
"namespace help {\n"
"std::map<std::string, std::string> help_text = boost::assign::map_list_of\n"
;
	for (std::size_t i = 0, end = helptopics.size(); i < end; ++i) {
		out << boost::format("(\"%1%\", help_%1%)\n") % helptopics[i];
	}
	out <<
";\n"
"}\n"
;
}
