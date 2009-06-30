PREFIX=

all: malloc-fail.so

malloc-fail.o: malloc-fail.c
	gcc -fPIC -g -c -Wall $<

malloc-fail.so: malloc-fail.o
	gcc -shared -o libmalloc-fail.so $< -lc

install:
	install -m644 libmalloc-fail.so $(PREFIX)/lib/libmalloc-fail.so

test: all
	gcc test.c -o test
	! LD_PRELOAD=./libmalloc-fail.so ./test malloc
	! LD_PRELOAD=./libmalloc-fail.so ./test calloc
	! LD_PRELOAD=./libmalloc-fail.so ./test realloc

.PHONY: test
