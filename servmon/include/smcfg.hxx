#ifndef SM_CFG_HXX_INCLUDED_
#define SM_CFG_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smcfg {

struct nokey : public std::runtime_error {
	nokey() : std::runtime_error("configuration option invalid") {}
};

class cfg : public smutl::singleton<cfg> {
public:
	cfg();

	std::string const& fetchstr(std::string const& key);
	int fetchint(std::string const& key);
	bool fetchbool(std::string const& key);
	void storestr(std::string const& key, std::string const& value);
	void storeint(std::string const& key, int value);
	void storebool(std::string const& key, bool value);

private:
	std::map<std::string, std::string> strvals;
	std::map<std::string, int> intvals;
	std::map<std::string, bool> boolvals;

	void wrcfg(void);
};

} // namespace smcfg

#endif
