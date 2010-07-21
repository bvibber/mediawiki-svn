CC		= cc
CFLAGS		= -g -mt -xc99=none -D_FILE_OFFSET_BITS=64
LDFLAGS		=
MYSQL_CFLAGS:sh	= mysql_config --include
MYSQL_LIBS:sh	= mysql_config --libs_r
INCLUDES	=
LIBS		= -lrt -ldoor
PROG		= trainwreck
SRCS		= 	\
	trainwreck.c	\
	config.c	\

OBJS		= $(SRCS:.c=.o)

all: $(PROG) twctl

$(PROG): $(OBJS)
	$(CC) $(CFLAGS) $(LDFLAGS) $(OBJS) -o $(PROG) $(MYSQL_LIBS) -lrt -ldoor

twctl: twctl.o config.o
	$(CC) $(CFLAGS) $(CPPFLAGS) twctl.o config.o -o twctl -ldoor

.c.o:
	$(CC) $(CFLAGS) $(CPPFLAGS) $(MYSQL_CFLAGS) -c $<

lint:
	lint $(INCLUDES) $(MYSQL_CFLAGS) -axsm -u -errtags=yes -s -Xc99=%none -Xarch=amd64 -errsecurity=core -erroff=E_INCONS_ARG_DECL2 -erroff=E_CONSTANT_CONDITION $(SRCS)

clean:
	rm -f trainwreck twctl $(OBJS) twctl.o

.PHONY: clean install lint
.KEEP_STATE:
