#ifndef SM_SMCSMPLEX_HXX_INCLUDED_
#define SM_SMCSMPLEX_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smthr.hxx"
#include "smnet.hxx"

namespace csmplex {

class csmplexd : public smthr::daemon {
public:
	csmplexd(void);
	void start(void);
	void newc(smnet::inetlsnrp, int);

private:
};

} // namespace csmplex

#endif
