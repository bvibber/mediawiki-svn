#ifndef SM_SMUTL_HXX_INCLUDED_
#define SM_SMUTL_HXX_INCLUDED_

#include "smutl.hxx"

namespace smutl {

template<class T>
class singleton : noncopyable {
public:	
	static T* instance(void) {
		return mT ? mT : (mT = new T);
	}
private:
	static T* mT;
};

template<class T>
T* singleton<T>::mT = 0;

std::string car(std::string&);

} // namespace smutl

template<class T>
T* instance(void) {
	return T::instance();
}
#define SMI(x) ::instance< x >()

typedef std::string const& str;
typedef std::string& strr;

#endif
