all: log2udp

clean:
	rm -f *.o log2udp host

HOST_OBJS = host.o HostEntry.o IPAddress.o
LOG2UDP_OBJS = log2udp.o HostEntry.o IPAddress.o Socket.o SocketAddress.o

%.o : %.cpp
	g++ -c -ggdb3 -Wall $<

host: $(HOST_OBJS)
	g++ -ggdb3 -Wall $(HOST_OBJS) -o host

log2udp: $(LOG2UDP_OBJS)
	g++ -ggdb3 -Wall -lboost_program_options $(LOG2UDP_OBJS) -o log2udp

