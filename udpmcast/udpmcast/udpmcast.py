#!/usr/bin/python
#
# udpcast.py
# application level udp multicaster/multiplexer
# Written on 2005/04/03 by Mark Bergsma <mark@nedworks.org>
#
# $Id$

import socket, getopt, sys, os

debugging = False

def debug(msg):
    global debugging
    if debugging:
        print msg;

def multicast_diagrams(sock, addresses):
    portnr = sock.getsockname()[1];

    while 1:
        diagram = sock.recv(1500)
        if not diagram: break
        for addr in addresses:
            try:
                sock.sendto(diagram, 0, (addr, portnr))
                debug('Sent diagram to '+addr+' port '+portnr)
            except socket.error:
                debug('Error while sending diagram to '+addr)
                pass

def join_multicast_group(sock, multicast_group):
    import struct

    ip_mreq = struct.pack('!4sl', socket.inet_aton(multicast_group),
        socket.INADDR_ANY)
    sock.setsockopt(socket.IPPROTO_IP,
                    socket.IP_ADD_MEMBERSHIP,
                    ip_mreq)

def print_help():
    print 'Usage:\n\tudpmcast [ options ] { addresses }\n'
    print 'Options:'
    print '\t-d\tFork into the background (become a daemon)'
    print '\t-p {portnr}\tUDP port number to listen on (default is 4827)'
    print '\t-j {multicast address}\tMulticast group to join on startup'
    print '\t-v\tBe more verbose'

if __name__ == '__main__':
    host = ''
    portnr = 4827
    multicast_group = None
    daemon = False
    opts = 'dhj:p:v'

    # Parse options
    options, arguments = getopt.getopt(sys.argv[1:], opts)
    if len(arguments) == 0:
        print_help()
        sys.exit()
    else:
        for option, value in options:
            if option == '-j':
                multicast_group = value
            elif option == '-p':
                portnr = int(value)
            elif option == '-h':
                print_help()
                sys.exit()
            elif option == '-d':
                daemon = True
            elif option == '-v':
                debugging = True

    try:
        # Open the UDP socket
        sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        sock.bind((host, portnr))

        # Join a multicast group if requested
        if multicast_group != None:
            debug('Joining multicast group ' + multicast_group)
            join_multicast_group(sock, multicast_group)

        # Become a daemon
        if daemon and os.fork():
            sys.exit()        

        # Multiplex everything that comes in
        multicast_diagrams(sock, arguments)
    except socket.error, msg:
        print msg[1];
    except KeyboardInterrupt:
        pass

