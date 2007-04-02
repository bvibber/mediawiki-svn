CC 		= cc
CXX     	= CC
PLAT_CPPFLAGS	= -DSOLARIS
PLAT_CXXFLAGS   = -mt -xO3 -g -library=stlport4
PLAT_LIBS       = -lrt -lsocket -lnsl

ARCH=$(shell uname -i)

ifeq ($(ARCH),i86pc)
PLAT_CXXFLAGS += -xtarget=opteron -xarch=amd64
else
PLAT_CXXFLAGS += -xarch=generic64
endif

