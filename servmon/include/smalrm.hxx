/* @(#) $Header$ */
#ifndef SM_SMALRM_HXX_INCLUDED_
#define SM_SMALRM_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smalrm {

class mgr : public smutl::singleton<mgr> {
public:
	void set_thresh(str metric, int thresh);
	int get_thresh(str metric);
	void value(str host, str metric, int value);

	bool hasalarm(str host, str metric);

	static int low(int value);
	void alarmup(str host, str metric, int value);
	void alarmdown(str host, str metric, int value);
	
private:
	std::map<std::string, std::map<std::string, int> > hosts;
};
	
} // namespace smalrm

#endif
