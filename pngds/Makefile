CC		= cc
CFLAGS		= -g -lz
PROG		= pngds
SRCS		= pngreader.c pngcmd.c pngresize.c
OBJS		= $(SRCS:.c=.o)

all: pngreader pngresize

pngreader:
	$(CC) $(CFLAGS) $(SRCS) -o pngreader -DPNGREADER
pngresize:
	$(CC) $(CFLAGS) $(SRCS) -o pngresize -DPNGRESIZE

clean:
	rm -rf pngreader pngresize
