JAVA_HOME	= /usr/java
JAVAC		= $(JAVA_HOME)/bin/javac

include ../plat_frag.mk
include ../local.mk

CXXFLAGS	= $(PLAT_CXXFLAGS) $(USER_CXXFLAGS)
CPPFLAGS	= $(PLAT_CPPFLAGS) $(USER_CPPFLAGS) -I.. -I../libsixdeg -I../client
LDFLAGS		= $(PLAT_LDFLAGS) $(USER_LDFLAGS) -L../libsigdeg
LIBS		= $(PLAT_LIBS) $(USER_LIBS)

.cc.o:
	@echo "	(compile) $<"
	@$(CXX) $(CXXFLAGS) $(CPPFLAGS) -c $<

.SUFFIXES: .cc .o .java .class .jar .h 
