#ifndef SM_SMUTL_HXX_INCLUDED_
#define SM_SMUTL_HXX_INCLUDED_

#include "smutl.hxx"

namespace smutl {

template<class T>
class singleton {
public:
	static T* instance(void) {
		return mT ? mT : (mT = new T);
	}
private:
	static T* mT;
};

template<class T>
T* singleton<T>::mT;

} // namespace smutl

template<class T>
T* instance(void) {
	return T::instance();
}
#endif
