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

} // namespace smutl
