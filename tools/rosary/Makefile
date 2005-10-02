default: all
all: rosary
rosary.o: rosary.c
	cc `pkg-config --cflags glib-2.0` -g -c rosary.c -o $@
rosary: rosary.o
	cc -g rosary.o -o rosary `pkg-config --libs glib-2.0` -lcurses -ltecla
clean:
	rm -f rosary rosary.o

.KEEP_STATE:
