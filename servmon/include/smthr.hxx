#ifndef SM_SMTHR_HXX_INCLUDED_
#define SM_SMTHR_HXX_INCLUDED_

#include "smstdinc.hxx"
#include "smutl.hxx"

#include <pthread.h>

namespace smthr {

class lck;

class mtx {
public:
	mtx();
private:
	pthread_mutex_t mmtx;
	friend class lck;
};

class lck {
public:
	lck(mtx&);
	~lck();
private:
	mtx& mymtx;
};

struct thr_creation_error : public std::runtime_error {
	thr_creation_error(int en) : std::runtime_error(std::strerror(en)) {}
};

class thrmgr : public smutl::singleton<thrmgr> {
public:
	void wait(void);
	void crethr(boost::function<void ()>);

private:
	std::set<pthread_t> mthrds;
};

class thrbase {
public:
	thrbase();
	virtual void run(void);
	virtual void start(void) = 0;
};

class daemon : public thrbase {
public:
};

} // namespace smthr

#endif
