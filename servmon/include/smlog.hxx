/* @(#) $Header$ */
#ifndef SM_SMLOG_HXX_INCLUDED_
#define SM_SMLOG_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

#include "msgtab.hxx"

namespace smlog {

enum dbg_t {
	mysql_connect,
	mysql_query,
	mysql_monitoring,
	irc,
};
	
class log : public smutl::singleton<log> {
public:
	void initialise(void);
	
	void logmsg(int ircvl, int fac, int msg, sm$msgarg = sm$msgarg(), sm$msgarg = sm$msgarg(), sm$msgarg = sm$msgarg());
	void debug(dbg_t func, str message);

	bool debugset(dbg_t f);
	void dodebug(dbg_t f);
	void dontdebug(dbg_t f);
	
private:
	std::set<dbg_t> debugs;
};
} // namespace smlog

#endif
