#include "smstdinc.hxx"
#include "smcfg.hxx"

namespace smcfg {

namespace {
std::string cfgfile = PFX "/servmon.cfg";
u_char const tystr = 0;
u_char const tyint = 1;
u_char const tybool = 2;
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
		}
	}
}

void cfg::wrcfg(void)
{
	std::ofstream f(cfgfile.c_str());
	if (!f) {
		std::cerr << "warning: could not write config file!\n";
		return;
	}
	for(std::map<std::string,std::string>::const_iterator it = strvals.begin(),
			end = strvals.end(); it != end; ++it)
	{
		f.write((char *)&tystr, sizeof tystr);
		uint32_t size = it->first.size();
		f.write((char *)&size, sizeof size);
		f.write(it->first.data(), it->first.size());
		size = it->second.size();
		f.write((char *)&size, sizeof size);
		f.write(it->second.data(), it->second.size());
	}
	for(std::map<std::string,int>::const_iterator it = intvals.begin(),
			end = intvals.end(); it != end; ++it)
	{
		f.write((char *)&tyint, sizeof tyint);
		uint32_t size = it->first.size();
		f.write((char *)&size, sizeof size);
		f.write(it->first.data(), it->first.size());
		f.write((char *)&it->second, sizeof it->second);
	}
	for(std::map<std::string,int>::const_iterator it = intvals.begin(),
			end = intvals.end(); it != end; ++it)
	{
		f.write((char *)&tybool, sizeof tybool);
		uint32_t size = it->first.size();
		f.write((char *)&size, sizeof size);
		f.write(it->first.data(), it->first.size());
		u_char d = it->second;
		f.write((char *)&d, sizeof d);
	}
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

} // namespace smcfg
