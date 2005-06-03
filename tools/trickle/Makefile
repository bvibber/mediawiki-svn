# @(#) $Header$

trickle: trickle.o tar.o proto.o util.o rdcp.o
	cc -g trickle.o tar.o proto.o util.o rdcp.o -o $@ -lsocket -lnsl
.c.o: Makefile $<
	cc -g -D_FILE_OFFSET_BITS=64 -c $<
clean:
	rm -f trickle trickle.o tar.o util.o rdcp.o proto.o
.KEEP_STATE:
