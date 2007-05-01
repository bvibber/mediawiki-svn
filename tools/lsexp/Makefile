lsexp: lsexp.c
	gcc -W -Wall -O lsexp.c -o lsexp
install:
	install -c -m 755 -o root -g bin lsexp /usr/local/sbin
clean:
	rm -f lsexp
