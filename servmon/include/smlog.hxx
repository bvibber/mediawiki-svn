/* @(#) $Header$ */
#ifndef SM_SMLOG_HXX_INCLUDED_
#define SM_SMLOG_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smlog {

enum dbg_t {
	mysql_connect,
	mysql_query,
	mysql_monitoring,
	irc,
};
	
class log : public smutl::singleton<log> {
public:
	void logmsg(int ircvl, str message);
	void debug(dbg_t func, str message);

	bool debugset(dbg_t f);
	void dodebug(dbg_t f);
	void dontdebug(dbg_t f);
	
private:
	std::string timestamp(void);

	std::set<dbg_t> debugs;
};
} // namespace smlog

#endif
