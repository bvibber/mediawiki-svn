setmail: setmail.c
	cc setmail.c -o setmail -lldap -lreadline

install:
	install -o root -g root -o 0755 -d /usr/local/bin
	install -o root -g root -o 0755 setmail /usr/local/bin
