#include "smstdinc.hxx"
#include "smthr.hxx"

namespace smthr {

namespace {
template<class T>
void* apply(void* f)
{
	T* fp = static_cast<T*>(f);
	(*fp)();
	delete fp;
	return NULL;
}
	
} // anon namespace

void thrmgr::wait(void)
{
	for_each(mthrds.begin(), mthrds.end(), 
		bind(&pthread_join, _1, static_cast<void**>(NULL)));
}

void thrmgr::crethr(boost::function<void()> f)
{
	pthread_t tid;
	boost::function<void()> *fp = new boost::function<void()>(f);
	int i = pthread_create(&tid, NULL, apply<boost::function<void()> >, 
			static_cast<void*>(fp));
	if (i < 0) {
		delete fp;
		throw thr_creation_error(errno);
	}
	mthrds.insert(tid);
}

thrbase::thrbase(void)
{
}

void thrbase::run(void)
{
	instance<thrmgr>()->crethr(boost::bind(&daemon::start, this));
}

mtx::mtx(void)
{
	pthread_mutex_init(&mmtx, NULL);
}

lck::lck(mtx& m)
: mymtx(m)
{
	pthread_mutex_lock(&mymtx.mmtx);
}

lck::~lck(void)
{
	pthread_mutex_unlock(&mymtx.mmtx);
}

}
