CC		= cc
CFLAGS		= -g -mt -xarch=amd64 -xc99=none
LDFLAGS		=
MYSQL_INCLUDES	= -I/usr/local/mysql/include
MYSQL_LIBS	= -L/usr/local/mysql/lib -R/usr/local/mysql/lib -lmysqlclient_r 
INCLUDES	=
LIBS		= -lrt -ldoor
PROG		= trainwreck
 SRCS		= 	\
	trainwreck.c	\

OBJS		= $(SRCS:.c=.o)

all: $(PROG) twctl

$(PROG): $(OBJS)
	$(CC) $(CFLAGS) $(LDFLAGS) $(OBJS) -o $(PROG) $(MYSQL_LIBS) -lrt -ldoor

twctl: twctl.c
	$(CC) $(CFLAGS) $(CPPFLAGS) twctl.c -o twctl -ldoor

.c.o:
	$(CC) $(CFLAGS) $(CPPFLAGS) $(MYSQL_INCLUDES) -c $<

lint:
	lint $(INCLUDES) -axsm -u -errtags=yes -s -Xc99=%none -Xarch=amd64 -errsecurity=core -erroff=E_INCONS_ARG_DECL2 $(SRCS)

clean:
	rm -f trainwreck $(OBJS)

.PHONY: clean install lint
.KEEP_STATE:
