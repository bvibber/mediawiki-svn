/* $Header$ */
#ifndef SM_SMQB_HXX_INCLUDED_
#define SM_SMQB_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"
#include "smcfg.hxx"

namespace smqb {

class cfg : public smutl::singleton<cfg> {
public:
	bool rule_exists(str name);
	void create_rule(str name);
	void rule_description(str name, str desc);
	void delete_rule(str name);
private:
};

} // namespace smqb

#endif
