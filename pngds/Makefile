CC              = cc
CFLAGS          = -g -lz
PROG            = pngds
SRCS            = pngreader.c pngcmd.c
OBJS            = $(SRCS:.c=.o)

all: $(PROG)

$(PROG):
        $(CC) $(CFLAGS) $(OBJS) -o $(PROG) -DPNGREADER
