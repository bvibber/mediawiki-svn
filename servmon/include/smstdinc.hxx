/* @(#) $Header$ */
#ifndef SM_SMSTDINC_HXX_INCLUDED_
#define SM_SMSTDINC_HXX_INCLUDED_

#ifdef SM_COMPILER_SHIMS_ONLY
# define SM_NO_HEADERS
#endif

#define SM_VERSION "2.1.0.0-pre"

#define SM_RELCVS     1 /* CVS version */
#define SM_RELBRANCH  2 /* Release branch */
#define SM_RELRELEASE 3 /* Release */

#define SM_RELTYPE SM_RELCVS

#ifndef SM_NO_HEADERS
#include <iostream>
#include <fstream>
#include <iomanip>
#include <string>
#include <map>
#include <vector>
#include <utility>
#include <functional>
#include <set>
#include <list>
#include <cerrno>
#include <cctype>
#include <algorithm>
#ifndef SM_NO_USING
using std::pair;
using std::make_pair;
using std::for_each;
#endif

#include <boost/bind.hpp>
#include <boost/function.hpp>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>
#include <boost/utility.hpp>
#include <boost/shared_ptr.hpp>
#include <boost/lambda/lambda.hpp>
#include <boost/thread.hpp>
#include <boost/thread/mutex.hpp>
#include <boost/any.hpp>
#include <boost/regex.hpp>
#ifndef SM_NO_USING
using boost::regex;
using boost::regex_search;
using boost::lexical_cast;
using boost::bad_lexical_cast;
using boost::format;
using boost::noncopyable;
using boost::shared_ptr;
using boost::lambda::var;
using boost::static_pointer_cast;
using boost::dynamic_pointer_cast;
using boost::tie;
namespace b = boost;
namespace bl = boost::lambda;
#endif

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/stat.h>
#include <sys/utsname.h>
#include <sys/un.h>

#include <arpa/inet.h>

#include <netinet/in.h>

#include <unistd.h>
#include <netdb.h>
#include <fcntl.h>
#endif /* !SM_NO_HEADERS */

/* Fix Solaris brokenness */
#ifndef SUN_LEN
# define SUN_LEN(su) \
	(sizeof(*(su)) - sizeof((su)->sun_path) + strlen((su)->sun_path))
#endif

#if defined(__GNUC__) || defined(__INTEL_COMPILER)
# define SM_NORETURN __attribute__((__noreturn__))
#else
# define SM_NORETURN 
#endif

#endif
