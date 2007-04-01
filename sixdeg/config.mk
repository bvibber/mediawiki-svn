# $Id$
# Six degrees of Wikipedia: build configuration.
#
# If necessary, edit this file to match your system.
#
CC		= gcc
CXX		= g++
CXXFLAGS	= -W -Wall -Werror -g3 -O2
CPPFLAGS	= 
PICFLAGS	= -fPIC

JAVA_HOME	= /usr/lib/j2sdk1.5-sun
JAVAH		= $(JAVA_HOME)/bin/javah
JAVAC		= $(JAVA_HOME)/bin/javac

.cc.o:
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) -c $<
