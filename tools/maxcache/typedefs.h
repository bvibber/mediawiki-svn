#ifndef MAXCACHE_TYPEDEFS_H
#define MAXCACHE_TYPEDEFS_H

#include <string>
#include <boost/shared_ptr.hpp>
#include <boost/date_time/posix_time/posix_time_types.hpp>
namespace MaxCache {
	typedef boost::shared_ptr<std::string> StringPtr;
	typedef boost::posix_time::ptime Time;
}

#endif
