/* @(#) $Header$ */
#ifndef SM_SMTMR_HXX_INCLUDED_
#define SM_SMTMR_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

/* timer interfaces */
namespace smtmr {

class evthdlr;

class evt {
public:
	typedef b::function<void()> cbtype;

	evt(str name_, time_t time_, bool rep_, cbtype cb_)
	: fname(name_)
	, frep(rep_)
	, ftime(time_)
	, cb(cb_)
	{}

	str name() { return fname; }
	time_t time() { return ftime; }
	bool rep() { return frep; }

private:
	friend class evthdlr;

	str	fname;
	bool	frep;
	time_t	ftime;
	time_t	next;
	cbtype 	cb;
};

typedef b::shared_ptr<evt> evtp;

class evthdlr : public smutl::singleton<evthdlr> {
public:
	void install(evtp e);
	time_t run_pend();

private:
	std::list<evtp> evts;
};

} // namespace smtmr

#endif
