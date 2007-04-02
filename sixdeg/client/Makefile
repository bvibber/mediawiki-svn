# $Id$
#
# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.

include ../rules.mk

PROGRAM	= linksc
LDFLAGS	+= -L../libsixdeg
LIBS	+=  -lsixdeg
SRCS	= linksc.cc liblinksc.cc
OBJS	= $(SRCS:.cc=.o)

include ../frag/cxxprog.mk
