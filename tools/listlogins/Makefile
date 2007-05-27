listlogins: listlogins.c
	gcc -W -Wall -O -g listlogins.c -o listlogins 
clean:
	rm -f listlogins
install:
	install -d -m 755 /usr/local/bin
	install -m 755 -o root -g bin listlogins /usr/local/bin
