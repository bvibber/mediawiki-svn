/* @(#) $Header$ */
#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smutl {

std::string
car(std::string& s)
{
	std::string::iterator it = std::find(s.begin(), s.end(), ' ');
	std::string t;
	if (it == s.end()) {
		t = s;
		s = "";
		return t;
	}
 
	t = std::string(s.begin(), it);
	s.erase(s.begin(), it + 1);
	return t;
}

std::string
lower(std::string s) {
	std::transform(s.begin(), s.end(), s.begin(), std::ptr_fun<int, int>(std::tolower));
	return s;
}

std::vector<std::string>
snarf(str cmd)
{
	std::vector<std::string> r;
	FILE *f = popen(cmd.c_str(), "r");
	char b[1024]; /* XXX */
	if (!f) return r;
	while (fgets(b, sizeof b, f)) {
		b[strlen(b) - 1] = '\0';
		r.push_back(b);
	}
	pclose(f);
	return r;
}

std::time_t
wf2time_t(str wf)
{
	struct tm t;
	/* 2005 03 07 03 59 50 */
	/* 0123 45 67 89 01 23 */
	/* yyyy mm dd hh mm ss */
	try {
		t.tm_sec = lexical_cast<int>(wf.substr(12, 2));
		t.tm_min = lexical_cast<int>(wf.substr(10, 2));
		t.tm_hour = lexical_cast<int>(wf.substr(8, 2));
		t.tm_mday = lexical_cast<int>(wf.substr(6, 2));
		t.tm_mon = lexical_cast<int>(wf.substr(4, 2));
		t.tm_year = lexical_cast<int>(wf.substr(0, 4)) - 1900;
	} catch (bad_lexical_cast&) {
		return 0;
	}
	t.tm_yday = t.tm_wday = t.tm_isdst = 0;
	return mktime(&t);
}

std::string
fmtuptime(void)
{
	return tdiff2(boottime);
}
	
std::string
tdiff2(std::time_t then, bool shrt)
{
	long uptime = std::time(0) - then;
	int secs = 0, mins = 0, hours = 0, days = 0, weeks = 0;
#define WEEK (60 * 60 * 24 * 7)
#define DAY (60 * 60 * 24)
#define HOUR (60 * 60)
#define MIN (60)
	
	while (uptime > WEEK) {
		++weeks;
		uptime -= WEEK;
	}
	while (uptime > DAY) {
		++days;
		uptime -= DAY;
	}
	while (uptime > HOUR) {
		++hours;
		uptime -= HOUR;
	}
	while (uptime > MIN) {
		++mins;
		uptime -= MIN;
	}
	secs = uptime;
	if (!shrt)
		return b::io::str(format("%d weeks, %d days, %d hours, %d minutes, %d seconds")
				  % weeks % days % hours % mins % secs);
	std::string s;
	if (weeks) s += b::io::str(format("%dw") % weeks);
	if (days)  s += b::io::str(format("%dd") % days);
	if (hours) s += b::io::str(format("%dh") % hours);
	if (mins)  s += b::io::str(format("%dm") % mins);
	if (secs)  s += b::io::str(format("%ds") % secs);
	if (s.empty()) s = "-";
	return s;
}

std::string
fmtboottime(void)
{
	char buf[256];
	struct tm now;
	gmtime_r(&boottime, &now);
	strftime(buf, sizeof buf, "%d-%b-%Y %H:%M:%S", &now);
	return buf;
}

std::string
fmttime(std::time_t n)
{
	if (n == 0)
		n = std::time(0);
	char buf[256];
	struct tm now;
	gmtime_r(&n, &now);
	strftime(buf, sizeof buf, "%d-%b-%Y %H:%M:%S", &now);
	return buf;
}


} // namespace smutl
