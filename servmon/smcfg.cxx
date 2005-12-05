/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smcfg.hxx"
#include "smlog.hxx"

namespace smcfg {

namespace {
std::string cfgfile = PFX "/servmon.cfg";
std::string cfgtemp = cfgfile + ".tmp";
u_char const tystr = 0;
u_char const tyint = 1;
u_char const tybool = 2;
u_char const tylist = 3;
uint32_t const str_maxlen = 65535;

#define flthr(x,e) if ((x) < 0) throw e();

struct shrtrd : std::runtime_error {
	shrtrd() : std::runtime_error("short read in config file") {}
};

struct mlfrmcfg : std::runtime_error {
	mlfrmcfg(str error) : std::runtime_error("config file seems malformed: " + error) {}
	mlfrmcfg() : std::runtime_error("config file seems malformed: unspecified error") {}
};

void
wrtchar(int f, char c)
{
	flthr(write(f, &c, 1), wrerr);
}

void
wrtint(int f, uint32_t i)
{
	flthr(write(f, (char *)&i, sizeof i), wrerr);
}

void
wrtstr(int f, str s)
{
	wrtint(f, s.size());
	flthr(write(f, s.data(), s.size()), wrerr);
}

void
wrtbool(int f, bool b)
{
	uint32_t i = b;
	flthr(write(f, (char *)&i, sizeof i), wrerr);
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
	if (len > str_maxlen)
		throw mlfrmcfg(b::io::str(b::format("string seems too long (%d bytes)") % len));
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
#define rdbool rdint

#undef flthr

} // anon namespace

cfg::cfg(void) 
try {
	std::ifstream f(cfgfile.c_str());
	if (!f) {
		SMI(smlog::log)->logmsg(0, SM$FAC_CONF, SM$MSG_OPNFAIL, cfgfile, std::strerror(errno));
		return;
	}
	for (;;) {
		char type;
		try {
			type = rdchar(f);
		} catch (shrtrd&) {
			return;
		}
		std::string key = rdstr(f);
		
		switch (type) {
		case tystr: {
			strvals[key] = rdstr(f);
			break;
		}
		case tyint: {
			intvals[key] = rdint(f);
			break;
		}
		case tybool: {
			boolvals[key] = rdbool(f);
			break;
		}
		case tylist: {
			uint32_t len = rdint(f);
			while (len--) {
				std::string e = rdstr(f);
				listvals[key].insert(e);
			}
			break;
		}
		}
	}
} catch (shrtrd& e) {
	SMI(smlog::log)->logmsg(0, SM$FAC_CONF, SM$MSG_RDERR, e.what());
	std::exit(1);
} catch (mlfrmcfg& e) {
	SMI(smlog::log)->logmsg(0, SM$FAC_CONF, SM$MSG_RDERR, e.what());
	std::exit(1);
}

void 
cfg::write(bool startup)
try {
	int f = open(cfgtemp.c_str(), O_CREAT|O_NOFOLLOW|O_TRUNC|O_WRONLY,
				S_IRUSR|S_IWUSR);
	if (f < 0) {
		if (startup)
			SMI(smlog::log)->logmsg(0, SM$FAC_CONF, SM$MSG_OPNFAIL, cfgtemp, std::strerror(errno));
		else
			throw wrerr();
		return;
	}
	for(std::map<std::string,std::string>::const_iterator it = strvals.begin(),
			end = strvals.end(); it != end; ++it)
	{
		wrtchar(f, tystr);
		wrtstr(f, it->first);
		wrtstr(f, it->second);
	}
	for(std::map<std::string,int>::const_iterator it = intvals.begin(),
			end = intvals.end(); it != end; ++it)
	{
		wrtchar(f, tyint);
		wrtstr(f, it->first);
		wrtint(f, it->second);
	}
	for(std::map<std::string,bool>::const_iterator it = boolvals.begin(),
			end = boolvals.end(); it != end; ++it)
	{
		wrtchar(f, tybool);
		wrtstr(f, it->first);
		wrtbool(f, it->second);
	}
	for (std::map<std::string, std::set<std::string> >::const_iterator it = listvals.begin(),
			end = listvals.end(); it != end; ++it)
	{
		wrtchar(f, tylist);
		wrtstr(f, it->first);
		wrtint(f, it->second.size());
		for (std::set<std::string>::const_iterator jt = it->second.begin(),
				jend = it->second.end(); jt != jend; ++jt)
		{
			wrtstr(f, *jt);
		}
	}

	close(f);
	if (std::remove(cfgfile.c_str()) < 0) {
		SMI(smlog::log)->logmsg(0, SM$FAC_CONF, SM$MSG_RMVFAIL, std::strerror(errno));
	}
	if (std::rename(cfgtemp.c_str(), cfgfile.c_str()) < 0) {
		SMI(smlog::log)->logmsg(0, SM$FAC_CONF, SM$MSG_MOVEFAIL, std::strerror(errno));
		return;
	}
} catch (wrerr& e) {
	SMI(smlog::log)->logmsg(0, SM$FAC_CONF, SM$MSG_WRTERR, e.what());
	if (!startup)
		throw;
}

void
cfg::storestr(std::string const& key, std::string const& value)
{
	strvals[key] = value;
}

void
cfg::storeint(std::string const& key, int value)
{
	intvals[key] = value;
}
void
cfg::storebool(std::string const& key, bool value)
{
	boolvals[key] = value;
}

std::string const&
cfg::fetchstr(std::string const& key)
{
	if (strvals.find(key) == strvals.end())
		throw nokey();
	return strvals[key];
}

int
cfg::fetchint(std::string const& key)
{
	if (intvals.find(key) == intvals.end())
		throw nokey();
	return intvals[key];
}

int
cfg::fetchint(std::string const& key, int deflt)
{
	if (intvals.find(key) == intvals.end())
		return deflt;
	return intvals[key];
}

bool
cfg::fetchbool(std::string const& key)
{
	if (boolvals.find(key) == boolvals.end())
		throw nokey();
	return boolvals[key];
}

std::set<std::string> const&
cfg::fetchlist(std::string const& key)
{
	if (listvals.find(key) == listvals.end())
		throw nokey();
	return listvals[key];
}

bool
cfg::listhas(std::string const& list, std::string const& value)
{
	try {
		std::set<std::string> const& l = fetchlist(list);
		return !(l.find(value) == l.end());
	} catch (nokey&) {
		return false;
	}
}

void
cfg::addlist(std::string const& list, std::string const& value)
{
	listvals[list].insert(value);
}

void
cfg::dellist(std::string const& list, std::string const& value)
{
	std::map<std::string, std::set<std::string> >::iterator lt = listvals.find(list);
	if (lt == listvals.end())
		throw nokey();
	std::set<std::string>& l = lt->second;
	l.erase(value);
}

} // namespace smcfg
