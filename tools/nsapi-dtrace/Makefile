CC		= cc
CFLAGS		= -xO3 -g -fPIC -DPIC -mt
CPPFLAGS	= -I /opt/webserver7/include
LDFLAGS		= -G
LIBS		= 

all: dtrace.so

dtrace.o: dtrace.c
	$(CC) $(CPPFLAGS) $(CFLAGS) -c $<

sjsws.o: dtrace.o sjsws.d
	dtrace -G -s sjsws.d dtrace.o

dtrace.so: dtrace.o sjsws.o
	$(CC) $(CFLAGS) $(LDFLAGS) dtrace.o sjsws.o -o $@ $(LIBS) 

clean:
	rm -f dtrace.o dtrace.so sjsws.o
