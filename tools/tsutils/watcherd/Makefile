PROG		= watcherd
BINDIR		= /usr/local/sbin

LIBTSUTILS	= YES
LIBS		= -lmysqlclient -lz
INCLUDES	= -I/usr/include/mysql

SRCS		= watcherd.c
OBJS		= $(SRCS:.c=.o)

include ../mk/prog.mk
