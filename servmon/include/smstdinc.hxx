#ifndef SM_SMSTDINC_HXX_INCLUDED_
#define SM_SMSTDINC_HXX_INCLUDED_

#include <iostream>
#include <fstream>
#include <iomanip>
#include <string>
#include <map>
#include <vector>
#include <utility>
#include <functional>
#include <set>
#include <cerrno>
using std::for_each;

#include <boost/bind.hpp>
#include <boost/function.hpp>
#include <boost/lexical_cast.hpp>
#include <boost/format.hpp>
#include <boost/utility.hpp>
#include <boost/shared_ptr.hpp>
using boost::lexical_cast;
using boost::format;
using boost::noncopyable;
using boost::shared_ptr;

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/stat.h>
#include <fcntl.h>

#include <arpa/inet.h>

#include <netinet/in.h>

#include <unistd.h>

#endif
