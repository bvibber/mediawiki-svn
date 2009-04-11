#include <tunables/global>

/usr/bin/memcached {

#include <abstractions/base>
#include <abstractions/nameservice>

capability net_bind_service,
capability setuid,
capability setgid,

/usr/bin/memcached rix,

}
