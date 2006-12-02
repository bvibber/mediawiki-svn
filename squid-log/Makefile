all: log2udp udprecv

clean:
	rm -f *.o log2udp host

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

