/* $Header$ */
#include "smqb.hxx"

namespace smqb {

bool
cfg::rule_exists(str name)
{
	try {
		return SMI(smcfg::cfg)->listhas("/qb/rules", name);
	} catch (smcfg::nokey&) {
		return false;
	}
}

void
cfg::create_rule(str name)
{
	if (rule_exists(name)) return;
	SMI(smcfg::cfg)->addlist("/qb/rules", name);
}

void
cfg::rule_description(str name, str desc)
{
	if (!rule_exists(name)) return;
	SMI(smcfg::cfg)->storestr("/qb/rule/"+name+"/description", desc);
}

void
cfg::delete_rule(str name)
{
	if (!rule_exists(name)) return;
	SMI(smcfg::cfg)->dellist("/qb/rules", name);
}

} // namespace smqb
	
