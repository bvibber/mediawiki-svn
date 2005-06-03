# @(#) $Header$

trickle: trickle.o tar.o proto.o util.o rdcp.o ldflags
	cc -g trickle.o tar.o proto.o util.o rdcp.o -o $@ `cat ldflags`
%.o: %.c Makefile $< t_stdint.h
	cc -g -D_FILE_OFFSET_BITS=64 -c $<
clean:
	rm -f trickle trickle.o tar.o util.o rdcp.o proto.o ldflags t_stdint.h
t_stdint.h:
	rm -f t_stdint.h
	touch t_stdint.h
	echo "#include <stdint.h>" >test.c
	-cc -c -o /dev/null test.c && echo "#define T_STDINT 1" >>t_stdint.h
	rm -f test.c
ldflags:
	rm -f ldflags
	touch ldflags
	echo "main(){}" > test.c
	-cc test.c -lsocket -lnsl -o /dev/null && echo -lsocket -lnsl >>ldflags
	rm -f test.c

.KEEP_STATE:
