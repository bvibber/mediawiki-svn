/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smutl {

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

std::vector<std::string>
snarf(str cmd)
{
	std::vector<std::string> r;
	FILE *f = popen(cmd.c_str(), "r");
	char b[1024]; /* XXX */
	if (!f) return r;
	while (fgets(b, sizeof b, f)) {
		b[strlen(b) - 1] = '\0';
		r.push_back(b);
	}
	pclose(f);
	return r;
}

std::time_t
wf2time_t(str wf)
{
	struct tm t;
	/* 2005 03 07 03 59 50 */
	/* 0123 45 67 89 01 23 */
	/* yyyy mm dd hh mm ss */
	try {
		t.tm_sec = lexical_cast<int>(wf.substr(12, 2));
		t.tm_min = lexical_cast<int>(wf.substr(10, 2));
		t.tm_hour = lexical_cast<int>(wf.substr(8, 2));
		t.tm_mday = lexical_cast<int>(wf.substr(6, 2));
		t.tm_mon = lexical_cast<int>(wf.substr(4, 2));
		t.tm_year = lexical_cast<int>(wf.substr(0, 4)) - 1900;
	} catch (bad_lexical_cast&) {
		return 0;
	}
	t.tm_yday = t.tm_wday = t.tm_isdst = 0;
	return mktime(&t);
}
	
} // namespace smutl
