#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smutl {

std::string car(std::string& s)
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

} // namespace smutl
