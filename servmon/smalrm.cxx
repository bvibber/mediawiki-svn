/* @(#) $Header$ */
#include "smalrm.hxx"
#include "smcfg.hxx"
#include "smirc.hxx"

namespace smalrm {

mgr::mgr(void)
{
	SMI(smtmr::evthdlr)->install(smtmr::evtp(new smtmr::evt("alarm monitor", 60 * 60, true, boost::bind(&mgr::evt_remind, this))));
}
	
void
mgr::set_thresh(str metric, int thresh, int type)
{
	SMI(smcfg::cfg)->storeint("/metrics/"+metric+"/thresh", thresh);
	SMI(smcfg::cfg)->storeint("/metrics/"+metric+"/type", type);
}

int
mgr::get_type(str metric)
{
	try {
		return SMI(smcfg::cfg)->fetchint("/metrics/"+metric+"/type");
	} catch (smcfg::nokey&) {
		return type_preferhigher;
	}
}
	
pair<int, int>
mgr::get_thresh(str metric)
{
	try {
		int low;
		int high = SMI(smcfg::cfg)->fetchint("/metrics/"+metric+"/thresh");
		try {
			low = SMI(smcfg::cfg)->fetchint("/metrics/"+metric+"/low");
		} catch (smcfg::nokey&) {
			if (get_type(metric) == type_preferhigher) {
				low = int(high * 0.75);
			} else {
				low = int(high / 0.75);
			}
		}
		return make_pair(high, low != -1 ? low : int(high * 0.75));
	} catch (smcfg::nokey&) {
		return make_pair(0, 0);
	}
}
	
void
mgr::value(str host, str metric, int value)
{
	int hthresh, lthresh;
	tie(hthresh, lthresh) = get_thresh(metric);
	int type = get_type(metric);
	
	if (!hthresh) return;
	if ((type == type_preferhigher && value > hthresh)
	    || (type == type_preferlower && value < hthresh)) {
		if (hasalarm(host, metric))
			return;
		alarmup(host, metric, value);
	} else if ((type == type_preferhigher && value < lthresh)
		   || (type == type_preferlower && value > lthresh)) {
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

void
mgr::alarmup(str host, str metric, int value)
{
	hosts[host][metric] = value;
	SMI(smlog::log)->logmsg(10, SM$FAC_MONIT, SM$MSG_ALRMSET, host, metric, value);
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
	SMI(smlog::log)->logmsg(10, SM$FAC_MONIT, SM$MSG_ALRMCLR, host, metric, value);
}

void
mgr::evt_remind(void)
{
	if (hosts.empty())
		return;
	std::string summ;
	for(std::map<std::string, std::map<std::string, int> >::const_iterator it = hosts.begin(),
		    end = hosts.end(); it != end; ++it) {
		if (it->second.empty())
			continue;
		summ += it->first + ": ";
		for(std::map<std::string, int>::const_iterator jt = it->second.begin(),
			    jnd = it->second.end(); jt != jnd; ++jt) {
			summ += b::io::str(format("%s = %s ") % jt->first % jt->second);
		}
	}
	SMI(smlog::log)->logmsg(10, SM$FAC_MONIT, SM$MSG_ALRMSUMM, summ);
}

} // namespace smalrm
