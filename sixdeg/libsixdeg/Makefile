# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id$

include ../rules.mk

LIBRARY	= libsixdeg.a
SRCS	= encode_decode.cc
OBJS	= $(SRCS:.cc=.o)

include ../frag/cxxlib.mk

encode_decode.cc: encode_decode.h
