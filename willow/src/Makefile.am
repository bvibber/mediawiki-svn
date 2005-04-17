bin_PROGRAMS = willow
selectsrc=wnet_@SELECTTYPE@.c
willow_CPPFLAGS = -DDATADIR=\"${datadir}\"
willow_SOURCES = willow.c wlog.c wconfig.c wnet.c whttp.c wbackend.c ${selectsrc}
EXTRA_DIST=wnet_poll.c wnet_ports.c wnet_kqueue.c wnet_epoll.c wnet_devpoll.c

# set the include path found by configure
INCLUDES= $(all_includes)

# the library search path.
willow_LDFLAGS = $(all_libraries) 
noinst_HEADERS = wlog.h wconfig.h wnet.h whttp.h wbackend.h willow.h
