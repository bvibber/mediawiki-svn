/* @(#) $Header$ */
#ifndef SM_SMSTDINC_HXX_INCLUDED_
#define SM_SMSTDINC_HXX_INCLUDED_

#define SM_VERSION "2.1.0.0-pre"

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
using std::for_each;

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
namespace b = boost;
namespace bl = boost::lambda;

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

#endif
