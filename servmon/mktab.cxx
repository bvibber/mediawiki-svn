/* @(#) $Header$ */

#include <iostream>
#include <fstream>
#include <string>
#include <map>
#include <set>
#include <algorithm>
#include <iterator>

#include <cerrno>
#include <cstring>

#include <boost/format.hpp>
using boost::format;
using boost::io::str;

std::string car(std::string&);

int
main(int argc, char *argv[])
{
	if (argc != 2) {
		std::cerr << format("usage: %s <msgtab>\n") % argv[0];
		std::exit(8);
	}
	
	std::ifstream f(argv[1]);
	if (!f.good()) {
		std::cerr << format("could not open msgtab %s: %s\n") % argv[1] % std::strerror(errno);
		std::exit(8);
	}

	std::ofstream header("msgtab.hxx");
	if (!header.good()) {
		std::cerr << format("could not open header %s: %s\n") % "msgtab.hxx" % std::strerror(errno);
		std::exit(8);
	}
	std::ofstream source("msgtab.cxx");
	if (!source.good()) {
		std::cerr << format("could not open source %s: %s\n") % "msgtab.cxx" % std::strerror(errno);
		std::exit(8);
	}
	header <<
"#ifndef SM$MSGTAB\n"
"#define SM$MSGTAB\n"
"#define SM$LEV_ERROR 0\n"
"#define SM$LEV_WARN 1\n"
"#define SM$LEV_INFORM 2\n"
"struct sm$msgarg {\n"
"\tsm$msgarg(std::string const& v_) : v(v_) {}\n"
"\tsm$msgarg(void) {}\n"
"\tstd::string v;\n"		
"};\n"		
"std::string sm$getmsg(int fac, int msg, sm$msgarg = sm$msgarg(), sm$msgarg = sm$msgarg(), sm$msgarg = sm$msgarg());\n"		
		;
	source <<
"#include <string>\n"
"#include <ostream>\n"		
"#include <boost/format.hpp>\n"		
"#include \"msgtab.hxx\"\n"		
"std::ostream& operator<< (std::ostream& os, sm$msgarg const& m) {\n"
"\tos << m.v;\n"
"\treturn os;\n"		
"}\n"		
"struct sm$message {\n"
"\tint sev;\n"
"\tchar const *name;\n"		
"\tchar const *msg;\n"
"};\n"
"char const *sm$sev_names[] = {\n"
"\t\"E\", \"W\", \"I\"\n"
"};\n"		
		;
	std::string s, cmd, curfac;
	std::map<std::string, int> seenfac;
	std::set<std::string> seenmsg;
	std::vector<std::string> facidx, facnames;
	int facid = 0, msgid = 0;
	while (getline(f, s)) {
		if (s[0] == '#')
			continue;
		cmd = car(s);
		if (cmd == "FACILITY") {
			curfac = car(s);
			if (curfac.empty()) {
				std::cerr << "bad facility\n";
				std::exit(8);
			}
			if (seenfac.find(curfac) != seenfac.end()) {
				std::cerr << format("duplicate facility %s\n") % curfac;
				std::exit(8);
			}
			seenfac[curfac] = facid;
			header << format("#define SM$FAC_%s %d\n") % curfac % facid;
			if (msgid) {
				source << "};\n";
			}
			source << format("struct sm$message sm$msgs_%s[] = {\n") % curfac;
			facidx.push_back(str(format("\t/* %d SM$FAC_%s */ sm$msgs_%s,\n") % facid % curfac % curfac));
			facnames.push_back(str(format("\t/* %d */ \"%s\",\n") % facid % curfac));
			++facid;
			msgid = 0;
		} else if (cmd == "ERROR" || cmd == "WARN" || cmd == "INFORM") {
			std::string m = car(s);
			if (m.empty()) {
				std::cerr << "invalid message\n";
				std::exit(8);
			}
			if (seenmsg.find(s) != seenmsg.end()) {
				std::cerr << format("duplicate message %s\n") % m;
				std::exit(8);
			}
			seenmsg.insert(m);
			header << format("#define SM$MSG_%s %d\n") % m % msgid;
			source << format("\t/* %d SM$MSG_%s */ {SM$LEV_%s, \"%s\", \"%s\"},\n")
				% msgid % m % cmd % m % s;
			++msgid;
		} else {
			std::cerr << format("unknown command %s\n") % cmd;
			std::exit(8);
		}
	}
	source << "};\n";
	source << "struct sm$message *sm$fac_idx[] = {\n";
	std::copy(facidx.begin(), facidx.end(), std::ostream_iterator<std::string>(source));
	source << "};\n";
	source << "char const *sm$fac_names[] = {\n";
	std::copy(facnames.begin(), facnames.end(), std::ostream_iterator<std::string>(source));
	source << "};\n";
	
	source <<
		"std::string sm$getmsg(int fac, int msg, sm$msgarg a1, sm$msgarg a2, sm$msgarg a3) {\n"
		"\tboost::format f(sm$fac_idx[fac][msg].msg);\n"
		"\tf.exceptions(boost::io::no_error_bits);\n"
		"\tf % a1; f % a2; f % a3;\n"
		"\treturn boost::io::str(boost::format(\"%%%s-%s-%s, %s\") \n"
		"\t\t% sm$fac_names[fac]\n"
		"\t\t% sm$sev_names[sm$fac_idx[fac]->sev]\n"
		"\t\t% sm$fac_idx[fac][msg].name\n"
		"\t\t% f.str());\n"
		"}\n"
		;
	header << "#endif\n";
	
}

std::string
car(std::string& s)
{
	std::string::iterator it = std::find(s.begin(), s.end(), ' ');
	std::string t;
	if (it == s.end()) {
		t = s;
		s = "";
		return t;
	}

	t = std::string(s.begin(), it);
	s.erase(s.begin(), it + 1);
	return t;
}
