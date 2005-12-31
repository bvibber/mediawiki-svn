#CFLAGS+=-g -Wall -I/sw/include/
#LDFLAGS+=-ldb -L/sw/lib/

LDFLAGS+=-ldb
CFLAGS+=-Wall -g

all: collector exporter

collector: collector.h collector.c export.c

exporter: export.c collector.h exporter.c

#export: collector.h export.c

clean:
	rm -f collector export
