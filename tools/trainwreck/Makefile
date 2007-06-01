CC		= cc
CFLAGS		= -g -O -mt -xarch=amd64 -xc99=none
LDFLAGS		=
INCLUDES	= -I/usr/local/mysql/include
LIBS		= -L/usr/local/mysql/lib -R/usr/local/mysql/lib -lmysqlclient_r -lrt
PROG		= trainwreck
 SRCS		= 	\
	trainwreck.c	\

OBJS		= $(SRCS:.c=.o)

$(PROG): $(OBJS)
	$(CC) $(CFLAGS) $(LDFLAGS) $(OBJS) -o $(PROG) $(LIBS)

.c.o:
	$(CC) $(CFLAGS) $(CPPFLAGS) $(INCLUDES) -c $<

lint:
	lint $(INCLUDES) -axsm -u -errtags=yes -s -Xc99=%none -Xarch=amd64 -errsecurity=core -erroff=E_INCONS_ARG_DECL2 $(SRCS)

clean:
	rm -f trainwreck $(OBJS)

.PHONY: clean install lint
.KEEP_STATE:
