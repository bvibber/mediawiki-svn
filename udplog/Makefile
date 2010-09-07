TARGETS = log2udp udprecv delta udp2log/udp2log packet-loss
HOST_OBJS = srcmisc/host.o srclib/HostEntry.o srclib/IPAddress.o
LOG2UDP_OBJS = srcmisc/log2udp.o srclib/HostEntry.o srclib/IPAddress.o srclib/Socket.o srclib/SocketAddress.o
UDPRECV_OBJS = srcmisc/udprecv.o srclib/IPAddress.o srclib/Socket.o srclib/SocketAddress.o
UDP2LOG_OBJS = udp2log/udp2log.o udp2log/LogProcessor.o udp2log/Udp2LogConfig.o srclib/IPAddress.o srclib/Socket.o srclib/SocketAddress.o
CFLAGS:=$(CFLAGS) -Wall

all: $(TARGETS)

clean:
	rm -f $(HOST_OBJS) $(LOG2UDP_OBJS) $(UDPRECV_OBJS) $(UDP2LOG_OBJS) $(TARGETS)

%.o : %.cpp
	g++ -c $(CFLAGS) -o $@ $<

host: $(HOST_OBJS)
	g++ $(CFLAGS) $(HOST_OBJS) -o host

log2udp: $(LOG2UDP_OBJS)
	g++ $(CFLAGS) -lboost_program_options $(LOG2UDP_OBJS) -o log2udp

udprecv: $(UDPRECV_OBJS)
	g++ $(CFLAGS) -Wall $(UDPRECV_OBJS) -o udprecv

delta: srcmisc/delta.cpp
	g++ $(CFLAGS) -o delta srcmisc/delta.cpp

packet-loss: srcmisc/packet-loss.cpp
	g++ $(CFLAGS) -o packet-loss srcmisc/packet-loss.cpp

udp2log/udp2log: $(UDP2LOG_OBJS)
	g++ $(CFLAGS) -o udp2log/udp2log -lboost_program_options $(UDP2LOG_OBJS) 

install:
	install log2udp $(DESTDIR)/usr/bin/log2udp
	install udp2log/udp2log $(DESTDIR)/usr/bin/udp2log
