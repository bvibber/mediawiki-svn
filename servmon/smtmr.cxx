#include "smtmr.hxx"

void smtmr::evthdlr::install(evtp e) {
	e->next = e->ftime;
	evts.push_back(e);
}

time_t smtmr::evthdlr::run_pend() {
	time_t now = std::time(0), next = 0;
	FE_TC_AS(std::list<evtp>, evts, i) {
		if (next)
			next = std::min(next, now + (*i)->ftime);
		else	next = now + (*i)->ftime;
		if ((*i)->next > now) continue;
		(*i)->cb();
		if (!((*i)->frep))
			i = evts.erase(i);
		else
			(*i)->next = now + (*i)->ftime;
	}
	return next;
}
