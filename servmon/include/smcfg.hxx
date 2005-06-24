/* @(#) $Header$ */
#ifndef SM_CFG_HXX_INCLUDED_
#define SM_CFG_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smcfg {

struct nokey : public std::runtime_error {
	nokey() : std::runtime_error("configuration option invalid") {}
};

struct wrerr : std::runtime_error {
	wrerr() : std::runtime_error("short write in config file") {}
};

class cfg : public smutl::singleton<cfg> {
public:
	cfg();

	void write(bool startup = true);
	
	std::string const& fetchstr(std::string const& key);
	int fetchint(std::string const& key);
	int fetchint(std::string const& key, int);
	bool fetchbool(std::string const& key);
	std::set<std::string> const& fetchlist(std::string const& key);
	bool listhas(std::string const& list, std::string const& value);

	void storestr(std::string const& key, std::string const& value);
        void remstr(str key);
	void storeint(std::string const& key, int value);
	void storebool(std::string const& key, bool value);
	void addlist(std::string const& list, std::string const& value);
	void dellist(std::string const& list, std::string const& value);

private:
	std::map<std::string, std::string> strvals;
	std::map<std::string, int> intvals;
	std::map<std::string, bool> boolvals;
	std::map<std::string, std::set<std::string> > listvals;
};

} // namespace smcfg

#endif
