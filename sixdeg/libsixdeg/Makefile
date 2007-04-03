# Six degrees of Wikipedia: Makefile
# This source code is released into the public domain.
#
# $Id$

include ../rules.mk

LIBRARY	= libsixdeg.a
SRCS	= encode_decode.cc log.cc
OBJS	= $(SRCS:.cc=.o)

include ../frag/cxxlib.mk

encode_decode.cc: encode_decode.h
#DO NOT DELETE -- make depend needs it
encode_decode.o: encode_decode.cc encode_decode.h
log.o: log.cc log.h
