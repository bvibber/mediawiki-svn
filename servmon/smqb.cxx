/* @(#) $Header$ */
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

rule
cfg::getrule(str name)
{
	if (!rule_exists(name)) throw norule();
	rule r;
	r.name = name;
	try {
		r.description = SMI(smcfg::cfg)->fetchstr("/qb/rule/"+name+"/description");
	} catch (smcfg::nokey&) {}
	// minthreads minlastthreads lowestpos minruntime users command query enabled
	r.minthreads = r.minlastthreads = r.minruntime = r.lowestpos = 0;
	r.enabled = false;
#define ASI(x,v) try { r.x = SMI(smcfg::cfg)->fetchint("/qb/rule/"+name+"/" #v); } catch (smcfg::nokey&) {}
#define ASS(x,v) try { r.x = SMI(smcfg::cfg)->fetchstr("/qb/rule/"+name+"/" #v); } catch (smcfg::nokey&) {}
	ASI(minthreads, minthreads);
	ASI(minlastthreads, minlastthreads);
	ASI(lowestpos, lowestpos);
	ASI(minruntime, minruntime);
	ASS(cmdtype, command);
	ASS(query, query);
	ASI(enabled, enabled);
#undef ASI
#undef ASS
	try {
		r.users = SMI(smcfg::cfg)->fetchlist("/qb/rule/"+name+"/users");
	} catch (smcfg::nokey&) {}
	return r;
}

std::vector<rule>
cfg::getrules(void)
{
	std::vector<rule> res;
	try {
		std::set<std::string> rulenames = SMI(smcfg::cfg)->fetchlist("/qb/rules");
		FE_TC_AS(std::set<std::string>, rulenames, i) {
			res.push_back(getrule(*i));
		}
	} catch (smcfg::nokey&) {}
	return res;
}

void
cfg::set_minthreads(str rule, int val)
{
	SMI(smcfg::cfg)->storeint("/qb/rule/"+rule+"/minthreads", val);
}

void
cfg::set_minlastthreads(str rule, int val)
{
	SMI(smcfg::cfg)->storeint("/qb/rule/"+rule+"/minlastthreads", val);
}

void
cfg::set_lowestpos(str rule, int val)
{
	SMI(smcfg::cfg)->storeint("/qb/rule/"+rule+"/lowestpos", val);
}

void
cfg::set_minruntime(str rule, int val)
{
	SMI(smcfg::cfg)->storeint("/qb/rule/"+rule+"/minruntime", val);
}

void
cfg::set_user(str rule, str val)
{
	SMI(smcfg::cfg)->addlist("/qb/rule/"+rule+"/users", val);
}

void
cfg::set_command(str rule, str val)
{
	SMI(smcfg::cfg)->storestr("/qb/rule/"+rule+"/command", val);
}

void
cfg::set_querystring(str rule, str val)
{
	SMI(smcfg::cfg)->storestr("/qb/rule/"+rule+"/query", val);
}

void
cfg::set_enabled(str rule)
{
	SMI(smcfg::cfg)->storeint("/qb/rule/"+rule+"/enabled", 1);
}

void
cfg::set_disabled(str rule)
{
	SMI(smcfg::cfg)->storeint("/qb/rule/"+rule+"/enabled", 0);
}
	
} // namespace smqb
	
