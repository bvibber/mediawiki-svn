/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smlog.hxx"
#include "smirc.hxx"

namespace smlog {

void
log::logmsg(int irclvl, str message)
{
	std::string fmt = b::io::str(b::format("%% %s -- %s") % timestamp() % message);
	std::cout << fmt << '\n';

	if (irclvl)
		SMI(smirc::cfg)->conn()->msg(irclvl, fmt);
}

void
log::debug(dbg_t func, str message)
{
	if (!debugset(func)) return;
	logmsg(0, message);
}
	
std::string
log::timestamp(void)
{
	char buf[256];
	struct tm now;
	std::time_t nowt = std::time(0);
	gmtime_r(&nowt, &now);
	strftime(buf, sizeof buf, "%d-%b-%Y %H:%M:%S", &now);
	return buf;
}

bool
log::debugset(dbg_t f)
{
	return debugs.find(f) != debugs.end();
}

void
log::dodebug(dbg_t f)
{
	debugs.insert(f);
}

void
log::dontdebug(dbg_t f)
{
	debugs.erase(f);
}

} // namespace smlog
