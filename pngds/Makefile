CC		= gcc
CFLAGS		= -lz
SRCS		= pngreader.c pngcmd.c pngresize.c pngutil.c pngwriter.c
OBJS		= $(SRCS:.c=.o)

all: pngds

pngreader: $(SRCS)
	$(CC) $(CFLAGS) $(SRCS) -o pngreader -DPNGREADER
pngresize: $(SRCS)
	$(CC) $(CFLAGS) $(SRCS) -o pngresize -DPNGRESIZE
pngds: $(SRCS)
	$(CC) $(CFLAGS) $(SRCS) -o pngds -DPNGDS

clean:
	rm -rf pngreader pngresize pngds
