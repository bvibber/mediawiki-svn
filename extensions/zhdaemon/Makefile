OBJS = convert.o dict.o segment.o ttree.o zhdaemon.o
all: zhdaemon

zhdaemon: $(OBJS)
	gcc -o zhdaemon $(OBJS) -lconfuse

%.o: %.c
	gcc -Wall -g -c $<

clean:
	rm -f zhdaemon *.o *~ core core.*
