/* @(#) $Header$ */
#ifndef SM_SMALRM_HXX_INCLUDED_
#define SM_SMALRM_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

namespace smalrm {

static const int
	 type_preferhigher = 1
	,type_preferlower  = 2
	;
	
class mgr : public smutl::singleton<mgr> {
public:
	void set_thresh(str metric, int thresh, int type = type_preferhigher);
	void set_lowthresh(str metrix, int thresh);
	pair<int, int> get_thresh(str metric);
	int get_type(str metric);
	
	void value(str host, str metric, int value);

	bool hasalarm(str host, str metric);

	void alarmup(str host, str metric, int value);
	void alarmdown(str host, str metric, int value);
	
private:
	std::map<std::string, std::map<std::string, int> > hosts;
};
	
} // namespace smalrm

#endif
