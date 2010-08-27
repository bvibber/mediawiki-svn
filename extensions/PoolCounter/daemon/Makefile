CC=gcc
DEFINES=-DENDIAN_BIG=0 -DENDIAN_LITTLE=1 -DHAVE_ACCEPT4=1
CFLAGS=-std=c90 -Wall $(DEFINES)
OBJS=main.o client_data.o locks.o hash.o
LINK=-levent
HEADERS=prototypes.h client_data.h

poolcounterd: $(OBJS)
	$(CC) $(LINK) $^ -o $@

%.o: %.c $(HEADERS)
	$(CC) -c $(CFLAGS) $< -o $@

prototypes.h: main.c
	sed -n 's/\/\* prototype \*\//;/p' $^ > $@

clean:
	rm -f *.o prototypes.h
