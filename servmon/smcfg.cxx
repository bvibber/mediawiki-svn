#include "smstdinc.hxx"
#include "smcfg.hxx"

namespace smcfg {

namespace {
std::string cfgfile = PFX "/servmon.cfg";
u_char const tystr = 0;
u_char const tyint = 1;
u_char const tybool = 2;
u_char const tylist = 3;
#define flthr(x,e) if ((x) < 0) throw e();

struct shrtrd : std::runtime_error {
	shrtrd() : std::runtime_error("short read in config file") {}
};

struct shrtwr : std::runtime_error {
	shrtwr() : std::runtime_error("short write in config file") {}
};

void wrtchar(int f, char c) {
	std::cerr << "writing a char: "<<int(c)<<"\n";
	flthr(write(f, &c, 1), shrtwr);
}

void wrtint(int f, uint32_t i) {
	std::cerr << "writing a int: "<<i<<"\n";
	flthr(write(f, (char *)&i, sizeof i), shrtwr);
}

void wrtstr(int f, str s) {
	wrtint(f, s.size());
	std::cerr<<"writing a str: ["<<s<<"]\n";
	flthr(write(f, s.data(), s.size()), shrtwr);
}

void wrtbool(int f, bool b) {
	std::cerr<<"writing a bool: "<<b<<"\n";
	uint32_t i = b;
	flthr(write(f, (char *)&i, sizeof i), shrtwr);
}

uint32_t
rdint(std::istream& f)
{
	uint32_t i;
	if (!f.read((char *)&i, sizeof i))
		throw shrtrd();
	return i;
}

std::string
rdstr(std::istream& f)
{
	uint32_t len = rdint(f);
	std::vector<char> b(len);
	if (!f.read(&b[0], len))
		throw shrtrd();
	return std::string(b.begin(), b.end());
}

char
rdchar(std::istream& f)
{
	char c;
	if (!f.read((char *)&c, sizeof c))
		throw shrtrd();
	return c;
}
#define rdbool rdchar

#undef flthr

} // anon namespace

cfg::cfg(void) 
try {
	std::ifstream f(cfgfile.c_str());
	if (!f) {
		std::cerr << "warning: can't open config file " <<
			cfgfile << ": " << std::strerror(errno) << "\n";
		return;
	}
	std::cerr << "reading cfg\n";
	for (;;) {
		char type;
		try {
			type = rdchar(f);
		} catch (shrtrd&) {
			return;
		}
		std::string key = rdstr(f);
		std::cerr << "key: "<<key<<"\n";
		
		switch (type) {
		case tystr: {
			std::cerr << "str\n";
			strvals[key] = rdstr(f);
			break;
		}
		case tyint: {
			std::cerr << "int\n";
			intvals[key] = rdint(f);
			break;
		}
		case tybool: {
			std::cerr << "bool\n";
			boolvals[key] = rdbool(f);
			break;
		}
		case tylist: {
			std::cerr << "list\n";
			uint32_t len = rdint(f);
			std::cerr << "with "<<len<<"elements\n";
			while (len--) {
				std::string e = rdstr(f);
				std::cerr << "\telement: "<<e<<"\n";
				listvals[key].insert(e);
			}
			break;
		}
		}
	}
} catch (shrtrd&) {
	std::cerr << "short read in config file\n";
	std::exit(1);
}

void 
cfg::wrcfg(void)
try {
	std::cerr << "writing cfg\n";
	int f = open(cfgfile.c_str(), O_CREAT|O_NOFOLLOW|O_TRUNC|O_WRONLY,
				S_IRUSR|S_IWUSR);
	if (f < 0) {
		std::cerr << "warning: could not write config file: " <<
			std::strerror(errno) << "\n";
		return;
	}
	for(std::map<std::string,std::string>::const_iterator it = strvals.begin(),
			end = strvals.end(); it != end; ++it)
	{
		std::cerr << "str: ["<<it->first<<"]=["<<it->second<<"]\n";
		wrtchar(f, tystr);
		wrtstr(f, it->first);
		wrtstr(f, it->second);
	}
	for(std::map<std::string,int>::const_iterator it = intvals.begin(),
			end = intvals.end(); it != end; ++it)
	{
		std::cerr << "int: ["<<it->first<<"]=["<<it->second<<"]\n";
		wrtchar(f, tyint);
		wrtstr(f, it->first);
		wrtint(f, it->second);
	}
	for(std::map<std::string,bool>::const_iterator it = boolvals.begin(),
			end = boolvals.end(); it != end; ++it)
	{
		std::cerr << "bool: ["<<it->first<<"]=["<<it->second<<"]\n";
		wrtchar(f, tybool);
		wrtstr(f, it->first);
		wrtbool(f, it->second);
	}
	for (std::map<std::string, std::set<std::string> >::const_iterator it = listvals.begin(),
			end = listvals.end(); it != end; ++it)
	{
		std::cerr << "list: ["<<it->first<<"]\n";
		wrtchar(f, tylist);
		wrtstr(f, it->first);
		wrtint(f, it->second.size());
		for (std::set<std::string>::const_iterator jt = it->second.begin(),
				jend = it->second.end(); jt != jend; ++jt)
		{
			std::cerr << "list item: ["<<*jt<<"]\n";
			wrtstr(f, *jt);
		}
	}

	close(f);
} catch (shrtwr&) {
	std::cerr << "short write in configuration file\n";
}


void cfg::storestr(std::string const& key, std::string const& value)
{
	strvals[key] = value;
	std::cerr << "storestr: ["<<key<<"]=["<<value<<"]\n";
	wrcfg();
}
void cfg::storeint(std::string const& key, int value)
{
	intvals[key] = value;
	std::cerr << "storeint: ["<<key<<"]=["<<value<<"]\n";
	wrcfg();
}
void cfg::storebool(std::string const& key, bool value)
{
	boolvals[key] = value;
	std::cerr << "storebool: ["<<key<<"]=["<<value<<"]\n";
	wrcfg();
}

std::string const& cfg::fetchstr(std::string const& key)
{
	if (strvals.find(key) == strvals.end())
		throw nokey();
	return strvals[key];
}

int cfg::fetchint(std::string const& key)
{
	if (intvals.find(key) == intvals.end())
		throw nokey();
	return intvals[key];
}

bool cfg::fetchbool(std::string const& key)
{
	if (boolvals.find(key) == boolvals.end())
		throw nokey();
	return boolvals[key];
}

std::set<std::string> const& cfg::fetchlist(std::string const& key)
{
	if (listvals.find(key) == listvals.end())
		throw nokey();
	return listvals[key];
}

bool cfg::listhas(std::string const& list, std::string const& value)
{
	try {
		std::set<std::string> const& l = fetchlist(list);
		return !(l.find(value) == l.end());
	} catch (nokey&) {
		return false;
	}
}

void cfg::addlist(std::string const& list, std::string const& value)
{
	listvals[list].insert(value);
	wrcfg();
}

void cfg::dellist(std::string const& list, std::string const& value)
{
	std::map<std::string, std::set<std::string> >::iterator lt = listvals.find(list);
	if (lt == listvals.end())
		throw nokey();
	std::set<std::string>& l = lt->second;
	l.erase(value);
	wrcfg();
}

} // namespace smcfg
