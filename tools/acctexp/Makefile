acctexp: acctexp.c
	gcc -W -Wall -O -g acctexp.c -o acctexp 
clean:
	rm -f acctexp
install:
	install -d -m 755 /usr/local/bin
	install -m 755 -o root -g bin acctexp /usr/local/bin
