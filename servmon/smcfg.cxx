#include "smstdinc.hxx"
#include "smcfg.hxx"

namespace smcfg {

namespace {
std::string cfgfile = PFX "/servmon.cfg";
u_char const tystr = 0;
u_char const tyint = 1;
u_char const tybool = 2;
u_char const tylist = 3;
} // anon namespace

cfg::cfg(void) {
	std::ifstream f(cfgfile.c_str());
	if (!f) {
		std::cerr << "warning: can't open config file " <<
			cfgfile << ": " << std::strerror(errno) << "\n";
		return;
	}
	for (;;) {
		char type;
		if (!f.get(type))
			return;
		std::string key;
		uint32_t keylen;
		if (!f.read((char *)&keylen, sizeof keylen)) {
			std::cerr << "warning: short read in config file\n";
			return;
		}
		std::vector<char> b(keylen);
		if (!f.read(&b[0], keylen)) {
			std::cerr << "warning: short read in config file\n";
			return;
		}
		key.assign(b.begin(), b.end());

		switch (type) {
		case tystr: {
			uint32_t len;
			if (!f.read((char *)&len, sizeof len)) {
				std::cerr << "warning: short read in config file\n";
				return;
			}
			b.resize(len);
			if (!f.read(&b[0], len)) {
				std::cerr << "warning: short read in config file\n";
				return;
			}
			std::string value;
			value.assign(b.begin(), b.end());
			strvals[key] = value;
			break;
		}
		case tyint: {
			uint32_t value;
			if (!f.read((char *)&value, sizeof value)) {
				std::cerr << "warning: short read in config file\n";
				return;
			}
			intvals[key] = value;
			break;
		}
		case tybool: {
			u_char value;
			if (!f.read((char *)&value, sizeof value)) {
				std::cerr << "warning: short read in config file\n";
				return;
			}
			boolvals[key] = value;
			break;
		}
		case tylist: {
			uint32_t len;
			if (!f.read((char *)&len, sizeof len)) {
				std::cerr << "warning: short read in config file\n";
				return;
			}
			while (len--) {
				uint32_t slen;
				if (!f.read((char *)&slen, sizeof slen)) {
					std::cerr << "warning: short read in config file\n";
					return;
				}
				b.resize(slen);
				if (!f.read((char *)&b[0], slen)) {
					std::cerr << "warning: short read in config file\n";
					return;
				}
				listvals[key].insert(std::string(b.begin(), b.end()));
			}
			break;
		}
		}
	}
}

void cfg::wrcfg(void)
{
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
		write(f, (char *)&tystr, sizeof tystr);
		uint32_t size = it->first.size();
		write(f, (char *)&size, sizeof size);
		write(f, it->first.data(), it->first.size());
		size = it->second.size();
		write(f, (char *)&size, sizeof size);
		write(f, it->second.data(), it->second.size());
	}
	for(std::map<std::string,int>::const_iterator it = intvals.begin(),
			end = intvals.end(); it != end; ++it)
	{
		write(f, (char *)&tyint, sizeof tyint);
		uint32_t size = it->first.size();
		write(f, (char *)&size, sizeof size);
		write(f, it->first.data(), it->first.size());
		write(f, (char *)&it->second, sizeof it->second);
	}
	for(std::map<std::string,int>::const_iterator it = intvals.begin(),
			end = intvals.end(); it != end; ++it)
	{
		write(f, (char *)&tybool, sizeof tybool);
		uint32_t size = it->first.size();
		write(f, (char *)&size, sizeof size);
		write(f, it->first.data(), it->first.size());
		u_char d = it->second;
		write(f, (char *)&d, sizeof d);
	}
	for (std::map<std::string, std::set<std::string> >::const_iterator it = listvals.begin(),
			end = listvals.end(); it != end; ++it)
	{
		write(f, (char *)&tylist, sizeof tylist);
		uint32_t size = it->first.size();
		write(f, (char *)&size, sizeof size);
		write(f, it->first.data(), it->first.size());
		size = it->second.size();
		write(f, (char *)&size, sizeof size);
		for (std::set<std::string>::const_iterator jt = it->second.begin(),
				jend = it->second.end(); jt != jend; ++jt)
		{
			size = jt->size();
			write(f, (char *)&size, sizeof size);
			write(f, (char *)jt->data(), jt->size());
		}
	}

	close(f);
}

void cfg::storestr(std::string const& key, std::string const& value)
{
	strvals[key] = value;
	wrcfg();
}
void cfg::storeint(std::string const& key, int value)
{
	intvals[key] = value;
	wrcfg();
}
void cfg::storebool(std::string const& key, bool value)
{
	boolvals[key] = value;
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
