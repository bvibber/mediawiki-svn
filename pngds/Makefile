CC		= cc
CFLAGS		= -g -lz
PROG		= pngds
SRCS		= pngreader.c pngcmd.c
OBJS		= $(SRCS:.c=.o)

all: pngreader

pngreader:
	$(CC) $(CFLAGS) $(SRCS) -o pngreader -DPNGREADER

clean:
	rm -rf pngreader
