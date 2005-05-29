# @(#) $Header$

trickle: trickle.o tar.o
	cc trickle.o tar.o -o $@
.c.o:
	cc -O -D_FILE_OFFSET_BITS=64 -c $<
clean:
	rm -f trickle trickle.o tar.o
.KEEP_STATE:
