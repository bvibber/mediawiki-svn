/* @(#) $Header$ */
#ifndef SM_SMQB_HXX_INCLUDED_
#define SM_SMQB_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smcfg.hxx"

namespace smqb {

struct rule {
	std::string name;
	std::string description;
	int minthreads;
	int minlastthreads;
	int lowestpos;
	int minruntime;
	std::set<std::string> users;
	std::string cmdtype;
	std::string query;
	bool enabled;
};

struct norule : public std::runtime_error {
	norule() : std::runtime_error("rule does not exist") {}
};
	
class cfg : public smutl::singleton<cfg> {
public:
	bool rule_exists(str name);
	void create_rule(str name);
	void rule_description(str name, str desc);
	void delete_rule(str name);

	std::vector<rule> getrules(void);
	rule getrule(str name);

	// rule operations
	void set_minthreads(str, int);
	void set_minlastthreads(str, int);
	void set_lowestpos(str, int);
	void set_minruntime(str, int);
	void set_user(str, str);
	void set_command(str, str);
	void set_querystring(str, str);
	void set_enabled(str);
	void set_disabled(str);
private:
};

} // namespace smqb

#endif
