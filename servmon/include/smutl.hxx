/* @(#) $Header$ */
#ifndef SM_SMUTL_HXX_INCLUDED_
#define SM_SMUTL_HXX_INCLUDED_

typedef std::string const& str;
typedef std::string& strr;

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
std::string lower(std::string s);

std::vector<std::string> snarf(str);
std::time_t wf2time_t(str wf);
} // namespace smutl

template<class T>
T* sminstance(void) {
	return T::instance();
}
#define SMI(x) ::sminstance< x >()

#define FE_TC_AS(T, c, i) for(T::iterator i = c.begin(), i ## _end = c.end(); i != i ## _end; ++i)

#endif
