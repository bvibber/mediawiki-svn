PLAT_CPPFLAGS   = -D_REENTRANT -DSOLARIS
PLAT_CXXFLAGS   = -O2 -g -W -Wall -Wno-non-virtual-dtor -m64
PLAT_LIBS       = -lrt -lsocket -lnsl
CC		= gcc
CXX		= g++
