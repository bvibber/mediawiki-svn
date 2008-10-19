# @(#) $Header$

CC?=cc
CFGHDRS=t_stdint.h t_xti.h t_alloca.h t_sysparam.h
all: config trickle
config: $(CFGHDRS)
trickle: trickle.o tar.o proto.o util.o rdcp.o ldflags
	$(CC) -O -g trickle.o tar.o proto.o util.o rdcp.o -o $@ `cat ldflags`
.c.o:
	$(CC) -O -g -D_FILE_OFFSET_BITS=64 -c $<
clean:
	rm -f trickle trickle.o tar.o util.o rdcp.o proto.o ldflags $(CFGHDRS)
t_sysparam.h:
	echo "#include <sys/param.h>" >test.c
	-($(CC) -c -o _conftest test.c && echo "#define T_SYS_PARAM_H 1") >t_sysparam.h
	rm -f test.c _conftest
t_stdint.h:
	echo "#include <stdint.h>" >test.c
	-($(CC) -c -o _conftest test.c && echo "#define T_STDINT 1") >t_stdint.h
	rm -f test.c _conftest
t_xti.h:
	echo "#include <xti.h>" >test.c
	-($(CC) -c -o _conftest test.c && echo "#define T_XTI 1") >t_xti.h
	rm -f test.c _conftest
t_alloca.h:
	echo "#include <alloca.h>" >test.c
	-($(CC) -c -o _conftest test.c && echo "#define T_ALLOCA 1") >t_alloca.h
	rm -f test.c _conftest
ldflags:
	echo "int main() { return 0; }" > test.c
	-($(CC) test.c -lsocket -lnsl -o _conftest && echo -lsocket -lnsl) >ldflags
	-($(CC) test.c -lxti `cat ldflags` -o _conftest && echo -lxti) >>ldflags
	rm -f test.c _conftest
dist:
	mkdir trickle-cvs1
	cp trickle.[ch] tar.c proto.c util.c rdcp.[ch] Makefile trickle-cvs1/
	tar cvf trickle-cvs1.tar trickle-cvs1
	rm -rf trickle-cvs1
	compress -f trickle-cvs1.tar
	ls -l trickle-cvs1.tar.Z

.KEEP_STATE:
