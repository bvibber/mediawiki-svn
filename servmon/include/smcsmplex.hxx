#ifndef SM_SMCSMPLEX_HXX_INCLUDED_
#define SM_SMCSMPLEX_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smthr.hxx"

namespace csmplex {

class csmplexd : public smthr::daemon {
public:
	csmplexd(void);
	void start(void);

private:
};

} // namespace csmplex

#endif
