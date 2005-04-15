bin_PROGRAMS = willow
selectsrc=wnet_@SELECTTYPE@.c
willow_SOURCES = willow.c wlog.c wconfig.c wnet.c whttp.c wbackend.c ${selectsrc}

# set the include path found by configure
INCLUDES= $(all_includes)

# the library search path.
willow_LDFLAGS = $(all_libraries) 
noinst_HEADERS = wlog.h wconfig.h wnet.h whttp.h wbackend.h willow.h
