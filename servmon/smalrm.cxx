/* @(#) $Header$ */
#include "smalrm.hxx"
#include "smcfg.hxx"
#include "smirc.hxx"

namespace smalrm {

void
mgr::set_thresh(str metric, int thresh)
{
	SMI(smcfg::cfg)->storeint("/metrics/"+metric+"/thresh", thresh);
}

int
mgr::get_thresh(str metric)
{
	try {
		return SMI(smcfg::cfg)->fetchint("/metrics/"+metric+"/thresh");
	} catch (smcfg::nokey&) {
		return 0;
	}
}
	
void
mgr::value(str host, str metric, int value)
{
	int thresh = get_thresh(metric);
	if (!thresh) return;
	if (value > thresh) {
		if (hasalarm(host, metric))
			return;
		alarmup(host, metric, value);
	} else if (value < low(thresh)) {
		if (!hasalarm(host, metric))
			return;
		alarmdown(host, metric, value);
	}
	return;
}

bool
mgr::hasalarm(str host, str metric)
{
	if (hosts.find(host) == hosts.end())
		return false;
	if (hosts[host].find(metric) == hosts[host].end())
		return false;
	return true;
}

int
mgr::low(int value)
{
	return int(value * 0.75);
}

void
mgr::alarmup(str host, str metric, int value)
{
	hosts[host][metric] = value;
	SMI(smlog::log)->logmsg(10, b::io::str(b::format("Alarm condition \002set\002 for host \002%s\002: %s = %d")
					       % host % metric % value));
}

void
mgr::alarmdown(str host, str metric, int value)
{
	std::map<std::string, std::map<std::string, int> >::iterator hit = hosts.find(host);
	if (hit == hosts.end()) return;
	std::map<std::string, int>::iterator mit = hit->second.find(metric);
	if (mit == hit->second.end()) return;
	hit->second.erase(mit);
	if (hit->second.begin() == hit->second.end())
		hosts.erase(hit);
	SMI(smlog::log)->logmsg(10, b::io::str(b::format("Alarm condition \002cleared\002 for host \002%s\002: %s = %d")
						% host % metric % value));
}
	
} // namespace smalrm
