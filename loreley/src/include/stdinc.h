/*
 * System includes.
 */

#ifndef STDINC_H
#define STDINC_H

#include "autoconf.h"

#ifndef _GNU_SOURCE
# define _GNU_SOURCE	/* glibc strptime */
#endif

#include <algorithm>
#include <cassert>
#include <cctype>
#include <cerrno>
#include <cmath>
#include <cstdarg>
#include <cstddef>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <deque>
#include <exception>
#include <fstream>
#include <ios>
#include <iostream>
#include <iterator>
#include <limits>
#include <map>
#include <set>
#include <sstream>
#include <stack>
#include <stdexcept>
#include <cstdlib>
#include <csignal>
#include <string>
#include <typeinfo>
#include <utility>
#include <vector>

#include <boost/algorithm/string.hpp>
#include <boost/archive/iterators/base64_from_binary.hpp>
#include <boost/archive/iterators/binary_from_base64.hpp>
#include <boost/archive/iterators/transform_width.hpp>
#include <boost/assign/list_of.hpp>
#include <boost/bind.hpp>
#include <boost/format.hpp>
#include <boost/function.hpp>
#include <boost/lexical_cast.hpp>
#include <boost/mpl/at.hpp>
#include <boost/mpl/equal.hpp>
#include <boost/mpl/find.hpp>
#include <boost/mpl/has_key.hpp>
#include <boost/mpl/int.hpp>
#include <boost/mpl/map.hpp>
#include <boost/mpl/minus.hpp>
#include <boost/mpl/pair.hpp>
#include <boost/mpl/placeholders.hpp>
#include <boost/mpl/plus.hpp>
#include <boost/mpl/set.hpp>
#include <boost/mpl/transform.hpp>
#include <boost/mpl/vector.hpp>
#include <boost/mpl/vector_c.hpp>
#include <boost/multi_index_container.hpp>
#include <boost/multi_index/member.hpp>
#include <boost/multi_index/ordered_index.hpp>
#include <boost/noncopyable.hpp>
#include <boost/shared_ptr.hpp>
#include <boost/spirit.hpp>
#include <boost/static_assert.hpp>
#include <boost/type_traits.hpp>
#include <boost/utility.hpp>
#include <boost/variant.hpp>

#include <sys/types.h>
#include <sys/fcntl.h>
#include <sys/mman.h>
#include <sys/socket.h>
#include <sys/stat.h>
#include <sys/time.h>
#include <sys/un.h>
#include <sys/utsname.h>

#ifdef HAVE_SYS_SENDFILE_H
# include <sys/sendfile.h>
#endif

#include <arpa/inet.h>
#include <netinet/in.h>

#include <unistd.h>
#include <netdb.h>
#include <pthread.h>
#include <inttypes.h>
#include <syslog.h>
#include <signal.h>	/* pthread_kill */
#include <pwd.h>
#include <grp.h>

#include <db.h>

#ifdef __INTEL_COMPILERx
# pragma hdrstop
#endif

#endif	/* STDINC_H */
