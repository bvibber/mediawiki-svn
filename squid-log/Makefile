all: log2udp udprecv delta

clean:
	rm -f *.o log2udp host udprecv delta

HOST_OBJS = host.o HostEntry.o IPAddress.o
LOG2UDP_OBJS = log2udp.o HostEntry.o IPAddress.o Socket.o SocketAddress.o
UDPRECV_OBJS = udprecv.o IPAddress.o Socket.o SocketAddress.o
CFLAGS=-Wall

%.o : %.cpp
	g++ -c $(CFLAGS) $<

host: $(HOST_OBJS)
	g++ $(CFLAGS) $(HOST_OBJS) -o host

log2udp: $(LOG2UDP_OBJS)
	g++ $(CFLAGS) -lboost_program_options $(LOG2UDP_OBJS) -o log2udp

udprecv: $(UDPRECV_OBJS)
	g++ $(CFLAGS) -Wall $(UDPRECV_OBJS) -o udprecv

delta: delta.cpp
	g++ $(CFLAGS) -o delta delta.cpp
