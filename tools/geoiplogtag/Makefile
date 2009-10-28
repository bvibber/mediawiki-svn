SHELL = /bin/sh
.SUFFIXES: .c .o
TARGETS = geoiplogtag

%.o:	%.c
		$(CC) -c -o $@ $<

geoiplogtag:	geoiplogtag.o
				$(CC) -o $@ -lGeoIP $<

.PHONY: clean

clean:
		rm -f *.o $(TARGETS)

all:	$(TARGETS)		