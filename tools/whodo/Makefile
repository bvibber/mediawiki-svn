whodo: whodo.c
	gcc -W -Wall -O -g whodo.c -o whodo 
clean:
	rm -f whodo
install:
	install -d -m 755 /usr/local/bin
	install -m 755 -o root -g bin whodo /usr/local/bin
